<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(UserRepository $userRepository, ObjectManager $manager)
    {
        $this->userRepository = $userRepository;
        $this->manager = $manager;
    }

    /**
     * @Route("/user/update/{id}", name="update_user_role")
     * @param $id
     * @return RedirectResponse
     */
    public function updateRole($id): RedirectResponse
    {
        $user = $this->userRepository->find($id);
        if ($user->getRole() == 'admin'){
            $user->setRole('user');
        }

        if ($user->getRole() == 'user'){
            $user->setRole('admin');
        }

        $this->manager->flush();
        return $this->redirectToRoute('user_admin');
    }

    /**
     * @Route("/user/delete/{id}", name="delete_user", methods="DELETE")
     * @param $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete($id, Request $request): RedirectResponse
    {
        $user = $this->userRepository->find($id);
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->get('_token'))){
            $this->manager->remove($user);
            $this->manager->flush();
            $this->addFlash('success', 'L\'utilisateur ' . $user->getName() . ' a bien été supprimé');
        }

        return $this->redirectToRoute('user_admin');
    }
}