<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class ErrorPagesController
 * @package App\Controller
 */
class ErrorPagesController extends AbstractController
{
    /**
     * @Route("/erreur-400", name="app_invalid_link")
     *
     * @return Response
     */
    public function invalidLink(): Response
    {
        //return $this->render('messages/invalid-link.html.twig', []);
        throw new BadRequestHttpException('Le lien que vous avez suivi est expiré ou invalide', null, 400);
    }
}
