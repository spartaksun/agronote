<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'create:admin',
    description: 'Create admin',
)]
class CreateAdminCommand extends Command
{

    private UserPasswordHasherInterface $hasher;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $hasher,
        string                      $name = null
    )
    {
        $this->hasher = $hasher;
        $this->entityManager = $entityManager;

        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $user = new User();
            $user->setUsername('admin');
            $user->setPassword(
                $this->hasher->hashPassword($user, 'admin')
            );
            $user->setRoles(['ROLE_ADMIN']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $output->writeln('Admin successfully created');
        } catch (Exception $e) {
            $output->writeln(sprintf('Error when creating an admin: "%s"', $e->getMessage()));
        }

        return Command::SUCCESS;
    }
}
