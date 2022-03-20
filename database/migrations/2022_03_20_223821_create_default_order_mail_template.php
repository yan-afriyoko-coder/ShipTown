<?php

use App\Models\MailTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefaultOrderMailTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MailTemplate::firstOrCreate(['code' => 'shipment_confirmation'], [
            'mailable' => \App\Mail\OrderMail::class,
            'subject' => 'Your Order #{{ variables.order.order_number }} has been Shipped!',
            'html_template' => '
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml"
          xmlns="http://www.w3.org/1999/xhtml"
          style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
    <head>
        <meta name="viewport" content="width=device-width" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>We shipped your order!</title>

        <style>img {
            max-width: 100%;
        }
        body {
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
            width: 100% !important;
            height: 100%;
            line-height: 1.6;
        }
        body {
            background-color: #f6f6f6;
        }
        @media only screen and (max-width: 640px) {
            h1 {
                font-weight: 600 !important; margin: 20px 0 5px !important;
            }
            h2 {
                font-weight: 600 !important; margin: 20px 0 5px !important;
            }
            h3 {
                font-weight: 600 !important; margin: 20px 0 5px !important;
            }
            h4 {
                font-weight: 600 !important; margin: 20px 0 5px !important;
            }
            h1 {
                font-size: 22px !important;
            }
            h2 {
                font-size: 18px !important;
            }
            h3 {
                font-size: 16px !important;
            }
            .container {
                width: 100% !important;
            }
            .content {
                padding: 10px !important;
            }
            .content-wrapper {
                padding: 10px !important;
            }
            .invoice {
                width: 100% !important;
            }
        }
        </style></head>

    <body style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6; margin: 0; padding: 0;" bgcolor="#f6f6f6">

    <table class="body-wrap" style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; margin: 0; padding: 0;" bgcolor="#f6f6f6">
        <tr style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
            <td style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;" valign="top"></td>
            <td class="container" width="600" style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto; padding: 0;" valign="top">
                <div class="content" style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
                    <table class="main" width="100%" cellpadding="0" cellspacing="0" style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; margin: 0; padding: 0; border: 1px solid #e9e9e9;" bgcolor="#fff">
                        <tr style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
                            <td class="content-wrap aligncenter" style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 20px;" align="center" valign="top">
                                <table width="100%" cellpadding="0" cellspacing="0" style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
                                    <!-------- logo -------->
    <!--                                <tr style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">-->
    <!--                                    <td class="content-block" style="text-align: center; align-content: center; font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0 0 20px;" valign="top">-->
    <!--                                        <a href="" target="_blank">-->
    <!--                                            <img src="" alt="logo">-->
    <!--                                        </a>-->
    <!--                                    </td>-->
    <!--                                </tr>-->
                                    <!-------- logo -------->
                                    <tr style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
                                        <td class="content-block" style="text-align: center; align-content: center; font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0 0 20px;" valign="top">
                                            <h2 style="font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, &quot;Lucida Grande&quot;, sans-serif; box-sizing: border-box; font-size: 24px; color: #000; line-height: 1.2; font-weight: 400; margin: 40px 0 0; padding: 0;">
                                                We shipped your order!<br>
                                                #{{ variables.order.order_number }}
                                            </h2>
                                        </td>
                                    </tr>
                                    <tr style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
                                        <td class="content-block" style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0 0 20px;" valign="top">
                                            <table class="invoice" style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; text-align: left; width: 80%; margin: 40px auto; padding: 0;">
                                                <tr style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
                                                    <td style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 5px 0;" valign="top">
                                                        Tracking Information:<br style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;" />
                                                    </td>
                                                </tr>
                                                <tr style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
                                                    <td style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 5px 0;" valign="top">
                                                        <table class="invoice-items" cellpadding="0" cellspacing="0" style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; margin: 0; padding: 0;">

                                                            {{#variables.shipments}}

                                                            <tr style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
                                                                <td style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; border-top-width: 1px; border-top-color: #eee; border-top-style: solid; margin: 0; padding: 5px 0;" valign="top">
                                                                    {{ carrier }}
                                                                </td>
                                                                <td class="alignright" style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; border-top-width: 1px; border-top-color: #eee; border-top-style: solid; margin: 0; padding: 5px 0;" align="right" valign="top">
                                                                    <a href="{{ tracking_url }}" target="_blank">
                                                                        {{ shipping_number }}
                                                                    </a>
                                                                </td>
                                                            </tr>

                                                            {{/variables.shipments}}
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
                                        <td class="content-block" style="text-align: center; align-content: center; font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0 0 20px;" valign="top">
                                            <p>
                                                Please note that any tracking information above <br>
                                                may not update until this evening.
                                            </p>
                                            <p>
                                                If you have any questions, please feel free <br>
                                                to email us at <a href="mailto:support@ship.town?subject=Order #{{ variables.order.order_number }} Enquiry">no-reply@products.management</a></br>
                                                or call us on <b>+353 (1) 1234567</b><br>
                                            </p>
                                            <p>Thank you again for your business.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <div class="footer" style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 20px;">
                        <table width="100%" style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
                            <tr style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;">
                                <td class="aligncenter content-block" style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 12px; margin: 0; padding: 0 0 20px;" align="center" valign="top">
                                    Questions? Email
                                    <a href="mailto:" style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 12px; color: #999; text-decoration: underline; margin: 0; padding: 0;">
                                        no-reply@products.management
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div></div>
            </td>
            <td style="font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; padding: 0;" valign="top"></td>
        </tr>
    </table>

    </body>
    </html>
        '
        ]);
    }
}
