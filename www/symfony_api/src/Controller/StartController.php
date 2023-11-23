<?php

namespace App\Controller;

use App\Entity\Description;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

#[Route('/start')]
class StartController extends AbstractController
{
    #[Route('/', name: 'app_start_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository, KernelInterface $kernel): Response
    {
        $results = []; 
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput (
        [
            'command' => 'start:app',
            '--no-interaction' => true
        ]);

        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->run($input, $output);

        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();
        $results[] = $content;
        return $this->render('start.html.twig', [
            'results' => $results,
        ]);
    }
    #[Route('/process', name: 'app_start_process', methods: ['GET'])]
    /**
     * this method doesn`t work
     * 
     * @param TaskRepository $taskRepository
     * @param KernelInterface $kernel
     * 
     * @return Response
     */
    public function process(TaskRepository $taskRepository, KernelInterface $kernel): Response
    {
        $process = new Process(['php', 'bin/console', 'doctrine:database:create'], "/var/www/symfony_api/");
        $process->run();
        $results[] = "success";

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        } else {
            $results["database"] = "create";
        }
        $process = new Process(['php', 'bin/console', 'doctrine:migrations:migrate'], "/var/www/symfony_api/");
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        } else {
            $results["migration"] = "success";
        }
        $process = new Process(['yes', '|', 'php', 'bin/console', 'make:fixture'], "/var/www/symfony_api/");
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        } else {
            $results["fixture"] = "success";
        }

        $process = new Process(['php', 'bin/console', 'doctrine:fixture:load']);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        } else {
            $results["fixture"] = "loaded";
        }
        // return new Response(""), if you used NullOutput()
        return $this->render('start.html.twig', [
            'results' => $results,
        ]);
    }

}
