<?php

namespace Tests\Feature\Modules\Automations\Actions;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Mail\OrderMail;
use App\Models\MailTemplate;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Modules\Automations\src\Actions\SendEmailToCustomerAction;
use App\Modules\Automations\src\Actions\SplitBundleSkuAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendEmailToCustomerActionTest extends TestCase
{
    use RefreshDatabase;

    /**
     */
    public function test_successful_notification()
    {
        $order = factory(Order::class)->create();
        $event = new ActiveOrderCheckEvent($order);
        $action = new SendEmailToCustomerAction($event);

        // act
        $actionSucceeded = $action->handle('');

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
