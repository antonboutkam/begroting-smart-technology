<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Classes\Commands;

use Roc\SmartTech\Begroting\Classes\Repositories\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:user', description: 'Beheer gebruikers binnen het begrotingssysteem.')]
final class UsersCommand extends Command
{
    public function __construct(private readonly UserRepository $users)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('action', InputArgument::REQUIRED, 'Actie: list of create')
            ->addArgument('username', InputArgument::OPTIONAL, 'Gebruikersnaam')
            ->addArgument('email', InputArgument::OPTIONAL, 'E-mailadres')
            ->addArgument('password', InputArgument::OPTIONAL, 'Wachtwoord')
            ->addArgument('display_name', InputArgument::OPTIONAL, 'Weergavenaam');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $action = (string) $input->getArgument('action');

        if ($action === 'list') {
            $rows = $this->users->all();
            if ($rows === []) {
                $io->warning('Er zijn nog geen gebruikers aanwezig.');
                return Command::SUCCESS;
            }

            $io->table(['ID', 'Gebruikersnaam', 'Naam', 'E-mail', 'Aangemaakt'], array_map(
                static fn (array $user): array => [
                    $user['id'],
                    $user['username'],
                    $user['display_name'],
                    $user['email'],
                    $user['created_at'],
                ],
                $rows
            ));

            return Command::SUCCESS;
        }

        if ($action === 'create') {
            $username = (string) $input->getArgument('username');
            $email = (string) $input->getArgument('email');
            $password = (string) $input->getArgument('password');
            $displayName = (string) $input->getArgument('display_name');

            if ($username === '' || $email === '' || $password === '' || $displayName === '') {
                $io->error('Gebruik: php bin/console app:user create gebruikersnaam email@example.com wachtwoord "Naam".');
                return Command::INVALID;
            }

            $this->users->create($username, $email, $displayName, $password);
            $io->success('Gebruiker aangemaakt.');
            return Command::SUCCESS;
        }

        $io->error('Onbekende actie. Gebruik list of create.');
        return Command::INVALID;
    }
}
