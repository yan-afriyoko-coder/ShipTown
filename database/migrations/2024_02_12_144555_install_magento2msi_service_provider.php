<?php

use App\Modules\Magento2MSI\src\Magento2MsiServiceProvider;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Magento2MsiServiceProvider::installModule();
    }
};
