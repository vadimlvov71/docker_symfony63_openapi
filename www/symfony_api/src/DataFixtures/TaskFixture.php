<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Monolog\DateTimeImmutable;
use App\Repository\TaskRepository;
use App\Entity\Task;
use App\Entity\Status;
use App\Entity\User;
use App\Entity\Description;

class TaskFixture extends Fixture
{
    private $taskRepository;
 
    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }
 
    public function load(ObjectManager $manager): void
    {
        $date = date("Y-m-d");
        $date = new \DateTime($date);
        $temp = [];
        for ($i = 1; $i < 20; $i++) {

            $day_random = $date->modify('-' . $i . 'day');
            $status = Status::randomValue();
            $user_id = User::randomValue();
        
            $task = new Task();
            $task->setTitle('Task '.$i);
            $task->setDescription(Description::randomValue());
            $task->setPriority(mt_rand(1, 5));
            $task->setStatus($status);
            $task->setCreatedAt($day_random);
            if ($status == "done") {
                $task->setCompletedAt($day_random);
            }
            $task->setUserId($user_id);
            $manager->persist($task);
        }
       
        $manager->flush();

        $tasks = $this->taskRepository->findAll();
        $i = 1;
        $temp = [];
        foreach ($tasks as $taskItem) {  
            foreach (User::cases() as $user) { 
                if ($i < 10 && $taskItem->getStatus() == "todo" && $taskItem->getUserId() == $user->value) {
                    $temp[$taskItem->getUserId()][] = $taskItem->getId();
                }
            }
            
            if ($i > 10 && array_key_exists($taskItem->getUserId(), $temp) == $taskItem->getUserId()) {
                $taskItem->setParentId($temp[$taskItem->getUserId()][array_rand($temp[$taskItem->getUserId()])]);
                $manager->persist($task);
            }  
            $i++;
        }
        $manager->flush();
    }
}
