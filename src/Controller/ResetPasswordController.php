<?php

namespace App\Controller;

use App\Helper\SessionTrait;
use App\Service\TemplateMailer;
use App\Service\JwtTokenHandler;
use App\Repository\UserRepository;
use App\Form\ChangePasswordFormType;
use App\Form\NewPasswordRequestFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as Hasher;

/**
 * Class ResetPasswordController
 * @package App\controller
 */
class ResetPasswordController extends AbstractController
{
    use SessionTrait;

    /**
     * @var JwtTokenHandler
     */
    private JwtTokenHandler $token;

    public function __construct(JwtTokenHandler $token)
    {
        $this->token = $token;
    }


    /**
     * @Route("/reset", name="app_reset_password_request")
     *
     * @param Request        $request
     * @param UserRepository $userRepository
     * @param TemplateMailer $mailer
     *
     * @return Response
     */
    public function request(Request $request, UserRepository $userRepository, TemplateMailer $mailer): Response
    {
        $form = $this->createForm(NewPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();

            if (null !== $userRepository->findOneByEmail($email)) {
                $mailer->sendResetPasswordRequestMail($email);

                return $this->render('messages/reset-password/sent.html.twig', ['email' => $email]);
            }

            return $this->render('messages/reset-password/failed.html.twig', []);
        }

        return $this->render('reset_password/request.html.twig', [
            'resetPassword' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset/{header}/{payload}/{signature}", name="app_reset_password_checkmail")
     *
     * @param string $header
     * @param string $payload
     * @param string $signature
     *
     * @return RedirectResponse
     */
    public function checkMail(string $header, string $payload, string $signature): RedirectResponse
    {
        $token = $header . '.' . $payload . '.' . $signature;

        if (!$this->token->tokenChecker($token)) {
            return $this->redirectToRoute('app_invalid_link');
        }

        $this->storeInSession('ResetPasswordToken', $token);

        return $this->redirectToRoute('app_update_password');
    }

    /**
     * @Route("/update-password", name="app_update_password")
     *
     * @param Request        $request
     * @param UserRepository $userRepository
     * @param Hasher         $passwordHasher
     *
     * @return Response
     */
    public function update(Request $request, UserRepository $userRepository, Hasher $passwordHasher): Response
    {
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $this->token->getMail($this->getFromSession('ResetPasswordToken'));
            $user  = $userRepository->findOneByEmail($email);

            $userRepository->changePassword($user, $passwordHasher, $form->get('plainPassword')->getData());
            $this->sessionInvalidate();

            return $this->render('messages/reset-password/done.html.twig', []);
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetPassword' => $form->createView(),
        ]);
    }
}
