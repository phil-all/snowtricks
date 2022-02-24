<?php

namespace App\Controller;

use App\Helper\SessionTrait;
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
    use SessionTrait;

    /**
     * @Route("/lien-non-valide/erreur-400", name="app_error_visitor_link")
     *
     * @return Response
     */
    public function invalidVisitorLink(): Response
    {
        if (!$this->getUser()) {
            $this->sessionInvalidate(); // invalidate only anonymous session.
        }

        throw new BadRequestHttpException(null, null, 400);
    }
}
