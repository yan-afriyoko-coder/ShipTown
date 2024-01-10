<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CsvImportController extends Controller
{
    /**
     * @var string[][]
     */
    private array $rules = [
        'data_collection_id' => ['required', 'exists:data_collections,id'],
        'data.*.product_sku' => ['required_if:product_id,null', 'string'],
        'data.*.product_id' => ['required_if:product_sku,null', 'integer'],
        'data.*.quantity_requested' => ['nullable', 'numeric'],
        'data.*.quantity_scanned' => ['nullable', 'numeric'],
    ];

    public function store(Request $request): JsonResource
    {
        $validatedData = Validator::make($request->all(), $this->rules)->validate();

        $tempTableName = 'temp_csv_import_' . rand(100000000000000000, 999999999999999999);

        Schema::create($tempTableName, function (Blueprint $table) {
            $table->temporary();
            $table->id();
            $table->string('product_sku')->nullable();
            $table->foreignId('product_id')->nullable();
            $table->double('quantity_requested')->nullable();
            $table->double('quantity_scanned')->nullable();
            $table->double('quantity_to_scan')->nullable();
            $table->timestamps();
        });

        DB::table($tempTableName)->insert($validatedData['data']);

        ray(DB::table($tempTableName)->get());

        DB::statement('
            UPDATE ' . $tempTableName . '
            LEFT JOIN products_aliases ON ' . $tempTableName . '.product_sku = products_aliases.alias
            SET ' . $tempTableName . '.product_id = products_aliases.product_id
            WHERE ' . $tempTableName . '.product_id IS NULL
        ');

        $skuNotFoundErrors = DB::table($tempTableName)
            ->whereNull('product_id')
            ->select('product_sku')
            ->get()
            ->map(function ($item) use (&$errors) {
                return 'SKU not found: ' . $item->product_sku;
            })
            ->filter();

        if ($skuNotFoundErrors->isNotEmpty()) {
            throw ValidationException::withMessages($skuNotFoundErrors->toArray());
        }

        DB::statement('
            INSERT INTO data_collection_records (
                data_collection_id,
                inventory_id,
                product_id,
                quantity_requested,
                quantity_scanned,
                created_at,
                updated_at
            )
            SELECT '. $validatedData['data_collection_id'] .',
                inventory.id,
                tempTable.product_id,
                tempTable.quantity_requested,
                IFNULL(tempTable.quantity_scanned, 0),
                NOW(),
                NOW()

            FROM ' . $tempTableName . ' as tempTable
            LEFT JOIN data_collections
                ON data_collections.id = '. $validatedData['data_collection_id'] .'
            LEFT JOIN inventory
                ON inventory.product_id = tempTable.product_id
                AND inventory.warehouse_id = data_collections.warehouse_id
        ');

        return JsonResource::make(['success' => true]);
    }
}
