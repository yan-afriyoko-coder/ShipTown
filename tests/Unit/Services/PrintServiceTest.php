<?php

namespace Tests\Unit\Services;

use App\Services\PrintService;
use PrintNode\PrintJob;
use PrintNode\Request;
use Tests\TestCase;

class PrintServiceTest extends TestCase
{
    public function testCallsGetPrinter()
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
    public function testCanSendNewPrintJob()
    {
        $faker = \Faker\Factory::create();

        $printer = $faker->randomNumber;
        $content = $faker->sentence;
        $title = $faker->name;

        // Mock the Printode\Request instance so that it doesn't actuall send out API requests.
        // We only want to test the implementation of our own service, and not their library.
        $this->mock(Request::class, function ($mock) use ($printer, $content, $title) {
            $mock->shouldReceive('post')->once()->with(
                \Mockery::on(function ($printJob) use ($printer, $content, $title) {
                    return $printJob->printer == $printer
                        // Check that the content is base64 encoded before sending
                        && $printJob->content == base64_encode($content)
                        && $printJob->title == $title;
                })
            );
        });

        // Call the method to trigger the mock tests
        app(PrintService::class)->newPdfPrintJob($printer, $title, $content);
    }
}
