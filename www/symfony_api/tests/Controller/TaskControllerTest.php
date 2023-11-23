<?php

namespace App\Test\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private TaskRepository $repository;
    private string $path = '/task/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Task::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Task index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'task[title]' => 'Testing',
            'task[description]' => 'Testing',
            'task[createdAt]' => 'Testing',
            'task[completedAt]' => 'Testing',
            'task[priority]' => 'Testing',
            'task[user_id]' => 'Testing',
            'task[parent_id]' => 'Testing',
            'task[status]' => 'Testing',
        ]);

        self::assertResponseRedirects('/task/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Task();
        $fixture->setTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setCompletedAt('My Title');
        $fixture->setPriority('My Title');
        $fixture->setUser_id('My Title');
        $fixture->setParent_id('My Title');
        $fixture->setStatus('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Task');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Task();
        $fixture->setTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setCompletedAt('My Title');
        $fixture->setPriority('My Title');
        $fixture->setUser_id('My Title');
        $fixture->setParent_id('My Title');
        $fixture->setStatus('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'task[title]' => 'Something New',
            'task[description]' => 'Something New',
            'task[createdAt]' => 'Something New',
            'task[completedAt]' => 'Something New',
            'task[priority]' => 'Something New',
            'task[user_id]' => 'Something New',
            'task[parent_id]' => 'Something New',
            'task[status]' => 'Something New',
        ]);

        self::assertResponseRedirects('/task/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getCompletedAt());
        self::assertSame('Something New', $fixture[0]->getPriority());
        self::assertSame('Something New', $fixture[0]->getUser_id());
        self::assertSame('Something New', $fixture[0]->getParent_id());
        self::assertSame('Something New', $fixture[0]->getStatus());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Task();
        $fixture->setTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setCompletedAt('My Title');
        $fixture->setPriority('My Title');
        $fixture->setUser_id('My Title');
        $fixture->setParent_id('My Title');
        $fixture->setStatus('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/task/');
    }
}
