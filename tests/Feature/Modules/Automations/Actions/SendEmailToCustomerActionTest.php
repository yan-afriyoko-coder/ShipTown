<?php

namespace Tests\Feature\Modules\Automations\Actions;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Mail\JustTesting;
use App\Mail\OrderMail;
use App\Mail\ShipmentConfirmationMail;
use App\Models\MailTemplate;
use App\Models\Order;
use App\Models\ShippingLabel;
use App\Modules\Automations\src\Actions\SendEmailToCustomerAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendEmailToCustomerActionTest extends TestCase
{
    /**
     */
    public function test_successful_notification()
    {
        $order = factory(Order::class)->create();
        $orderShipment = factory(ShippingLabel::class)->create();

        $event = new ActiveOrderCheckEvent($order);

        $action = new SendEmailToCustomerAction($event);

        Mail::fake();

        // act
        $actionSucceeded = $action->handle('ready_for_collection_notification');

        Mail::assertSent(OrderMail::class, function ($mail) {
            $this->assertEquals('ready_for_collection_notification', $mail->getMailTemplate()->code);
            return true;
        });

        // validate
        $this->assertTrue($actionSucceeded, 'Action failed');
    }

    /**
     */
    public function test_success_when_template_specified()
    {
        /** @var MailTemplate $template */
        $template = factory(MailTemplate::class)->create([
            'code' => 'shipment_confirmation',
            'subject' => 'test email',
            'mailable' => OrderMail::class,
            'html_template' => ''
        ]);

        $order = factory(Order::class)->create();
        $event = new ActiveOrderCheckEvent($order);
        $action = new SendEmailToCustomerAction($event);

        // act
        $actionSucceeded = $action->handle($template->code);

        // validate
        $this->assertTrue($actionSucceeded, 'Action failed');
    }
}
