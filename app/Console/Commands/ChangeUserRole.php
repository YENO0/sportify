<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ChangeUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:change-role {email} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change a user\'s role (student, committee, or admin)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $role = $this->argument('role');

        // Validate role
        $validRoles = [User::ROLE_STUDENT, User::ROLE_COMMITTEE, User::ROLE_ADMIN];
        if (!in_array($role, $validRoles)) {
            $this->error("Invalid role. Valid roles are: " . implode(', ', $validRoles));
            return 1;
        }

        // Find user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }

        // Update role
        $user->role = $role;
        $user->save();

        $this->info("Successfully changed role for {$user->name} ({$user->email}) to '{$role}'");
        return 0;
    }
}
