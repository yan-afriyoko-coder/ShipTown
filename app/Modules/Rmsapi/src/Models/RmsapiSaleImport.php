<?php

namespace App\Modules\Rmsapi\src\Models;

use App\BaseModel;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * App\Models\RmsapiProductImport.
 *
 * @property int         $id
 * @property Carbon|null $reserved_at
 * @property int         $connection_id
 * @property Carbon|null $when_processed
 * @property int|null    $product_id
 * @property string|null $sku
 * @property array       $raw_import
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $comment
 *
 * @mixin Eloquent
 */
class RmsapiSaleImport extends BaseModel
{
    use HasFactory;

    protected $table = 'modules_rmsapi_sales_imports';

    protected $fillable = [
        'connection_id',
        'reserved_at',
        'when_processed',
        'sku',
        'price',
        'quantity',
        'transaction_time',
        'transaction_number',
        'transaction_entry_id',
        'comment',
        'raw_import',
    ];

    protected $casts = [
        'raw_import' => 'array',
    ];

    // we use attributes to set default values
    // we won't use database default values
    // as this is then not populated
    // correctly to events
    protected $attributes = [
        'raw_import' => '{}',
    ];
}
