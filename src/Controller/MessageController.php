<?php

namespace App\Controller;

use App\Repository\MessageRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends Controller
{
    /**
     * @var MessageRepository
     */
    private $messageRepository;
    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(MessageRepository $messageRepository, ObjectManager $manager)
    {
        $this->messageRepository = $messageRepository;
        $this->manager = $manager;
    }

    /**
     * @Route("/message/update/{id}", name="update_validated_message")
     * @param $id
     * @return RedirectResponse
     */
    public function updateValidated($id): RedirectResponse
    {
        $message = $this->messageRepository->find($id);
        $message->setValidated(1);
        $this->manager->flush();
        return $this->redirectToRoute('message_admin');
    }

    /**
     * @Route("/message/delete/{id}", name="delete_message")
     * @param $id
     * @return RedirectResponse
     */
    public function delete($id): RedirectResponse
    {
        $message = $this->messageRepository->find($id);
        $this->manager->remove($message);
        $this->manager->flush();
        return $this->redirectToRoute('message_admin');
    }

}