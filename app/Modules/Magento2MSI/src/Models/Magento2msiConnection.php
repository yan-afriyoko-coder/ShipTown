<?php

namespace App\Modules\Magento2MSI\src\Models;

use App\BaseModel;
use App\Models\Warehouse;
use App\Traits\HasTagsTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\QueryBuilder\QueryBuilder;

class Magento2msiConnection extends BaseModel
{
    use HasTagsTrait;

    protected $table = 'modules_magento2msi_connections';

    protected $fillable = [
        'base_url',
        'api_access_token',
        'store_code',
        'inventory_source_warehouse_tag_id',
    ];

    protected $casts = [
        'api_access_token' => 'encrypted',
    ];

    protected $hidden = [
        'api_access_token'
    ];

    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(self::class)
            ->allowedIncludes([
                'tags','warehouse'
            ]);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'pricing_source_warehouse_id', 'id');
    }
}
