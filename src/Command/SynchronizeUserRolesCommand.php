<?php

namespace App\Command;

use App\Entity\User;
use App\Service\RoleFonctionManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:synchronize-user-roles',
    description: 'Synchronise les fonctions des utilisateurs en fonction de leurs rôles',
)]
class SynchronizeUserRolesCommand extends Command
{
    private $entityManager;
    private $roleFonctionManager;

    public function __construct(EntityManagerInterface $entityManager, RoleFonctionManager $roleFonctionManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->roleFonctionManager = $roleFonctionManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $userRepository = $this->entityManager->getRepository(User::class);
        $users = $userRepository->findAll();
        
        $count = 0;
        foreach ($users as $user) {
            $this->roleFonctionManager->synchronizeFonctionsFromRoles($user);
            $count++;
        }
        
        $this->entityManager->flush();
        
        $io->success(sprintf('%d utilisateurs ont été synchronisés avec succès.', $count));

        return Command::SUCCESS;
    }
}
