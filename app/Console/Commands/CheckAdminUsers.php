<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckAdminUsers extends Command
{
    protected $signature = 'admin:check';
    protected $description = 'Check admin users in the database';

    public function handle()
    {
        $this->info('=== Checking Admin Users ===');
        
        $users = User::all();
        
        $this->info("\nTotal users: " . $users->count());
        
        $this->table(
            ['ID', 'Name', 'Email', 'Role', 'isAdmin()'],
            $users->map(function ($user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    "'{$user->role}'",
                    $user->isAdmin() ? 'YES' : 'NO'
                ];
            })
        );
        
        $adminCount = User::where('role', 'admin')->count();
        $this->info("\nUsers with role='admin': {$adminCount}");
        
        return 0;
    }
}
