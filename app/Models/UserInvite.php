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
 * @method static Builder|UserInvite newModelQuery()
 * @method static Builder|UserInvite newQuery()
 * @method static Builder|UserInvite query()
 * @method static Builder|UserInvite whereCreatedAt($value)
 * @method static Builder|UserInvite whereEmail($value)
 * @method static Builder|UserInvite whereId($value)
 * @method static Builder|UserInvite whereToken($value)
 * @method static Builder|UserInvite whereUpdatedAt($value)
 * @mixin Eloquent
 */
class UserInvite extends Model
{
    protected $fillable = [
        'email', 'token',
    ];
}
