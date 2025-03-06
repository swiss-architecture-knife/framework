<?php

namespace Swark\IdP\Presenter\Console;

use Filament\Commands\MakeUserCommand;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use function Laravel\Prompts\text;

#[AsCommand(name: 'swark:user')]
class UpsertSwarkUser extends MakeUserCommand
{
    protected $description = 'Create a new swark user';

    protected $signature = 'swark:create-user
                            {--name= : The name of the user}
                            {--email= : A valid and unique email address}
                            {--password= : The password for the user (min. 8 characters), leave it empty to generate a new one - it will be printed out}
                            {--is-admin : This user is an admin user}
                            {--skip-on-existing-user : If the user\'s email already exist, do nothing}
                            ';

    private ?string $lastGeneratedPassword = null;

    protected function generatePassword(): string
    {
        return $this->lastGeneratedPassword = Str::random();
    }

    protected function getUserData(): array
    {
        return [
            'name' => $this->options['name'] ?? text(
                    label: 'Name',
                    required: true,
                ),

            'email' => $this->options['email'] ?? text(
                    label: 'Email address',
                    required: true,
                    validate: fn(string $email): ?string => match (true) {
                        !filter_var($email, FILTER_VALIDATE_EMAIL) => 'The email address must be valid.',
                        static::getUserModel()::where('email', $email)->exists() => 'A user with this email address already exists',
                        default => null,
                    },
                ),

            'password' => Hash::make($this->options['password'] ?? $this->generatePassword()),
        ];
    }

    protected function skipOnExistingUser(): bool
    {
        if ($this->options['skip-on-existing-user']) {
            return static::getUserModel()->where('email', $this->options['email'])->exists();
        }

        return false;
    }

    protected function sendSuccessMessage(Authenticatable $user): void
    {
        $loginUrl = Filament::getLoginUrl();

        $info = 'Success! ' . ($user->getAttribute('email') ?? $user->getAttribute('username') ?? 'You') . " may now log in at {$loginUrl}";

        if ($this->lastGeneratedPassword) {
            $info .= " with password '{$this->lastGeneratedPassword}'";
        }

        $this->components->info($info);
    }

    public function handle(): int
    {
        $this->options = $this->options();

        if (!Filament::getCurrentPanel()) {
            $this->error('Filament has not been installed yet: php artisan filament:install --panels');

            return static::INVALID;
        }

        if ($this->skipOnExistingUser()) {
            $this->info("User with this email address already exists, not creating a new one");
        }

        $user = $this->createUser();
        $this->sendSuccessMessage($user);

        return static::SUCCESS;
    }
}
