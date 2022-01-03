<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     */
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('users/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/users/add", methods={"GET"}, name="users_add")
     */
    public function usersAdd(): Response
    {
        return $this->render('users/add.html.twig');
    }

    /**
     * @Route("/users/add/save", methods={"POST"}, name="users_add_save")
     */
    public function usersAddSave(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $user = new User();

        $user->setFirstName($request->request->get('first_name'));
        $user->setLastName($request->request->get('last_name'));
        $user->setEmail($request->request->get('email'));

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Your user has been added !');

        return $this->redirectToRoute('users');
    }

    /**
     * @Route("/users/edit/{id}", name="users_edit")
     */
    public function usersEdit(User $user): Response
    {
        return $this->render('users/edit.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/users/save/{id}", methods={"POST"}, name="users_save_id")
     */
    public function usersSaveId(ManagerRegistry $doctrine, Request $request, User $user): Response
    {
        $entityManager = $doctrine->getManager();

        $user->setFirstName($request->request->get('first_name'));
        $user->setLastName($request->request->get('last_name'));
        $user->setEmail($request->request->get('email'));

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Your user has been updated !');

        return $this->redirectToRoute('users');
    }

    /**
     * @Route("/users/delete/{id}", methods={"GET"}, name="users_delete_id")
     */
    public function usersDeleteId(ManagerRegistry $doctrine, User $user): Response
    {
        $entityManager = $doctrine->getManager();

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Your user has been deleted !');

        return $this->redirectToRoute('users');
    }

    /**
     * @Route("/users/{id}/projects", methods={"GET"}, name="users_projects")
     */
    public function usersProjects(User $user): Response
    {
        $projects = $user->getProjects();

        return $this->render('users/projects.html.twig', [
            'projects' => $projects,
            'user' => $user,
        ]);
    }
}
