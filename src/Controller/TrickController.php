<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Form\EditTrickFormType;
use App\Repository\TrickRepository;
use App\Repository\CategoryRepository;
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
     *
     * @param Request              $request
     * @param Trick                $trick
     * @param CategoryRepository   $categoryRepository
     * @param TrickRepository      $trickRepository
     *
     * @return Response
     */
    public function update(
        Request $request,
        Trick $trick,
        TrickRepository $trickRepository,
        CategoryRepository $categoryRepository,
        MediaUpdaterService $mediaUpdaterService
    ): Response {

        $form = $this->createForm(EditTrickFormType::class, $trick, [
            'reorderedCategory' => $categoryRepository->onTopOfList($trick->getCategory()->getId()),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedThumbnailFile */
            $uploadedThumbnailFile = $form['thumbnail']->getData();

            /** @var Media $thumbnail */
            $thumbnail = $trick->getThumbnail();

            /** @var ArrayCollection $images */
            $images = $form['images']->getData();

            /** @var ArrayCollection $videos */
            $videos = $form['videos']->getData();

            $mediaUpdaterService
                ->replaceThumbnail($uploadedThumbnailFile, $thumbnail)
                ->replaceAdditionnalImages($images)
                ->replaceVideos($videos);

            $trickRepository->update($trick);

            return $this->redirectToRoute('app_home');
        }

        return $this->render('trick-update/index.html.twig', [
            'trick'      => $trick,
            'form' => $form->createView(),
        ]);
    }
}
