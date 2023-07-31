<?php

use App\Models\Heartbeat;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Heartbeat::query()->truncate();
    }
};
