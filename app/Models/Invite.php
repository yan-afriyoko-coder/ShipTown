<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Invite
 *
 * @property int $id
 * @property string $email
 * @property string $token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Invite newModelQuery()
 * @method static Builder|Invite newQuery()
 * @method static Builder|Invite query()
 * @method static Builder|Invite whereCreatedAt($value)
 * @method static Builder|Invite whereEmail($value)
 * @method static Builder|Invite whereId($value)
 * @method static Builder|Invite whereToken($value)
 * @method static Builder|Invite whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Invite extends Model
{
    protected $fillable = [
        'email', 'token',
    ];
}
