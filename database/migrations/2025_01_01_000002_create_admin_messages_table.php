<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_messages', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Titre du message
            $table->text('message'); // Contenu du message
            $table->enum('type', ['info', 'success', 'warning', 'error', 'celebration']); // Type de message
            $table->enum('priority', ['low', 'medium', 'high', 'urgent']); // Priorité
            $table->boolean('is_active')->default(true); // Message actif/inactif
            $table->timestamp('expires_at')->nullable(); // Date d'expiration
            $table->json('target_roles')->nullable(); // Rôles cibles (seller, admin, etc.)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_messages');
    }
};
