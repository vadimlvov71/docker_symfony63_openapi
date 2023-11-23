<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

// Import the required classed
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

#[Route('/init')]
class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_init_index', methods: ['GET'])]
    public function indexAction(Request $request): Response
    {
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'app:print-lines',
            '--firstline' => "Hello",
            '--secondline' => "World"
        ));

        $output = new BufferedOutput();

        $application->run($input, $output);

        // return the output
        $content = $output->fetch();

        // Send the output of the console command as response
        return $content;
    }
}
