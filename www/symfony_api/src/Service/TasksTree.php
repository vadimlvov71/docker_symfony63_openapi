<?php

namespace App\Service;

use App\Repository\TaskRepository;
use App\Collection\StatusCollection;

/**
 * [Description TasksTree]
 */
class TasksTree
{
    private $taskRepository;
    public static $status = [];

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }
    /**
     * Find children tasks with status = "todo"
     * @param int $id
     * 
     * @return [string] as 'ok' or a list of id
     */
    public function findChildrenTasksTodo(int $id): string
    {
        $criteria = ['parent_id' => $id];
        $tasks = $this->loopTasks($criteria, "status");
        $result = "ok";
        if (count($tasks) > 0) {
            $tasks = array_unique($tasks);
            $result = "this task has nesting tasks: ".implode(", ", $tasks). " with status: todo";
        }
        return $result;
    }
    /**
     * Create an array with an unlimited nesting of sub tasks 
     * @param array $criteria
     * @param string $type
     * @param array $parents
     * 
     * @return [array]
     */
    public function loopTasks(array $criteria, string $type, array $parents = []): array 
    {
        $children = [];
        //$status = [];
        $x = 0;
        if (count($parents) == 0) {
            $parents = $this->taskRepository->findBy($criteria);
        } else {
            $x++;
            $children = $this->taskRepository->findBy($criteria);
        }
        
        if (count($parents) > 0) {
            $parent_ids = [];
            foreach ($parents as $parent) {
                $parent_ids[] = $parent->getId();
                $temp = [];
                
                if ($parent->getStatus() == "todo") {
                    //echo $parent->getId()." status: ".$parent->getStatus()."<br>";
                    self::$status[] = $parent->getId();
                }
                
                if (count($children) > 0) {
                    $parent_ids = [];
                    $parent_criteria = [];
                    $parents = [];
                    foreach ($children as $child) {
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

            $parent_criteria = ['parent_id' =>  $parent_ids];
            if (count($parent_ids) > 0) {
                $this->loopTasks($parent_criteria, $type, $parents);
            }
        }
        if ($type == "tree") {
            return $parents;
        }
        return self::$status;
    }
}