<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends Controller
{
    /**
     * @var TrickRepository
     */
    private $trickRepository;
    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(TrickRepository $trickRepository, ObjectManager $manager)
    {
        $this->trickRepository = $trickRepository;
        $this->manager = $manager;
    }

    /**
     * @Route("/trick/update/{id}", name="update_validated_trick")
     * @param $id
     * @return RedirectResponse
     */
    public function updateValidated($id): RedirectResponse
    {
        $trick = $this->trickRepository->find($id);
        $trick->setValidated(1);
        $this->manager->flush();
        return $this->redirectToRoute('trick_admin');
    }

    /**
     * @Route("/trick/delete/{id}", name="delete_trick")
     * @param $id
     * @return RedirectResponse
     */
    public function delete($id): RedirectResponse
    {
        $trick = $this->trickRepository->find($id);
        $this->manager->remove($trick);
        $this->manager->flush();
        return $this->redirectToRoute('trick_admin');
    }
}