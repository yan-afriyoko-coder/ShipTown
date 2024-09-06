<?php

namespace Tests\Feature\Api\MailTemplates;

use App\User;
use Tests\TestCase;
use App\Models\Order;
use Laravel\Passport\Passport;

class StoreTest extends TestCase
{
    /** @test */
    public function it_creates_a_new_mail_template_and_returns_it_as_a_resource()
    {
        Passport::actingAs(
            User::factory()->create(),
            ['mail-template-create'] 
        );
    
        $requestData = [
            'code' => 'UniqueCode123', 
            'to' => 'test@example.com', 
            'reply_to' => 'reply@example.com',
            'subject' => 'Test Subject',
            'html_template' => '<p>This is a test body.</p>', 
            'text_template' => 'This is a test body.', 
        ];
    
        $response = $this->postJson(route('api.mail-templates.store'), $requestData);

        $response->dump(); 

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'name',
                         'to',
                         'reply_to',
                         'subject',
                         'html_template',
                         'text_template',
                     ],
                 ]);
    
        $this->assertDatabaseHas('mail_templates', [
            'code' => 'UniqueCode123',
            'subject' => 'Test Subject',
            'html_template' => '<p>This is a test body.</p>',
        ]);
    }
}