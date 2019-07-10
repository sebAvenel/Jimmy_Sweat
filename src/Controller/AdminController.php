<?php

namespace App\Controller;

use App\Repository\MessageRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package App\Controller
 * @Route("/admin")
 */
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
     * @Route("/tricks")
     * @Template()
     */
    public function trick()
    {
        return ['invalidTrickList' => $this->trickRepository->findInvalid()];
    }

    /**
     * @Route("/messages")
     * @Template()
     */
    public function message()
    {
        return [
            'trickList' => $this->trickRepository->findAll(),
            'invalidMessageList' => $this->messageRepository->findInvalid()
        ];
    }

    /**
     * @Template()
     * @Route("/users")
     */
    public function user()
    {
        return ['userList' => $this->userRepository->findAll()];
    }
}
