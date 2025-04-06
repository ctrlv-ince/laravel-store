<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Dompdf\Dompdf;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with(['account.user', 'orderInfos.item'])->select('orders.*');
            
            return DataTables::of($orders)
                ->addColumn('customer', function ($order) {
                    if ($order->account && $order->account->user) {
                        return $order->account->user->first_name . ' ' . $order->account->user->last_name;
                    }
                    return 'Unknown';
                })
                ->addColumn('items', function ($order) {
                    $items = $order->orderInfos->count();
                    return $items . ' item(s)';
                })
                ->addColumn('date', function ($order) {
                    if ($order->date_ordered) {
                        if ($order->date_ordered instanceof \DateTime || $order->date_ordered instanceof \Carbon\Carbon) {
                            return $order->date_ordered->format('M d, Y H:i');
                        }
                        return $order->date_ordered;
                    }
                    return 'N/A';
                })
                ->addColumn('status_badge', function ($order) {
                    $status = $order->status ?? 'unknown';
                    $class = '';
                    
                    switch (strtolower($status)) {
                        case 'pending':
                            $class = 'bg-yellow-500';
                            break;
                        case 'processing':
                            $class = 'bg-blue-500';
                            break;
                        case 'completed':
                            $class = 'bg-green-500';
                            break;
                        case 'cancelled':
                            $class = 'bg-red-500';
                            break;
                        case 'refunded':
                            $class = 'bg-purple-500';
                            break;
                        default:
                            $class = 'bg-gray-500';
                    }
                    
                    return '<span class="' . $class . ' text-white text-xs px-2 py-1 rounded-full">' . ucfirst($status) . '</span>';
                })
                ->addColumn('actions', function ($order) {
                    $viewBtn = '<a href="' . route('admin.orders.show', $order->order_id) . '" class="btn btn-sm btn-info mr-1">View</a>';
                    $editBtn = '<a href="' . route('admin.orders.edit', $order->order_id) . '" class="btn btn-sm btn-primary mr-1">Edit</a>';
                    
                    return $viewBtn . $editBtn;
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }
        
        return view('admin.orders.index');
    }

    /**
     * Display the specified order.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $order = Order::with(['account.user', 'orderInfos.item.images'])->findOrFail($id);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $order = Order::with(['account.user', 'orderInfos.item'])->findOrFail($id);
        $statuses = ['pending', 'processing', 'completed', 'cancelled', 'refunded'];
        
        return view('admin.orders.edit', compact('order', 'statuses'));
    }

    /**
     * Update the specified order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,processing,completed,cancelled,refunded',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            $order = Order::with(['account.user'])->findOrFail($id);
            $oldStatus = $order->status;
            $newStatus = $request->input('status');
            
            // Only update and send email if status has changed
            if ($oldStatus !== $newStatus) {
                $order->status = $newStatus;
                $order->save();
                
                // Send email notification about status change
                $this->sendOrderStatusUpdateEmail($order);
                
                return redirect()->route('admin.orders.index')
                    ->with('success', 'Order status updated successfully and notification email sent.');
            } else {
                return redirect()->route('admin.orders.index')
                    ->with('info', 'No changes made to order status.');
            }
            
        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred while updating the order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Send order status update email to the customer.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    protected function sendOrderStatusUpdateEmail($order)
    {
        try {
            $order->load(['account.user', 'orderInfos.item']);
            
            // Generate PDF receipt
            $dompdf = new Dompdf();
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->setOptions(new \Dompdf\Options([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
                'isPhpEnabled' => true,
            ]));
            
            // Generate PDF content
            $pdfContent = view('orders.receipt', compact('order'))->render();
            $dompdf->loadHtml($pdfContent);
            $dompdf->render();
            
            // Generate a temp filename
            $filename = 'order-' . $order->order_id . '-receipt-' . time() . '.pdf';
            $tempPath = storage_path('app/public/temp/' . $filename);
            
            // Ensure directory exists
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }
            
            // Save PDF to temp file
            file_put_contents($tempPath, $dompdf->output());
            
            // Send email with attachment
            Mail::send('emails.order-status-update', ['order' => $order], function($message) use ($order, $tempPath, $filename) {
                $message->to($order->account->user->email)
                       ->subject('Order Status Update - #' . $order->order_id)
                       ->attach($tempPath, [
                            'as' => $filename,
                            'mime' => 'application/pdf',
                       ]);
            });
            
            // Delete temp file after sending
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            
            Log::info('Order status update email sent successfully for order #' . $order->order_id);
        } catch (\Exception $e) {
            Log::error('Failed to send order status update email: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
} 