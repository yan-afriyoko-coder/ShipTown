<?php

namespace App\Services;

use PrintNode\Credentials;
use PrintNode\Request;

class PrintService
{
    private $credentials = null;

    public function setApiKey($key)
    {
        $this->credentials = (new Credentials)->setApiKey($key);
    }

    public function getPrinters()
    {
        return $this->request()->getPrinters();
    }

    private function request()
    {
        return new Request($this->credentials);
    }
}