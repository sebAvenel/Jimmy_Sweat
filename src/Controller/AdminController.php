<?php

namespace App\Controller;

use App\Repository\MessageRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends Controller
{
    /**
     * @var TrickRepository
     */
    private $trickRepository;
    /**
     * @var MessageRepository
     */
    private $messageRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * AdminController constructor.
     * @param TrickRepository $trickRepository
     * @param MessageRepository $messageRepository
     * @param UserRepository $userRepository
     */
    public function __construct(TrickRepository $trickRepository, MessageRepository $messageRepository, UserRepository $userRepository)
    {
        $this->trickRepository = $trickRepository;
        $this->messageRepository = $messageRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @return Response
     * @Route("/admin/tricks", name="trick_admin")
     */
    public function trickAdminPage(): Response
    {
        return $this->render('admin/trickAdmin.html.twig', [
            'invalidTrickList' => $this->trickRepository->findInvalid()
        ]);
    }

    /**
     * @return Response
     * @Route("/admin/messages", name="message_admin")
     */
    public function messageAdminPage(): Response
    {
        return $this->render('admin/messageAdmin.html.twig', [
            'trickList' => $this->trickRepository->findAll(),
            'invalidMessageList' => $this->messageRepository->findInvalid()
        ]);
    }

    /**
     * @return Response
     * @Route("/admin/users", name="user_admin")
     */
    public function userAdminPage(): Response
    {
        return $this->render('admin/userAdmin.html.twig', [
            'userList' => $this->userRepository->findAll()
        ]);
    }
}