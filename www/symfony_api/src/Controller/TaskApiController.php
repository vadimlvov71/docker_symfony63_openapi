<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\DateTimeImmutable;
use App\Entity\Status;
use App\Entity\User;
use App\Entity\Task;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\Validation;
use App\Service\TasksTree;

/**
 * TaskApiController uses OpenAPI and has actions:
 * 4 ones for crud, one - for changing only one field: 'current salary'
 * used API with five entrypoints
 * create a tree of a tasks of a one
 * 
 * 
* @author Vadim Podolyan <vadim.podolyan@gmail.com>
*
 */
#[Route('/task/api')]
class TaskApiController extends AbstractController
{
    #[Route('/list/{user_id}', name: 'app_task', methods: ['GET'])]
    /**
     * @param Request $request
     * @param TaskRepository $taskRepository
     * 
     * @return Response
     */
    public function index(Request $request, TaskRepository $taskRepository): Response
    {
        $user_id = $request->get('user_id');
        $criteria = ['user_id' => $user_id];
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
        $i = 0;
        foreach ($tasks as $task) { 
            if (in_array($task->getId(), $child_ids)) {
               unset($tasks[$i]);
            }
            $i++;
        }
        return $this->json($tasks);
    }
    #[Route('/{user_id}/status/{status}', name: 'app_task_status', methods: ['GET'])]
    /**
     * @param Request $request
     * @param TaskRepository $taskRepository
     * 
     * @return Response
     */
    public function status(Request $request, TaskRepository $taskRepository): Response
    {
        $user_id = $request->get('user_id');
        $status = $request->get('status');
        $criteria = ['user_id' => $user_id, 'status' => $status];
        $tasks = $taskRepository->findBy($criteria);
        return $this->json($tasks);
    }
    #[Route('/{user_id}/priority/{priority}', name: 'app_task_priority', methods: ['GET'])]
    /**
     * @param Request $request
     * @param TaskRepository $taskRepository
     * 
     * @return Response
     */
    public function priority(Request $request, TaskRepository $taskRepository): Response
    {
        $user_id = $request->get('user_id');
        $priority = $request->get('priority');
        $criteria = ['user_id' => $user_id, 'priority' => $priority];
        $tasks = $taskRepository->findBy($criteria);
        return $this->json($tasks);
    }
    #[Route('/{user_id}/title/{title}', name: 'app_task_title', methods: ['GET'])]
    /**
     *  Filter by Title
     * @param Request $request
     * @param TaskRepository $taskRepository
     * 
     * @return Response
     */
    public function title(Request $request, TaskRepository $taskRepository): Response
    {
        $user_id = $request->get('user_id');
        $title = $request->get('title');
        $tasks = $taskRepository->findByTitle($title, $user_id);
        //$tasks = gettype($tasks);
        return $this->json($tasks);
    }
    #[Route('/{user_id}/description/{description}', name: 'app_task_description', methods: ['GET'])]
    /**
     * Filter by Description
     * @param Request $request
     * @param TaskRepository $taskRepository
     * 
     * @return Response
     */
    public function description(Request $request, TaskRepository $taskRepository): Response
    {
        $user_id = $request->get('user_id');
        $description = $request->get('description');
        $tasks = $taskRepository->findByDescription($description, $user_id);
        return $this->json($tasks);
    }
    #[Route('/new', name: 'app_task_api_new', methods: ['GET', 'POST'])]
    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param validatorInterface $validator
     * 
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $entityManager, validatorInterface $validator): Response
    {
        $constraints = Validation::getConstrains();
        $response = [];
        $responseItem = [];

        $postData = $request->toArray();

        $postData["priority"] = (int)$postData["priority"];
        $validationResult = $validator->validate($postData, $constraints);
        
        if (count($validationResult) > 0) {
            foreach ($validationResult as $result) {
                $responseItem[$result->getPropertyPath()] = $result->getMessage();
            }
            $response['validate_error'] = $responseItem;
        } else {
            
            $response[] = "validate_success";
            try {
                $task = new Task();
                $task->setTitle($postData["title"]);
                $task->setDescription($postData["description"]);
                $task->setPriority($postData["priority"]);
                $task->setStatus($postData["status"]);
                $task->setCreatedAt(new \DateTime());
                $task->setUserId($postData["user_id"]);
                if (isset($postData["parent_id"]) && $postData["parent_id"] != null) {
                    $task->setParentId($postData["parent_id"]);
                }
                $entityManager->persist($task);
                $entityManager->flush();
                $response[] = $task->getId();
                $response[] = "insert_success";
            } catch (\Exception $e) {
                $response['insert_errror'] = $e->getMessage();
            }
        }
        return $this->json($response);
    }
    #[Route('/{user_id}/edit/{id}', name: 'app_task_api_edit', methods: ['GET', 'PUT'])]
    /**
     * @param Request $request
     * @param Task $task
     * @param EntityManagerInterface $entityManager
     * @param validatorInterface $validator
     * @param TaskRepository $taskRepository
     * 
     * @return Response
     */
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager, validatorInterface $validator, TaskRepository $taskRepository): Response
    {
        
        $response = [];
        $responseItem = [];

        $postData = $request->toArray();
        $postData["priority"] = (int)$postData["priority"];

        $constraints = Validation::getConstrains("edit", $task->getUserId());
        $postData["user_id"] = (int)$request->get('user_id');

        $validationResult = $validator->validate($postData, $constraints);
        if (count($validationResult) > 0) {
            foreach ($validationResult as $result) {
                $responseItem[$result->getPropertyPath()] = $result->getMessage();
            }
            $response['validate_error'] = $responseItem;
        } else {
            $response[] = "validate_success";
            try {
                $task->setTitle($postData["title"]);
                $task->setDescription($postData["description"]);
                $task->setPriority($postData["priority"]);
                $task->setStatus($postData["status"]);
                $task->setCreatedAt(new \DateTime());
                $entityManager->persist($task);
                $entityManager->flush();
                $response[] = $task->getId();
                $response[] = "update_success";
            } catch (\Exception $e) {
                $response['update_errror'] = $e->getMessage();
            }
        }
        return $this->json($response);
    }

    #[Route('/{user_id}/change_status/{id}/{status}', name: 'app_task_ip_new_salary', methods: ['GET', 'PATCH'])]
    /**
     * @param Request $request
     * @param task $task
     * @param EntityManagerInterface $entityManager
     * @param validatorInterface $validator
     * @param taskRepository $taskRepository
     * 
     * @return Response
     */
    public function changeStatus(Request $request, task $task, EntityManagerInterface $entityManager, validatorInterface $validator, taskRepository $taskRepository, TasksTree $taskTree): Response
    {

        $constraints = Validation::getConstrains("status", $task->getUserId());
        $response = [];
        $responseItem = [];
        $postData = [];
        $id = (int)$request->get('id');
        $user_id = (int)$request->get('user_id');
        $postData["id"] = $id;
        $postData["user_id"] = $user_id;
        $postData["status"] = $request->get('status');
        $criteria = ['parent_id' => $id];

        $tasks = $taskTree->findChildrenTasksTodo($id);

        return $this->json($tasks);
    }
    #[Route('/{user_id}/delete/{id}', name: 'app_task_api_delete', methods: ['DELETE'])]
    /**
     * @param Request $request
     * @param task $task
     * @param EntityManagerInterface $entityManager
     * 
     * @return Response
     */
    public function delete(Request $request, Task $task, validatorInterface $validator, EntityManagerInterface $entityManager): Response
    {
        $response = [];
        $responseItem = [];

        $postData = [];
        $postData["user_id"] = (int)$request->get('user_id');
        $postData["status"] = $task->getStatus();
  
        $constraints = Validation::getConstrains("delete", $task->getUserId());

        $validationResult = $validator->validate($postData, $constraints);
        if (count($validationResult) > 0) {
            foreach ($validationResult as $result) {
                $responseItem[$result->getPropertyPath()] = $result->getMessage();
            }
            $response['validate_error'] = $responseItem;
        } else {
            try {
                $entityManager->remove($task);
                $entityManager->flush();
                $response[] = 'delete_success';
            } catch (\Exception $e) {
                $response['delete_errror'] = $e->getMessage();
            }
        }
        return $this->json($response);
    }
    
    #[Route('/{user_id}/tasks_tree/{id}', name: 'app_task_api_ task_tree', methods: ['GET'])]
    /**
     * The tree of a task with nesting tasks
     * @param Request $request
     * @param TaskRepository $taskRepository
     * 
     * @return Response
     */
    public function taksTree(Request $request, TasksTree $taskTree): Response
    {
        $user_id = $request->get('user_id');
        $id = $request->get('id');
        $criteria = ['parent_id' => $id];
        
        $tasks = $taskTree->loopTasks($criteria, "tree");

        return $this->json($tasks);
    }
    #[Route('/{user_id}/priority_by/{priority_sort}/created_by/{created_sort}', name: 'app_api_task_sortBy', methods: ['GET'])]
    /**
     * @param Request $request
     * @param TaskRepository $taskRepository
     * 
     * @return Response
     */
    public function sortBy(Request $request, TaskRepository $taskRepository): Response
    {
       
        $user_id = $request->get('user_id');
        $priority_sort = $request->get('priority_sort');
        $created_sort = $request->get('created_sort');
        $tasks = $taskRepository->sortBy($user_id, $priority_sort, $created_sort);

        /*echo "____<pre>";
        print_r($tasks);
        echo "</pre>";*/
       
        return $this->json($tasks);
    }
}
