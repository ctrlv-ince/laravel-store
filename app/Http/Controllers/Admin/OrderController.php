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
use Illuminate\Support\Facades\DB;

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
                        case 'shipped':
                            $class = 'bg-blue-500';
                            break;
                        case 'for_confirm':
                            $class = 'bg-purple-500';
                            break;
                        case 'completed':
                            $class = 'bg-green-500';
                            break;
                        case 'cancelled':
                            $class = 'bg-red-500';
                            break;
                        default:
                            $class = 'bg-gray-500';
                    }
                    
                    return '<span class="' . $class . ' text-white text-xs px-2 py-1 rounded-full">' . ucfirst(str_replace('_', ' ', $status)) . '</span>';
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
        $statuses = ['pending', 'shipped', 'for_confirm', 'completed', 'cancelled'];
        
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
        Log::info('Order status update initiated', [
            'order_id' => $id,
            'requested_status' => $request->input('status')
        ]);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,shipped,for_confirm,completed,cancelled',
        ]);
        
        if ($validator->fails()) {
            Log::error('Order status validation failed', [
                'order_id' => $id,
                'errors' => $validator->errors()->toArray()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            $order = Order::with(['account.user'])->findOrFail($id);
            $oldStatus = $order->status;
            $newStatus = $request->input('status');
            
            Log::info('Order found, preparing to update status', [
                'order_id' => $id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);
            
            // Only update and send email if status has changed
            if ($oldStatus !== $newStatus) {
                DB::beginTransaction();
                try {
                    $order->status = $newStatus;
                    $order->save();
                    
                    Log::info('Order status updated in database', [
                        'order_id' => $id,
                        'new_status' => $order->status,
                        'saved' => $order->wasChanged('status')
                    ]);
                    
                    // Send email notification about status change
                    $this->sendOrderStatusUpdateEmail($order);
                    
                    DB::commit();
                    
                    Log::info('Order status update completed successfully', [
                        'order_id' => $id,
                        'final_status' => $order->status
                    ]);

                    if ($request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Order status updated successfully and notification email sent.',
                            'order' => [
                                'id' => $order->order_id,
                                'status' => $order->status
                            ]
                        ]);
                    }
                    
                    return redirect()->route('admin.orders.index')
                        ->with('success', 'Order status updated successfully and notification email sent.');
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            } else {
                Log::info('No status change needed', [
                    'order_id' => $id,
                    'status' => $oldStatus
                ]);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'No changes made to order status.',
                        'order' => [
                            'id' => $order->order_id,
                            'status' => $order->status
                        ]
                    ]);
                }
                
                return redirect()->route('admin.orders.index')
                    ->with('info', 'No changes made to order status.');
            }
            
        } catch (\Exception $e) {
            Log::error('Error updating order status', [
                'order_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the order: ' . $e->getMessage()
                ], 500);
            }
            
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
            Log::info('Starting to send order status update email', [
                'order_id' => $order->order_id,
                'status' => $order->status,
                'customer_email' => $order->account->user->email ?? 'not available'
            ]);

            $order->load(['account.user', 'orderInfos.item']);
            
            // Ensure date_ordered is a Carbon instance
            if (!($order->date_ordered instanceof \Carbon\Carbon)) {
                $order->date_ordered = \Carbon\Carbon::parse($order->date_ordered);
            }
            
            // Generate PDF receipt
            $dompdf = new Dompdf();
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->setOptions(new \Dompdf\Options([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
                'isPhpEnabled' => true,
            ]));
            
            Log::info('Generating PDF receipt');
            
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
                Log::info('Created temp directory for PDF', ['path' => dirname($tempPath)]);
            }
            
            // Save PDF to temp file
            file_put_contents($tempPath, $dompdf->output());
            Log::info('PDF generated and saved', ['path' => $tempPath]);
            
            // Send email with attachment
            Mail::send('emails.order-status-update', ['order' => $order], function($message) use ($order, $tempPath, $filename) {
                $message->to($order->account->user->email)
                       ->subject('Order Status Update - #' . $order->order_id)
                       ->attach($tempPath, [
                            'as' => $filename,
                            'mime' => 'application/pdf',
                       ]);
            });
            
            Log::info('Email sent successfully');
            
            // Delete temp file after sending
            if (file_exists($tempPath)) {
                unlink($tempPath);
                Log::info('Temp PDF file deleted', ['path' => $tempPath]);
            }
            
            Log::info('Order status update email sent successfully', ['order_id' => $order->order_id]);
        } catch (\Exception $e) {
            Log::error('Failed to send order status update email', [
                'order_id' => $order->order_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw the exception to be handled by the caller
        }
    }
} 