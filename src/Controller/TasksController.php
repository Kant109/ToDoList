<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;

use App\Repository\TaskRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TasksController extends AbstractController
{
    /**
     * @Route("project/{id}/tasks", name="tasks")
     */
    public function tasks(Project $project): Response
    {
        $tasks = $project->getTasks();

        return $this->render('tasks/index.html.twig', [
            'tasks' => $tasks,
            'project' => $project,
        ]);
    }

    /**
     * @Route("/project/{id}/tasks/add", methods={"GET"}, name="tasks_add")
     */
    public function tasksAdd(Project $project): Response
    {
        $users = $project->getUsers();

        return $this->render('tasks/add.html.twig', [
            'project' => $project,
            'users' => $users,
        ]);
    }

    /**
     * @Route("/project/{id}/tasks/add/save", methods={"POST"}, name="tasks_add_save")
     */
    public function tasksAddSave(ManagerRegistry $doctrine, Request $request, Project $project): Response
    {
        $entityManager = $doctrine->getManager();

        $task = new Task();

        $task->setName($request->request->get('name'));
        $task->setDescription($request->request->get('description'));
        $task->setStartDate(new \DateTime($request->request->get('start_date')));
        $task->setEndDate(new \DateTime($request->request->get('end_date')));
        $task->setProject($project);
        
        $entityManager->persist($task);
        $entityManager->flush();

        return $this->redirectToRoute('tasks', ["id" => $project->getId()]);
    }

    /**
     * @Route("/project/{project_id}/tasks/{task_id}/edit", name="tasks_edit")
     * @ParamConverter("project", options={"mapping": {"project_id": "id"}})
     * @ParamConverter("task", options={"mapping": {"task_id": "id"}})
     */
    public function tasksEdit(Project $project, Task $task): Response
    {
        return $this->render('tasks/edit.html.twig', [
            'project' => $project,
            'task' => $task,
        ]);
    }

    /**
     * @Route("/project/{project_id}/tasks/{task_id}/edit/save", methods={"POST"}, name="task_edit_save")
     * @ParamConverter("project", options={"mapping": {"project_id": "id"}})
     * @ParamConverter("task", options={"mapping": {"task_id": "id"}})
     */
    public function taskEditSave(ManagerRegistry $doctrine, Request $request, Task $task, Project $project): Response
    {
        $entityManager = $doctrine->getManager();

        $task->setName($request->request->get('name'));
        $task->setDescription($request->request->get('description'));
        $task->setStartDate(new \DateTime($request->request->get('start_date')));
        $task->setEndDate(new \DateTime($request->request->get('end_date')));

        $entityManager->persist($task);
        $entityManager->flush();

        return $this->redirectToRoute('tasks', ["id" => $project->getId()]);
    }

    
    /**
     * @Route("/project/{project_id}/tasks/{task_id}/delete", methods={"GET"}, name="task_delete")
     * @ParamConverter("project", options={"mapping": {"project_id": "id"}})
     * @ParamConverter("task", options={"mapping": {"task_id": "id"}})
     */
    public function taskDelete(ManagerRegistry $doctrine, Task $task, Project $project): Response
    {
        $entityManager = $doctrine->getManager();

        $entityManager->remove($task);
        $entityManager->flush();

        return $this->redirectToRoute('tasks', ["id" => $project->getId()]);
    }

}
