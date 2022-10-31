<?php

namespace App\Modules\MagentoApi\src\Models;

use App\BaseModel;
use Exception;
use Illuminate\Support\Facades\Crypt;

/**
 * @property integer $magento_store_id
 */
class MagentoConnection extends BaseModel
{
    protected $table = 'modules_magento2api_connections';

    protected $fillable = [
        'base_url',
        'magento_store_id',
        'inventory_source_warehouse_tag_id',
        'access_token'
    ];

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
