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
use App\Modules\Automations\src\Actions\SendOrderEmailAction;
use App\Modules\Automations\src\Actions\SplitBundleSkuAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendOrderEmailActionTest extends TestCase
{
    use RefreshDatabase;

    /**
     */
    public function test_success_when_template_specified()
    {
        Mail::fake();

        /** @var MailTemplate $template */
        $template = factory(MailTemplate::class)->create([
            'code' => 'shipment_confirmation',
            'subject' => 'We shipped your order!',
            'mailable' => OrderMail::class,
            'html_template' => 'Hi Xyz, We shipped your order'
        ]);

        $order = factory(Order::class)->create();
        $action = new SendOrderEmailAction($order);

        // act
        $actionSucceeded = $action->handle($template->code);

        // validate
        $this->assertTrue($actionSucceeded, 'Action failed');
        Mail::assertSent(OrderMail::class);
    }
}
