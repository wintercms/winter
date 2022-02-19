<?php namespace System\Console;

use Str;
use Backend\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;

/**
 * Console command to change the password of a Backend user via CLI.
 *
 * @package winter\wn-system-module
 * @author Ben Thomson
 * @author Winter CMS
 */
class WinterPasswd extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'winter:passwd';

    /**
     * @var string The console command description.
     */
    protected $description = 'Change the password of a Backend user.';

    /**
     * @var bool Was the password automatically generated?
     */
    protected $generatedPassword = false;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        // Register aliases for backwards compatibility with October
        $this->setAliases(['october:passwd']);
    }

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
            $this->output->writeLn('Password set to <info>' . $password . '</info>.');
        }
        return 0;
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['username', InputArgument::OPTIONAL, 'The username of the Backend user'],
            ['password', InputArgument::OPTIONAL, 'The new password']
        ];
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
