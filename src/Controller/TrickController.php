<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TrickController
 * @package App\Controller
 * @Route("/trick")
 */
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
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * TrickController constructor.
     * @param TrickRepository $trickRepository
     * @param ObjectManager $manager
     * @param Filesystem $filesystem
     */
    public function __construct(TrickRepository $trickRepository, ObjectManager $manager, Filesystem $filesystem)
    {
        $this->trickRepository = $trickRepository;
        $this->manager = $manager;
        $this->filesystem = $filesystem;
    }

    /**
     * @Route("/update/{id}")
     * @param $id
     * @return RedirectResponse
     */
    public function updateValidated($id): RedirectResponse
    {
        $trick = $this->trickRepository->find($id);
        $trick->setValidated(1);
        $this->manager->flush();

        return $this->redirectToRoute('app_admin_trick');
    }

    /**
     * @Route("/delete/{source}/{id}")
     * @param Trick $trick
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Trick $trick, Request $request, $source): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->get('_token'))) {
            $this->manager->remove($trick);
            $this->manager->flush();
            $this->addFlash('success', 'La figure "' . $trick->getName() . '"" a bien été supprimée');
            if ($source == 'admin'){
                return $this->redirectToRoute('app_admin_trick');
            }elseif ($source == 'home'){
                return $this->redirectToRoute('app_home_home');
            }
        }

        $this->addFlash('failure', 'La figure "' . $trick->getName() . '" n\'a pas pu être supprimée');

        return $this->redirectToRoute('app_admin_trick');
    }

    /**
     * @Route("/show/{id}")
     * @param int $id
     * @Template()
     * @return array
     */
    public function show(int $id)
    {
        $trick = $this->trickRepository->find($id);

        return ['trick' => $trick];
    }

    /**
     * @Route("/edit/{id}")
     * @Template()
     * @param Trick $trick
     * @param Request $request
     * @return array
     */
    public function edit(Trick $trick, Request $request)
    {
        $originalVideos = $trick->videosArrayMaker($trick);
        $images_directory = $this->getParameter('images_directory');
        $originalImages = $trick->imagesArrayMaker($trick, $images_directory);
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            if ($_POST['firstImage'] != 'null'){
                $trick->setFirstImage($_POST['firstImage']);
            };
            $trick->videosEraser($originalVideos, $trick, $this->manager);
            $trick->imagesEraser($originalImages, $trick, $this->filesystem, $images_directory, $this->manager);
            $trick->imagesManagement($trick, $images_directory);
            $trick->setValidated(0);
            $trick->setUpdatedAt(new \DateTime());
            $this->manager->flush();

            return $this->render('trick/edit.html.twig');
        }

        return ['form' => $form->createView(), 'images' => $trick->getImages()];
    }

    /**
     * @Route("/create/")
     * @Template()
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $images_directory = $this->getParameter('images_directory');
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $trick->setUser($this->getUser());
            $trick->imagesManagement($trick, $images_directory);
            $this->manager->persist($trick);
            $this->manager->flush();

            return $this->render('trick/create.html.twig');
        }

        return ['form' => $form->createView()];
    }
}
