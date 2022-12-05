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
        $mailTemplate = new MailTemplate();
        $mailTemplate->mailable = \App\Mail\ShipmentConfirmationMail::class;
        $mailTemplate->subject = 'testing Subject';
        $mailTemplate->reply_to = 'test@example.com';
        $mailTemplate->to = 'test@example.com';
        $mailTemplate->html_template = '<p>tes</p>';
        $mailTemplate->text_template = null;
        $mailTemplate->save();

        $response = $this->put(route('api.settings.mail-templates.update', $mailTemplate), [
            'subject'       => 'update subject',
            'html_template' => '<p>update html</p>',
            'text_template' => 'update text',
            'to'            => '',
            'reply_to'      => 'test@example.com'
        ]);

        return $response;
    }

    /** @test */
    public function test_update_call_returns_ok()
    {
        Passport::actingAs(
            User::factory()->admin()->create()
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
            User::factory()->create()
        );

        $response = $this->simulationTest();

        $response->assertForbidden();
    }
}
