<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\TemplateMailer;
use App\Service\JwtTokenHandler;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class RegistrationController
 * @package App\Controller
 */
class RegistrationController extends AbstractController
{
    /**
     * @var JwtTokenHandler
     */
    private JwtTokenHandler $token;

    /**
     * RegistrationController constructor
     *
     * @param JwtTokenHandler $token
     */
    public function __construct(JwtTokenHandler $token)
    {
        $this->token = $token;
    }

    /**
     * @Route("/inscription", name="app_register")
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @param TemplateMailer $mailer
     * @param UserPasswordHasherInterface $passwordHasher
     *
     * @return Response
     */
    public function register(
        Request $request,
        UserRepository $userRepository,
        TemplateMailer $mailer,
        UserPasswordHasherInterface $passwordHasher
    ): Response {

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->createPendingUser($user, $passwordHasher, $form->get('plainPassword')->getData());

            $mailer->sendValidationMail($user, $this->token);

            return $this->render('messages/waiting-validation-account.html.twig', [
                'email' => $user->getEmail(),
            ]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/validation/{header}/{payload}/{signature}", name="app_account_validation")
     *
     * @param string $header
     * @param string $payload
     * @param string $signature
     *
     * @return Response
     */
    public function validation(
        string $header,
        string $payload,
        string $signature,
        UserRepository $userRepository
    ): Response {

        $token = $header . '.' . $payload . '.' . $signature;

        if (!$this->token->tokenChecker($token)) {
            return $this->render('messages/invalid-link.html.twig', []);
        }

        $email  = $this->token->getMail($token);
        $user   = $userRepository->findOneByEmail($email);
        $status = $user->getStatus();

        if ($status->getId() !== 1) {
            return $this->render('messages/user-already-validated.html.twig', []);
        }

        $userRepository->userActivation($user);

        return $this->render('messages/account-confirmation.html.twig', []);
    }
}
