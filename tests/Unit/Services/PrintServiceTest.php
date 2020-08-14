<?php

use Mockery;
use PrintNode\PrintJob;
use PrintNode\Request;

use App\Services\PrintService;
use Tests\TestCase;

class PrintServiceTest extends TestCase
{
    public function test_calls_get_printer()
    {
        // Mock the Printode\Request instance so that it doesn't actuall send out API requests.
        // We only want to test the implementation of our own service, and not their library.
        $this->mock(Request::class, function ($mock) {
            $mock->shouldReceive('getPrinters')->once();
        });

        // Call the method to trigger the mock tests
        app(PrintService::class)->getPrinters();
    }

    /**
     * Tests that when we send the correct parameters to the POST PrintJob call using the API client.
     */
    public function test_can_send_new_print_job()
    {
        $faker = \Faker\Factory::create();

        $printer = $faker->randomNumber;
        $content = $faker->sentence;
        $title = $faker->name;

        // Mock the Printode\Request instance so that it doesn't actuall send out API requests.
        // We only want to test the implementation of our own service, and not their library.
        $this->mock(Request::class, function ($mock) use ($printer, $content, $title) {
            $mock->shouldReceive('post')->once()->with(
                \Mockery::on(function($printJob) use ($printer, $content, $title) {
                    return $printJob->printer == $printer                        
                        && $printJob->content == base64_encode($content) // Check that the content is base64 encoded before sending
                        && $printJob->title == $title;
                })
            );
        });
    
        // Call the method to trigger the mock tests
        app(PrintService::class)->newPdfPrintJob($printer, $title, $content);
    }
}
