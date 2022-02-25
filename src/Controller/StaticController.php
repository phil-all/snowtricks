<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class StaticController
 * @package App\Controller
 */
class StaticController extends AbstractController
{
    /**
     * @Route("/protection-des-donnees-utilisateur", name="app_privacy", methods={"GET"})
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
