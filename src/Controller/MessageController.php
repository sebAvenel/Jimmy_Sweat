<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Trick;
use App\Repository\MessageRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MessageController
 * @package App\Controller
 * @Route("/message")
 */
class MessageController extends Controller
{
    /**
     * @var MessageRepository
     */
    private $repository;
    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(MessageRepository $repository, ObjectManager $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * @Route("/update/{id}")
     * @param Message $message
     * @return RedirectResponse
     */
    public function updateValidated(Message $message): RedirectResponse
    {
        $message->setValidated(1);
        $this->manager->flush();
        $this->addFlash('successUpdateMessage', 'Le message a bien été validé');

        return $this->redirectToRoute('app_admin_message');
    }

    /**
     * @Route("/delete/{id}", methods="DELETE")
     * @param Message $message
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Message $message, Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->get('_token'))) {
            $this->manager->remove($message);
            $this->manager->flush();
            $this->addFlash('success', 'Le message a bien été supprimé');

            return $this->redirectToRoute('app_admin_message');
        }

        $this->addFlash('failure', 'Le message n\'a pas pu être supprimé');

        return $this->redirectToRoute('app_admin_message');
    }

    /**
     * @Route("/create/{id}")
     * @param Trick $trick
     * @return Response
     */
    public function create(Trick $trick): Response
    {
        if (strlen($_POST['content']) < 10 || strlen($_POST['content']) > 500){
            $this->addFlash('failure', 'Votre message doit comporter entre 10 et 500 caractères et le votre en comporte ' . strlen($_POST['content']) );
            return $this->redirectToRoute('app_trick_show', ['_fragment' => 'message', 'id' => $trick->getId()]);
        } else {
            $user = $this->getUser();
            $message = new Message();
            $message
                ->setContent($_POST['content'])
                ->setTrick($trick)
                ->setUser($user);
            $this->manager->persist($message);
            $this->manager->flush();

            $this->addFlash('success', 'Votre message a bien été pris en compte, il sera prochainement validé ou supprimé par l\'un de nos modérateurs' );
            return $this->redirectToRoute('app_trick_show', ['_fragment' => 'message', 'id' => $trick->getId()]);
        }

        return $this->redirectToRoute('app_trick_show', ['id' => $trick->getId()]);
    }

    /**
     * @Route("/show_more")
     * @Template()
     */
    public function showMore()
    {
        $messageIndex = $this->repository->showMore($_GET['last_message_id'], $_GET['trick_id']);
        $indexSize = sizeof($messageIndex);
        $trickId = $_GET['trick_id'];

        return ['messageList' => $messageIndex, 'indexSize' => $indexSize, 'trickId' => $trickId];
    }

}
