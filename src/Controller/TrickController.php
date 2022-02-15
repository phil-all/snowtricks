<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Media;
use App\Entity\Trick;
use App\Form\EditTrickFormType;
use App\Form\CreateTrickFormType;
use App\Repository\TrickRepository;
use App\Repository\CategoryRepository;
use App\Service\Entity\TrickInitService;
use App\Service\Entity\MediaUpdaterService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @param TrickRepository $trickRepository
     *
     * @return Response
     */
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'tricks' => $trickRepository->findAll()
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
        return $this->render('single/index.html.twig', [
            'trick' => $trick
        ]);
    }

    /**
     * @Route("/trick/modifier/{id}/{slug}", name="app_trick_update")
     * @Route("/create", name="app_trick_create")
     *
     * @param Request              $request
     * @param CategoryRepository   $categoryRepository
     * @param TrickRepository      $trickRepository
     *
     * @return Response
     */
    public function edit(
        Request $request,
        TrickRepository $trickRepository,
        CategoryRepository $categoryRepository,
        MediaUpdaterService $mediaUpdaterService
    ): Response {

        /** @var User $user */
        $user = $this->getUser();

        /** @var Trick $trick */
        $trick = (new TrickInitService())->setNew($user);

        $form = $this->createForm(CreateTrickFormType::class, $trick);

        if ($request->attributes->get('_route') === 'app_trick_update') {
            /** @var int $trickId */
            $trickId = $request->attributes->get('_route_params')['id'];

            /** @var Trick $trick */
            $trick = $trickRepository->find($trickId);

            $form = $this->createForm(EditTrickFormType::class, $trick, [
                'reorderedCategory' => $categoryRepository->onTopOfList($trick->getCategory()->getId()),
            ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedThumbnailFile */
            $uploadedThumbnailFile = $form['thumbnail']->getData();

            /** @var ArrayCollection $images */
            $images = $form['images']->getData();

            /** @var ArrayCollection $videos */
            $videos = $form['videos']->getData();

            $mediaUpdaterService
                ->defineTrick($trick)
                ->setThumbnail($uploadedThumbnailFile)
                ->setAdditionnalImages($images)
                ->setVideos($videos);

            $trickRepository->update($trick);

            return $this->redirectToRoute('app_home');
        }

        return $this->render('trick-update/index.html.twig', [
            'trick' => $trick,
            'form'  => $form->createView(),
        ]);
    }
}
