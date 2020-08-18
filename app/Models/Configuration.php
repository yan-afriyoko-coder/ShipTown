<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static Configuration firstOrNew(array $array)
 * @method static Configuration where(string $string, $key)
 * @method firstOrFail()
 * @property mixed value
 */
class Configuration extends Model
{
    protected $fillable = [
        'key', 'value',
    ];
}
