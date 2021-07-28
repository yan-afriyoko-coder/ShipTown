<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\MailTemplateController;

use App\Models\MailTemplate;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    private function simulationTest()
    {
        $orderStatus = new MailTemplate();
        $orderStatus->mailable = 'App\Mail\ShipmentConfirmationMail';
        $orderStatus->subject = 'testing Subject';
        $orderStatus->html_template = '<p>tes</p>';
        $orderStatus->text_template = null;
        $orderStatus->save();

        $response = $this->put(route('api.settings.mail-templates.update', $orderStatus), [
            'subject'      => 'update subject',
            'html_template'    => '<p>update html</p>',
            'text_template'    => 'update text',
        ]);

        return $response;
    }

    /** @test */
    public function test_update_call_returns_ok()
    {
        Passport::actingAs(
            factory(User::class)->states('admin')->create()
        );

        $response = $this->simulationTest();

        $response->assertSuccessful();
    }

    public function test_update_call_should_be_loggedin()
    {
        $response = $this->simulationTest();

        $response->assertRedirect(route('login'));
    }

    public function test_update_call_should_loggedin_as_admin()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->simulationTest();

        $response->assertForbidden();
    }
}
