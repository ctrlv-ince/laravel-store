<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Group;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class ItemsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        try {
            DB::beginTransaction();

            // Create the item
            $item = Item::create([
                'item_name' => $row['item_name'],
                'item_description' => $row['item_description'],
                'price' => $row['price'],
            ]);

            // Create inventory record
            Inventory::create([
                'item_id' => $item->item_id,
                'quantity' => $row['quantity'] ?? 0
            ]);

            // Attach groups if provided
            if (!empty($row['groups'])) {
                $groupNames = explode(',', $row['groups']);
                foreach ($groupNames as $groupName) {
                    $group = Group::firstOrCreate(
                        ['group_name' => trim($groupName)],
                        ['group_description' => '']
                    );
                    $item->groups()->attach($group->group_id);
                }
            }

            DB::commit();
            return $item;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error importing item', [
                'row_data' => $row,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            'item_name' => 'required|string|max:255',
            'item_description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'groups' => 'nullable|string'
        ];
    }

    public function customValidationMessages()
    {
        return [
            'item_name.required' => 'Item name is required',
            'item_name.max' => 'Item name cannot exceed 255 characters',
            'item_description.required' => 'Description is required',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
            'price.min' => 'Price must be 0 or higher',
            'quantity.integer' => 'Quantity must be a whole number',
            'quantity.min' => 'Quantity must be 0 or higher'
        ];
    }
}
