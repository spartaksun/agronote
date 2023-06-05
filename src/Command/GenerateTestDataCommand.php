<?php

namespace App\Command;

use App\Entity\Task;
use App\Entity\User;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use joshtronic\LoremIpsum;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(
    name: 'generate:test-data',
    description: 'Generate test tasks for all existing users',
)]
class GenerateTestDataCommand extends Command
{
    private readonly EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        string                 $name = null
    )
    {
        parent::__construct($name);
        $this->entityManager = $entityManager;
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ipsum = new LoremIpsum();
        $users = $this->entityManager->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            for ($i = 0; $i < 100; $i++) {
                try {
                    $task = new Task();
                    $task->setTitle($ipsum->words(rand(1, 4)));
                    $task->setDescription($ipsum->paragraph());
                    $statuses = array_values(Task::statuses());
                    $task->setStatus($statuses[rand(0, count($statuses) - 1)]);
                    $task
                        ->setDueDate((new DateTime())
                            ->add(new DateInterval(sprintf('P%sD', rand(1, 365))))
                        );
                    $task->setCreatedBy($user);

                    $this->entityManager->persist($task);
                    $this->entityManager->flush();
                } catch (Exception $e) {
                    $output->writeln(sprintf(
                        'Error: Cant create a task %s',
                        $e->getMessage()
                        )
                    );
                }

                gc_collect_cycles();
                $output->writeln('Created a task.');
            }
        }

        return Command::SUCCESS;
    }


}
