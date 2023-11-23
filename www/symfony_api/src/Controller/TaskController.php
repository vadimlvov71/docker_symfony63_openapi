<?php

namespace App\Controller;

use App\Entity\Description;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\TasksTree;

#[Route('/task')]
class TaskController extends AbstractController
{
    #[Route('/', name: 'app_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        
        $user_id = 1;
        $child_ids = [];
        $criteria = ['user_id' => $user_id];
        $tasks = $taskRepository->findBy($criteria);
        foreach ($tasks as $task) { 
             
            $criteria = ['parent_id' => $task->getId()];
            $tasks_child = $taskRepository->findBy($criteria);
            $temp = [];
            foreach ($tasks_child as $child) { 
                if ($child->getParentId() == $task->getId()) {
                    $temp[$child->getId()] = $child; 
                    $child_ids[] = $child->getId();
                }
            }
            $task->setChild($temp); 
        }
        echo gettype($tasks);
        $i = 0;
        foreach ($tasks as $task) { 
            if (in_array($task->getId(), $child_ids)) {
               unset($tasks[$i]);
            }
            $i++;
        }
        echo "<pre>";
        print_r($child_ids);
        echo "</pre>";
           echo "!!!!!<pre>";
                print_r($tasks);
                echo "</pre>";
       // return $this->json($tasks);
       /* return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findAll(),
        ]);*/
    }

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }
    public function three($taskRepository, $criteria): array
    {
        //$criteria = ['parent_id' => $id];
        $tasks = $taskRepository->findBy($criteria);
        return $tasks;
    }
    public function loopTasks($taskRepository, $criteria, $i, $parents = [], $children = [])
    {
       
        echo "iiiii : ".$i."<br>";
        echo count($parents)."<br>";
        $x = 0;
        if (count($parents) == 0) {
            $parents = $taskRepository->findBy($criteria);
        } else {
            $x++;
            $children = $taskRepository->findBy($criteria);
        }
        
        if (count($parents) > 0) {
            $parent_ids = [];
            foreach ($parents as $parent) {
                echo "parent:: : ".$parent->getId()."<br>";
               // $parents[] = $task;
                $parent_ids[] = $parent->getId();
                $temp = [];
                if (count($children) > 0) {
                    $parent_ids = [];
                    $parent_criteria = [];
                    $parents = [];
                    foreach ($children as $child) {
                        echo "child : ".$child->getId()."<br>";
                        if ($child->getParentId() == $parent->getId()) {
                            $parents[] = $child;
                            $temp[$child->getId()] = $child; 
                            $parent_ids[] = $child->getId();
                        }
                    }
                    $parent->setChild($temp); 
                } else {
                    if ($x != 0) {
                        $parent_ids = [];
                    }
                    
                }
            }
            //exit;
                echo "parent_ids:::<pre>";
                print_r($parent_ids);
                echo "</pre>";
            echo "count::; : ".count($parent_ids)."<br>";
            $parent_criteria = ['parent_id' =>  $parent_ids];
           // echo "parent_criteria::; : ".count($parent_criteria)."<br>";
           /* echo "____<pre>";
                print_r($parent_criteria);
                echo "</pre>";*/
            if (count($parent_ids) > 0) {
                    $i++;
                    if ($i < 7) {
                        $this->loopTasks($taskRepository, $parent_criteria, $i, $parents);
                    }
                
            }
        }
        return $parents;
    }
    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task, TaskRepository $taskRepository, TasksTree $tree): Response
    {
       
        $id = 3;
        $user_id = $request->get('user_id');
        //$tree = new TasksTree($taskRepository);
        $criteria = ['parent_id' => $id];
        $tasks = $tree->loopTasks($criteria, "tree");

        echo "____<pre>";
        print_r($tasks);
        echo "</pre>";
        $tasks = $tree->findChildrenTasksTodo($id);

        echo "____<pre>";
        print_r($tasks);
        echo "</pre>";
       /* echo "<pre>";
        print_r($tasks);
        echo "</pre>";*/
        return $this->json($tasks);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{user_id}/priority_by/{priority_sort}/created_by/{created_sort}', name: 'app_task_sortBy', methods: ['GET'])]
    public function sortBy(Request $request, TaskRepository $taskRepository): Response
    {
       
        $user_id = $request->get('user_id');
        $priority_sort = $request->get('priority_sort');
        $created_sort = $request->get('created_sort');
        //$tree = new TasksTree($taskRepository);
        $tasks = $taskRepository->sortBy($user_id, $priority_sort, $created_sort);

        echo "____<pre>";
        print_r($tasks);
        echo "</pre>";
       
        return $this->json($tasks);
    }
}
