<?php

namespace App\Command;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateUserRolesCommand extends Command
{
    protected static $defaultName = 'app:update-user-roles';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setDescription('Met à jour les rôles des utilisateurs en fonction de leur email');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $userRepository = $this->entityManager->getRepository(Utilisateur::class);
        $users = $userRepository->findAll();

        foreach ($users as $user) {
            $email = $user->getEmail();
            $oldRoles = $user->getRoles();
            
            // Définir les rôles en fonction de l'email
            if (str_contains(strtolower($email), 'dashboard')) {
                $user->setRoles(['ROLE_ADMIN']);
            } else {
                $user->setRoles(['ROLE_USER']);
            }
            
            $newRoles = $user->getRoles();
            $io->text(sprintf(
                'Utilisateur %s : rôles changés de [%s] à [%s]',
                $email,
                implode(', ', $oldRoles),
                implode(', ', $newRoles)
            ));
        }

        $this->entityManager->flush();
        $io->success('Les rôles des utilisateurs ont été mis à jour.');

        return Command::SUCCESS;
    }
} 