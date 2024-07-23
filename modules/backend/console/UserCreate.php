<?php

namespace Backend\Console;

use Backend\Models\User;
use Backend\Models\UserRole;
use Illuminate\Support\Facades\Config;
use Winter\Storm\Console\Command;

class UserCreate extends Command
{
    use \Winter\Storm\Console\Traits\ConfirmsWithInput;

    /**
     * @var string The console command name.
     */
    protected static $defaultName = 'user:create';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'user:create email
        {email : The email address of the user to create.}
        {--password= : The password for the user.}
        {--fname= : The first name of the user.}
        {--lname= : The last name of the user.}
        {--role= : The role to assign the user to. <info>Use the role\'s code</info>}
        {--f|force : Force the operation to run and ignore production warnings and confirmation questions.}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Creates a backend user.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');

        if (
            Config::get('app.env', 'production') !== 'local'
            && !$this->option('force')
            && !$this->confirmWithInput("CAUTION, currently working with non-local data. Please confirm the user email address", $email)
        ) {
            return 1;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('A user with that email already exists.');
            return 1;
        }

        $data = [
            'email' => $email,
            'password' => $this->option('password') ?: $this->secret('Password'),
            'first_name' => $this->option('fname') ?: $this->ask('First name', ''),
            'last_name' => $this->option('lname') ?: $this->ask('Last name', ''),
            'role_id' => (
                UserRole::where(
                    'code',
                    $this->option('role') ?: $this->choice(
                        'Role',
                        UserRole::lists('name', 'code')
                    )
                )->firstOrFail()
            )->id,
        ];

        $data['password_confirmation'] = $data['password'];

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'login'      => $data['email'],
            'email'      => $data['email'],
            'role_id'    => $data['role_id'],
            'password'   => $data['password'],
            'password_confirmation' => $data['password'],
        ]);

        $this->info("User {$user->email} created successfully with the {$role->name} role.");

        return 0;
    }
}
