<?php

namespace App\Modules\MagentoApi\src\Models;

use App\BaseModel;
use App\Traits\HasTagsTrait;
use Illuminate\Support\Facades\Crypt;

/**
 * @property string $base_url
 * @property string $access_token
 * @property integer $magento_store_id
 * @property integer inventory_source_warehouse_tag_id
 */
class MagentoConnection extends BaseModel
{
    use HasTagsTrait;

    protected $table = 'modules_magento2api_connections';

    protected $fillable = [
        'base_url',
        'access_token_encrypted',
        'magento_store_id',
        'inventory_source_warehouse_tag_id',
        'pricing_source_warehouse_id',
    ];

    public function getAccessTokenEncryptedAttribute(): string
    {
        return Crypt::decryptString($this->attributes['access_token_encrypted']);
    }

    public function setAccessTokenEncryptedAttribute($value)
    {
        $this->attributes['access_token_encrypted'] = Crypt::encryptString($value);
    }
}
