<?php

namespace App\Imports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ItemsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $item = Item::create([
            'item_name' => $row['item_name'],
            'price' => $row['price'],
            'item_description' => $row['item_description'],
            'date_added' => now(),
        ]);

        // Create inventory record
        $item->inventory()->create([
            'quantity' => $row['quantity'] ?? 0
        ]);

        // Attach groups if provided
        if (!empty($row['groups'])) {
            $groupIds = explode(',', $row['groups']);
            $item->groups()->attach($groupIds);
        }

        return $item;
    }

    public function rules(): array
    {
        return [
            'item_name' => 'required|unique:items,item_name|max:100',
            'price' => 'required|numeric|min:0',
            'item_description' => 'required',
            'quantity' => 'nullable|integer|min:0',
            'groups' => 'nullable|string'
        ];
    }
}
