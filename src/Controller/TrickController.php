<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Form\EditTrickFormType;
use App\Form\CreateTrickFormType;
use App\Repository\TrickRepository;
use App\Service\Entity\TrickService;
use App\Repository\CommentRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\FormInterface;
use App\Service\Entity\MediaUpdaterService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @param Request           $request
     * @param Trick             $trick
     * @param CommentRepository $commentRepository
     *
     * @return Response
     */
    public function read(Request $request, Trick $trick, CommentRepository $commentRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $options = [
            'trick' => $trick
        ];

        if (null !== $user) {
            /** @var Comment $comment */
            $comment = $commentRepository->init($user, $trick);

            $form = $this->createForm(CommentFormType::class, $comment)->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $commentRepository->persist($comment);

                return new RedirectResponse($request->headers->get('referer') . '/#comment');
            }

            $options['form'] = $form->createView();
        }

        return $this->render('single/index.html.twig', $options);
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
        TrickService $trickService,
        TrickRepository $trickRepository,
        CategoryRepository $categoryRepository,
        MediaUpdaterService $mediaUpdaterService
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        /** @var Trick $trick */
        $trick = null;

        /** @var FormInterface $form */
        $form = null;

        /** @var string $template */
        $template = null;

        if ($request->attributes->get('_route') === 'app_trick_create') {
            $trick = $trickService->setNew($user);

            $template = 'trick-create/index.html.twig';

            $form = $this->createForm(CreateTrickFormType::class, $trick)->handleRequest($request);
        }

        if ($request->attributes->get('_route') === 'app_trick_update') {
            $trick = $trickRepository->find(
                $request->attributes->get('_route_params')['id']
            );

            $template = 'trick-update/index.html.twig';

            $form = $this
                ->createForm(EditTrickFormType::class, $trick, [
                    'reorderedCategory' => $categoryRepository->onTopOfList($trick->getCategory()->getId()),
                ])
                ->handleRequest($request);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedThumbnailFile */
            $uploadedThumbnailFile = $form['thumbnail']->getData();

            /** @var ArrayCollection $images */
            $images = $form['images']->getData();

            /** @var ArrayCollection $videos */
            $videos = $form['videos']->getData();

            $mediaUpdaterService
                ->defineTrick($trick)
                ->updateMedias($uploadedThumbnailFile, $images, $videos);

            $trickService->update($trick);

            return $this->redirectToRoute('app_home');
        }

        return $this->render($template, [
            'trick' => $trick,
            'form'  => $form->createView(),
        ]);
    }

    /**
     * @Route("/trick/delete/{id}/{slug}", name="app_trick_delete")
     *
     * @return RedirectResponse
     */
    public function delete(Trick $trick, TrickService $trickService): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $trickService->delete($trick);

        return $this->redirectToRoute('app_home');
    }
}
