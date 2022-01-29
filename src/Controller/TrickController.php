<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\UpdateTrickFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TrickRepository as TrickRepo;
use App\Repository\CategoryRepository as CatRepo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class TrickController
 * @package App\Controller
 */
class TrickController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     *
     * @param TrickRepo $trickRepo
     *
     * @return Response
     */
    public function index(TrickRepo $trickRepo): Response
    {
        return $this->render('home/index.html.twig', [
            'tricks' => $trickRepo->findAll()
        ]);
    }

    /**
     * @Route("/trick/{id}/{slug}", name="app_trick_read")
     *
     * @param Trick $trick
     *
     * @return Response
     */
    public function read(Trick $trick): Response
    {
        $trick->addThumbnailPath();

        return $this->render('single/index.html.twig', [
            'trick' => $trick
        ]);
    }

    /**
     * @Route("/trick/modifier/{id}/{slug}", name="app_trick_update")
     *
     * @param Request   $request
     * @param Trick     $trick
     * @param CatRepo   $category
     * @param TrickRepo $trickRepo
     *
     * @return Response
     */
    public function update(Request $request, Trick $trick, CatRepo $category, TrickRepo $trickRepo): Response
    {
        $form = $this->createForm(UpdateTrickFormType::class, $trick, [
            'reorderedCategory' => $category->onTopOfList($trick->getCategory()->getId()),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickRepo->update($trick);

            return $this->redirectToRoute('app_home');
        }

        return $this->render('trick-update/index.html.twig', [
            'trick'      => $trick,
            'updateForm' => $form->createView(),
        ]);
    }
}
