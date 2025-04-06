<div class="flex space-x-2">
    <button class="edit-quantity-btn bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded-md text-xs"
            data-item-id="{{ $cart->item_id }}" data-quantity="{{ $cart->quantity }}">
        <i class="fas fa-edit"></i>
    </button>
    <button class="remove-item-btn bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded-md text-xs"
            data-item-id="{{ $cart->item_id }}">
        <i class="fas fa-trash-alt"></i>
    </button>
</div> 