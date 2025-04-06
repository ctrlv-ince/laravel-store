<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Group;
use App\Models\Inventory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                DB::beginTransaction();
                
                // Create the item
                $item = Item::create([
                    'item_name' => $row['product_name'],
                    'item_description' => $row['description'],
                    'price' => $row['price'],
                ]);
                
                // Set category if provided
                if (!empty($row['category'])) {
                    // Find or create the category
                    $group = Group::firstOrCreate(
                        ['group_name' => $row['category']],
                        ['group_description' => '']
                    );
                    
                    // Associate the item with the group
                    $item->groups()->attach($group->group_id);
                }
                
                // Create inventory
                Inventory::create([
                    'item_id' => $item->item_id,
                    'quantity' => $row['quantity'] ?? 0
                ]);
                
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error importing product: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'product_name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'category' => 'nullable|string|max:255',
        ];
    }
} 