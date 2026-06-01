<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drive_files', function (Blueprint $table) {
            $table->string('public_upload_key', 80)->nullable()->after('uploaded_by')->index();
        });
    }

    public function down(): void
    {
        Schema::table('drive_files', function (Blueprint $table) {
            $table->dropColumn('public_upload_key');
        });
    }
};
