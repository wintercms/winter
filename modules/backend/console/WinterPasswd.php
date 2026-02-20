<?php namespace Backend\Console;

use Backend\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Str;
use Symfony\Component\Console\Question\Question;
use Winter\Storm\Console\Command;

/**
 * Console command to change the password of a Backend user via CLI.
 *
 * @package winter\wn-backend-module
 * @author Ben Thomson
 * @author Winter CMS
 */
class WinterPasswd extends Command
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'winter:passwd';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'winter:passwd
        {username? : The username or email of the Backend user. <info>(eg: admin or admin@example.com)</info>}
        {password? : The new password to set.}
    ';

    /**
     * @var string The console command description.
     */
    protected $description = 'Change the password of a Backend user.';

    /**
     * @var array List of commands that this command replaces (aliases)
     */
    protected $replaces = [
        'october:passwd',
        'winter:password',
    ];

    /**
     * @var bool Was the password automatically generated?
     */
    protected $generatedPassword = false;

    /**
     * Execute the console command.
     * @return int
     */
    public function handle()
    {
        $username = $this->argument('username')
            ?? $this->ask('Username to reset');

        // Check that the user exists
        try {
            $user = User::where('login', $username)
                ->orWhere('email', $username)
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            $this->error('The specified user does not exist.');
            return 1;
        }

        $password = $this->argument('password')
            ?? (
                $this->optionalSecret(
                    'Enter new password (leave blank for generated password)',
                    false,
                    false
                ) ?: $this->generatePassword()
            );

        // Change password
        $user->password = $password;
        $user->forceSave();

        $this->info('Password successfully changed.');
        if ($this->generatedPassword) {
            $this->output->writeLn('Password set to <info>' . $password . '</info>');
        }
        return 0;
    }

    /**
     * Return the 20 most recently updated users for autocompletion of the "username" argument
     */
    public function suggestUsernameValues(): array
    {
        $options = [];
        $users = User::orderBy('updated_at', 'desc')->limit(20)->get();
        foreach ($users as $user) {
            if ($user->email) {
                $options[] = $user->email;
            } elseif ($user->username) {
                $options[] = $user->username;
            }
        }

        return $options;
    }

    /**
     * Prompt the user for input but hide the answer from the console.
     *
     * Also allows for a default to be specified.
     *
     * @param  string  $question
     * @param  bool    $fallback
     * @return string
     */
    protected function optionalSecret($question, $fallback = true, $default = null)
    {
        $question = new Question($question, $default);

        $question->setHidden(true)->setHiddenFallback($fallback);

        return $this->output->askQuestion($question);
    }

    /**
     * Generate a password and flag it as an automatically-generated password.
     *
     * @return string
     */
    protected function generatePassword()
    {
        $this->generatedPassword = true;

        return Str::random(22);
    }
}
