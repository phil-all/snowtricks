<?php

namespace App\Controller;

use App\Entity\Trick;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SingleController extends AbstractController
{
    /**
     * @Route("/trick/{id}/{slug}", name="trick_read")
     *
     * @param Trick $trick
     * @return Response
     */
    public function index(Trick $trick): Response
    {
        return $this->render('single/index.html.twig', [
            'trick' => $trick
        ]);
    }
}
