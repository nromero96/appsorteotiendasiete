<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('prize_id', 80)->nullable()->after('draw_id')->index();
        });

        DB::statement('
            UPDATE tickets
            INNER JOIN ticket_prizes ON ticket_prizes.ticket_id = tickets.id
            SET tickets.prize_id = ticket_prizes.prize_id
            WHERE tickets.prize_id IS NULL
        ');

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreign('prize_id')->references('id')->on('prizes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['prize_id']);
            $table->dropColumn('prize_id');
        });
    }
};
