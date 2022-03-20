<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodeToPreviousMailTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\MailTemplate::query()->where([
            'mailable' => \App\Mail\ShipmentConfirmationMail::class
        ])->update(['code' => 'module_shipment_confirmation']);

        \App\Models\MailTemplate::query()->where([
            'mailable' => \App\Mail\OversoldProductMail::class
        ])->update(['code' => 'module_oversold_product_mail']);
    }
}
