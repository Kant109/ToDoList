<?php

namespace App\Controller;

use App\Repository\TaskRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TasksController extends AbstractController
{
    /**
     * @Route("/tasks", name="tasks")
     */
    public function tasks(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();

        return $this->render('tasks/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }
}
