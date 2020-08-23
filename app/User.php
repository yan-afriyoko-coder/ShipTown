<?php

namespace App;

use App\Services\PrintService;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use phpseclib\Math\BigInteger;
use PrintNode\Response;
use Spatie\Permission\Traits\HasRoles;
use Thomasjohnkane\Snooze\Traits\SnoozeNotifiable;

/**
 * @property BigInteger printer_id
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;
    use SnoozeNotifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Sends a new print job request to the service from a PDF source.
     *
     * @param string $title A title to give the print job.
     *   This is the name which will appear in the operating system's print queue.
     * @param string $content The path to the pdf file, a pdf string
     *
     * @return Response
     */
    public function newPdfPrintJob($title, $content)
    {
        return app(PrintService::class)->newPdfPrintJob($this->printer_id, $title, $content);
    }
}
