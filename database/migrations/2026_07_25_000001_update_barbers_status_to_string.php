<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE barbers MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'masuk'");

        DB::table('barbers')->where('status', '1')->orWhere('status', 'true')->update(['status' => 'masuk']);
        DB::table('barbers')->where('status', '0')->orWhere('status', 'false')->update(['status' => 'nonaktif']);
    }

    public function down(): void
    {
        DB::table('barbers')->where('status', 'masuk')->update(['status' => '1']);
        DB::table('barbers')->where('status', '!=', '1')->update(['status' => '0']);
        DB::statement("ALTER TABLE barbers MODIFY COLUMN status TINYINT(1) NOT NULL DEFAULT 1");
    }
};
