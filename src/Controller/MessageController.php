<?php

namespace App\Controller;

use App\Repository\MessageRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/message/delete/{id}", name="delete_message", methods="DELETE")
     * @param $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete($id, Request $request): RedirectResponse
    {
        $message = $this->messageRepository->find($id);
        if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->get('_token'))) {
            $this->manager->remove($message);
            $this->manager->flush();
            $this->addFlash('success', 'Le message ' . $message->getId() . ' a bien été supprimé');
            return $this->redirectToRoute('message_admin');
        }

        $this->addFlash('failure', 'Le message ' . $message->getId() . ' n\'a pas pu être supprimé');
        return $this->redirectToRoute('message_admin');
    }

}