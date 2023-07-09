<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Notification\ContactNotification;
use App\Repository\TrickRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @var TrickRepository
     */
    private $repository;

    public function __construct(TrickRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/", name="app_home_home")
     * @Template()
     * @param Request $request
     * @param ContactNotification $notification
     * @return array
     */
    public function home(Request $request, ContactNotification $notification)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $notification->notify($contact);
            $this->addFlash('successSendMail', 'Votre email a bien été envoyé');
            return $this->redirectToRoute('app_home_home', ['_fragment' => 'contact']);
        }
        $trickIndex = $this->repository->findBy(['validated' => 1], ['id' => 'DESC'], $limit = 8, $offset = 0);
        $indexSize = sizeof($trickIndex);

        return ['trickList' => $trickIndex,  'indexSize' => $indexSize, 'form' => $form->createView()];
    }

    /**
     * @Route("/show_more")
     * @Template()
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function showMore()
    {
        if (!isset($_GET['group'])){
            $_GET['group'] = ' > 0';
        }else{
            $_GET['group'] = ' = ' .$_GET['group'];
        }
        $trickIndex = $this->repository->showMore($_GET['last_trick_id'], $_GET['group']);
        $indexSize = sizeof($trickIndex);

        return ['trickList' => $trickIndex, 'indexSize' => $indexSize];
    }

    /**
     * @Route("/show_by_group/{group}")
     * @Template()
     * @param $group
     * @param Request $request
     * @param ContactNotification $notification
     * @return array
     */
    public function byGroup($group, Request $request, ContactNotification $notification)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $notification->notify($contact);
            $this->addFlash('successSendMail', 'Votre email a bien été envoyé');
            return $this->redirectToRoute('app_home_bygroup', ['group' => $group, '_fragment' => 'contact']);
        }
        $trickIndex = $this->repository->findBy(['validated' => 1, 'groups' => $group], ['id' => 'DESC'], $limit = 8, $offset = 0);
        $indexSize = sizeof($trickIndex);

        return ['trickList' => $trickIndex,  'indexSize' => $indexSize, 'group' => $group, 'form' => $form->createView()];
    }
}
