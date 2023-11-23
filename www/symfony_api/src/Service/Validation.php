<?php

namespace App\Service;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * [Validation of crud OPENAPI]
 */
class Validation
{
    /**
     * @param string $condition
     * @param null|int $user_id
     * 
     * @return Collection
     */
    public static function getConstrains($condition = "all", $user_id = null): Collection
    {
        if ($condition == "edit") {
            $constraints = new Assert\Collection([
                'title' => [
                    new Assert\NotBlank()
                ],
                'description' => [
                    new Assert\NotBlank()
                ],
                'user_id' => [
                    //new Assert\Optional()
                    new Assert\IdenticalTo($user_id)
                ],
                'priority' => [
                    new Assert\Type('int'),
                    new Assert\GreaterThan(0),
                    new Assert\LessThanOrEqual(5)
                ],
                'status' => [
                    new Assert\Choice(['todo', 'done'])
                ],
                'parent_id' => [
                    new Assert\Optional()
                ]
            ]);
        } else if ($condition == "delete") {
            $constraints = new Assert\Collection([
                'user_id' => [
                    new Assert\IdenticalTo($user_id)
                ],
                'status' => [
                    new Assert\IdenticalTo("todo")
                ],
            ]);
        } else if ($condition == "status") {
            $constraints = new Assert\Collection([
                'id' => [
                    new Assert\NotBlank()
                ],
                'user_id' => [
                    new Assert\IdenticalTo($user_id)
                ],
                'status' => [
                    new Assert\Optional()
                ],
            ]);
        } else {
            $constraints = new Assert\Collection([
                'title' => [
                    new Assert\NotBlank()
                ],
                'description' => [
                    new Assert\NotBlank()
                ],
                'user_id' => [
                    new Assert\NotBlank()
                ],
                'priority' => [
                    new Assert\Type('int'),
                    new Assert\GreaterThan(0),
                    new Assert\LessThanOrEqual(5)
                ],
                'status' => [
                    new Assert\Choice(['todo', 'done'])
                ],
                'parent_id' => [
                    new Assert\Optional()
                ]
            ]);
        }
        return  $constraints;
    }
}