<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get all users with their roles
$users = DB::table('users')->select('id', 'name', 'email', 'role')->get();

echo "=== Users in Database ===\n";
foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Role: {$user->role}\n";
}

echo "\n=== Checking if admin role exists ===\n";
$adminCount = DB::table('users')->where('role', 'admin')->count();
echo "Number of admin users: {$adminCount}\n";
