<div class="flex space-x-2">
    <a href="{{ route('orders.show', $order) }}" 
       class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-lg transition-colors duration-200"
       title="View Details">
        <i class="fas fa-eye"></i>
    </a>
    
    <a href="{{ route('orders.generate-receipt', $order) }}" 
       class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-lg transition-colors duration-200"
       title="Download Receipt">
        <i class="fas fa-file-pdf"></i>
    </a>

    @if(Auth::user()->account->isAdmin())
    <button type="button" 
            class="bg-purple-500 hover:bg-purple-600 text-white p-2 rounded-lg transition-colors duration-200"
            onclick="updateStatus({{ $order->order_id }})"
            title="Update Status">
        <i class="fas fa-cog"></i>
    </button>
    @endif
</div>

@if(Auth::user()->account->isAdmin())
<div id="status-modal-{{ $order->order_id }}" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-gray-800 rounded-lg p-6 max-w-md w-full">
            <h3 class="text-xl font-bold text-blue-400 mb-4">Update Order Status</h3>
            <form action="{{ route('orders.update-status', $order) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full bg-gray-700 text-white rounded-lg p-2">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="for_confirm" {{ $order->status == 'for_confirm' ? 'selected' : '' }}>For Confirmation</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg"
                            onclick="closeStatusModal({{ $order->order_id }})">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateStatus(orderId) {
    document.getElementById(`status-modal-${orderId}`).classList.remove('hidden');
}

function closeStatusModal(orderId) {
    document.getElementById(`status-modal-${orderId}`).classList.add('hidden');
}
</script>
@endpush
@endif 