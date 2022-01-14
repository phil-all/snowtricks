<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StaticController
 * @package App\Controller
 */
class StaticController extends AbstractController
{
    /**
     * @Route("/protection-des-donnees-utilisateur", name="privacy")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('static/privacy.html.twig', [
            'controller_name' => 'StaticController',
        ]);
    }
}
