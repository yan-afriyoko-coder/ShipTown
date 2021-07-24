<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\MailTemplates\Models\MailTemplate;

class CreateMailTemplatesTable extends Migration
{
    public function up()
    {
        Schema::create('mail_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mailable');
            $table->text('subject')->nullable();
            $table->longtext('html_template');
            $table->longtext('text_template')->nullable();
            $table->timestamps();
        });

        MailTemplate::create([
            'mailable' => \App\Mail\ShipmentConfirmationMail::class,
            'subject' => 'Guineys.ie - Your Order has been Shipped!',
            'html_template' => '
<html>
    <body bgcolor="#f8f8f8" style="text-align: center; align-content: center; padding: 10px;">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 20px; padding: 10px; border-radius: 10px;">
            <tr>
                <td align="center">
                    <div style="max-width: 550px; background: white"">
                        <div style="margin-top: 10px; margin-bottom: 10px;">
                            <img src="https://guineys.ie/static/version1626453051/frontend/Bointsoft/Guineys/en_IE/Magento_Email/logo_email.png" alt="logo">
                        </div>
                        <p style="font-size: 12pt">We shipped your order!</p>
                        <p>
                            <hr>
                            <table>
                                {{#variables.shipments}}
                                <tr>
                                    <td>{{ carrier }}</td>
                                    <td><a href="{{ tracking_url }}">{{ shipping_number }}</a></td>
                                </tr>
                                {{/variables.shipments}}
                            </table>
                            <hr>
                        </p>

                        <p>Thank you for your order from Guineys.ie.</p>
                        </p>
                        <p> Your order/part of your order has now been shipped. In some cases part of your order may be shipped separately from another location and you will receive a separate shipment notice for the balance of your order. Please note that any tracking information/link below will not update until this evening.</a>
                        </p>
                        <p> If you have any questions, please feel free to contact us at
                            <a href="mailto:info@guineys.ie?subject=Order Enquiry">Michael Guineys</a> or call us on +353 (01) 6522902 Monday - Friday, 9am - 5.30pm
                        </p>
                        </p>
                        <p>
                            Thank you again for your business.
                        </p>
                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>
    '
        ]);
    }
}
