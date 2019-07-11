<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
     * @Route("/")
     * @Template()
     * @return array
     */
    public function home()
    {
        $trickIndex = $this->repository->findBy(['validated' => 1], ['id' => 'DESC'], $limit = 8, $offset = 0);
        $indexSize = sizeof($trickIndex);

        return ['trickList' => $trickIndex,  'indexSize' => $indexSize];
    }

    /**
     * @Route("/show_more")
     * @Template()
     */
    public function showMore()
    {
        $trickIndex = $this->repository->showMore($_GET['last_trick_id']);
        $indexSize = sizeof($trickIndex);

        return ['trickList' => $trickIndex, 'indexSize' => $indexSize];

    }
}
