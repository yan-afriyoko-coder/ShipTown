<?php

namespace App\Modules\MagentoApi\src\Models;

use App\BaseModel;
use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class MagentoConnection extends BaseModel
{
    protected $table = 'modules_magento2api_connections';

    protected $fillable = [
        'base_url',
        'inventory_source_warehouse_tag_id',
        'access_token'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getAccessTokenAttribute(): string
    {
        try {
            return Crypt::decryptString($this->attributes['access_token_encrypted']);
        } catch (Exception $exception) {
            return '';
        }
    }

    public function setAccessTokenAttribute($value)
    {
        $this->attributes['access_token_encrypted'] = Crypt::encryptString($value);
    }
}
