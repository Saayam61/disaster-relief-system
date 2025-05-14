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
        // Users Table
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('password');
            $table->enum('role', ['General User', 'Relief Center', 'Organization', 'Volunteer', 'Administrator']);
            $table->double('latitude');
            $table->double('longitude');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        
        // Flood Alerts Table
        Schema::create('flood_alerts', function (Blueprint $table) {
            $table->id('alert_id');
            $table->foreignId('admin_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('message');
            $table->string('severity');
            $table->text('description')->nullable();
            $table->timestamp('timestamp')->useCurrent();
            $table->boolean('is_active')->default(true);
        });

        // Relief Centers Table
        Schema::create('relief_centers', function (Blueprint $table) {
            $table->id('center_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->text('address');
            $table->integer('capacity');
            $table->integer('current_occupancy')->default(0);
            $table->integer('total_volunteers');
            $table->text('total_supplies')->nullable();
            $table->string('contact_numbers');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Organizations Table
        Schema::create('organizations', function (Blueprint $table) {
            $table->id('org_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->enum('type', ['i/ngo', 'private']);
            $table->integer('total_volunteers');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Volunteers Table
        Schema::create('volunteers', function (Blueprint $table) {
            $table->id('volunteer_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('center_id')->nullable()->constrained('relief_centers', 'center_id')->onDelete('set null');
            $table->foreignId('org_id')->nullable()->constrained('organizations', 'org_id')->onDelete('set null');            
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('skills')->nullable();
            $table->text('availability')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->timestamps();
        });

        // Requests Table
        Schema::create('requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('center_id')->constrained('relief_centers', 'center_id')->onDelete('cascade');            
            $table->enum('request_type', ['supply', 'evacuation', 'medical', 'other']);
            $table->enum('status', ['pending', 'processing', 'fulfilled', 'rejected'])->default('pending');
            $table->text('description');
            $table->integer('quantity')->nullable();
            $table->string('unit', 10)->nullable();
            $table->enum('urgency', ['low', 'medium', 'high'])->nullable();
            $table->timestamps();
        });

        // News Feed Table
        Schema::create('news_feed', function (Blueprint $table) {
            $table->id('post_id');
            $table->foreignId('center_id')->constrained('relief_centers', 'center_id')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->string('image_url')->nullable();
            $table->timestamps();
        });

        // Communications Table
        Schema::create('communications', function (Blueprint $table) {
            $table->id('message_id');
            $table->foreignId('sender_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users', 'user_id')->onDelete('cascade');            
            $table->text('message');
            $table->timestamp('timestamp')->useCurrent();
            $table->enum('read_status', ['sent', 'delivered', 'read'])->default('sent');
        });

        // Contributions Table
        Schema::create('contributions', function (Blueprint $table) {
            $table->id('contribution_id');
            $table->foreignId('center_id')->nullable()->constrained('relief_centers', 'center_id')->onDelete('cascade');
            $table->foreignId('org_id')->nullable()->constrained('organizations', 'org_id')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id')->onDelete('set null');
            $table->foreignId('volunteer_id')->nullable()->constrained('volunteers', 'volunteer_id')->onDelete('set null');
            $table->string('name', 100);
            $table->integer('quantity');
            $table->string('unit', 10);
            $table->enum('type', ['received', 'donated']);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Indexes for optimization
        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
        });

        Schema::table('communications', function (Blueprint $table) {
            $table->index('timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributions');
        Schema::dropIfExists('communications');
        Schema::dropIfExists('news_feed');
        Schema::dropIfExists('requests');
        Schema::dropIfExists('volunteers');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('relief_centers');
        Schema::dropIfExists('flood_alerts');
        Schema::dropIfExists('users');
    }
};