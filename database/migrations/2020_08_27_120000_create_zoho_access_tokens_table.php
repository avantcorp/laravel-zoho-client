<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZohoAccessTokensTable extends Migration
{
    public function up(): void
    {
        Schema::create('zoho_access_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('user_id');
            $table->text('data');
            $table->string('refresh_token')
                ->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zoho_access_tokens');
    }
}
