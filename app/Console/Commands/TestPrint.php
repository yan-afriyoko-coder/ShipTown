<?php

namespace App\Console\Commands;

use App\Services\PrintService;
use App\User;
use Dompdf\Dompdf;
use Illuminate\Console\Command;

class TestPrint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'print:test';

    private $printService;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a test print using the default user\'s (user ID = 1) preferred printer.
        Setup printers via the UI before using this command.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PrintService $printService)
    {
        parent::__construct();

        $this->printService = $printService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml('hello world');

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        $user = User::find(1);

        if (empty($user->printer_id)) {
            $this->error('Please setup printers.');
            return;
        }

        $response = $this->printService->newPdfPrintJob($user->printer_id, 'Test Print', $dompdf->output());

        $this->info('Server responded with status: ' . $response->getStatusCode());

        if ($response->getStatusCode() == 201) {
            $this->info('Check the computer connected to the printer for the output.');
        }
    }
}
