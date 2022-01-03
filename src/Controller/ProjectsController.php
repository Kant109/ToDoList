<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;

use Doctrine\Persistence\ManagerRegistry;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectsController extends AbstractController
{
    /**
     * @Route("/projects", name="projects")
     */
    public function projects(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();

        return $this->render('projects/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * @Route("/projects/add", methods={"GET"}, name="projects_add")
     */
    public function projectsAdd(): Response
    {
        return $this->render('projects/add.html.twig');
    }

    /**
     * @Route("/projects/add/save", methods={"POST"}, name="projects_add_save")
     */
    public function projectsAddSave(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $project = new Project();

        $project->setName($request->request->get('name'));
        $project->setDescription($request->request->get('description'));
        $project->setStartDate(new \DateTime($request->request->get('start_date')));
        $project->setEndDate(new \DateTime($request->request->get('end_date')));
        
        $entityManager->persist($project);
        $entityManager->flush();

        $this->addFlash('success', 'Your project has been added !');

        return $this->redirectToRoute('projects');
    }

        /**
     * @Route("/projects/edit/{id}", name="projects_edit")
     */
    public function projectsEdit(Project $project): Response
    {
        $users = $project->getUsers();

        return $this->render('projects/edit.html.twig', [
            'project' => $project,
            'users' => $users,
        ]);
    }

    /**
     * @Route("/projects/save/{id}", methods={"POST"}, name="projects_save_id")
     */
    public function projectsSaveId(ManagerRegistry $doctrine, Request $request, Project $project): Response
    {
        $entityManager = $doctrine->getManager();

        $project->setName($request->request->get('name'));
        $project->setDescription($request->request->get('description'));
        $project->setStartDate(new \DateTime($request->request->get('start_date')));
        $project->setEndDate(new \DateTime($request->request->get('end_date')));
        
        $entityManager->persist($project);
        $entityManager->flush();

        $this->addFlash('success', 'Your project has been added !');

        return $this->redirectToRoute('projects');
    }

    /**
     * @Route("/projects/delete/{id}", methods={"GET"}, name="projects_delete_id")
     */
    public function projectsDeleteId(ManagerRegistry $doctrine, Project $project): Response
    {
        $entityManager = $doctrine->getManager();

        $entityManager->remove($project);
        $entityManager->flush();

        $this->addFlash('success', 'Your project has been deleted !');

        return $this->redirectToRoute('projects');
    }

    /**
     * @Route("/projects/{id}/user", methods={"GET"}, name="projects_addUser")
     */
    public function projectsAddUser(UserRepository $userRepository, Project $project): Response
    {
        $users = $userRepository->findAll();

        $projectUsers = $project->getUsers();

        return $this->render('projects/addUser.html.twig', [
            'project' => $project,
            'users' => $users,
            'projectUsers' => $projectUsers,
        ]);
    }

    /**
     * @Route("/projects/{id}/user/save", methods={"POST"}, name="projects_addUser_save")
     */
    public function projectsAddUserSave(LoggerInterface $logger, ManagerRegistry $doctrine, UserRepository $userRepository, Request $request, Project $project): Response
    {
        $entityManager = $doctrine->getManager();

        $project->clearUsers();

        $listIdUser = $request->request->get('user_id', []);

        $logger->debug("valeur userId", ["userId"=>$listIdUser]);
        
        foreach($listIdUser as $userId) {
            $user = $userRepository->find($userId);
            $project->addUser($user);
        }

        $entityManager->persist($project);
        $entityManager->flush();

        return $this->redirectToRoute('projects');
    }
}
