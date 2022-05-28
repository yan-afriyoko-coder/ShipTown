<?php

namespace Tests\Feature\Modules\Automations\Actions;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Mail\OrderMail;
use App\Models\MailTemplate;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Modules\Automations\src\Actions\SendEmailToCustomerActionAbstract;
use App\Modules\Automations\src\Actions\SendOrderEmailActionAbstract;
use App\Modules\Automations\src\Actions\SplitBundleSkuActionAbstract;
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
        $event = new ActiveOrderCheckEvent($order);
        $action = new SendOrderEmailActionAbstract($event);

        // act
        $actionSucceeded = $action->handle($template->code);

        // validate
        $this->assertTrue($actionSucceeded, 'Action failed');
        Mail::assertSent(OrderMail::class);
    }
}
