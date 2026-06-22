<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add any missing columns to existing tables (safe, non-destructive)
        if (Schema::hasTable('bja_invoices') && !Schema::hasColumn('bja_invoices', 'order_type')) {
            Schema::table('bja_invoices', function (Blueprint $table) {
                $table->string('order_type', 30)->default('');
            });
        }

        if (Schema::hasTable('bja_leads')) {
            $additions = [
                'tujuan'        => fn(Blueprint $t) => $t->string('tujuan', 120)->nullable(),
                'detail'        => fn(Blueprint $t) => $t->text('detail')->nullable(),
                'leads_per_day' => fn(Blueprint $t) => $t->integer('leads_per_day')->default(0),
                'klasifikasi'   => fn(Blueprint $t) => $t->string('klasifikasi', 30)->default(''),
                'date'          => fn(Blueprint $t) => $t->date('date')->nullable(),
            ];
            foreach ($additions as $col => $definition) {
                if (!Schema::hasColumn('bja_leads', $col)) {
                    Schema::table('bja_leads', function (Blueprint $table) use ($definition) {
                        $definition($table);
                    });
                }
            }
        }
    }

    public function down(): void
    {
        // intentionally empty — never drop live data
    }
};
