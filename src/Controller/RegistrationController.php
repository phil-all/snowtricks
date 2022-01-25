<?php

namespace App\Controller;

use App\Entity\User;
use App\Helper\SessionTrait;
use App\Service\TemplateMailer;
use App\Service\JwtTokenHandler;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as Hasher;

/**
 * Class RegistrationController
 * @package App\Controller
 */
class RegistrationController extends AbstractController
{
    use SessionTrait;

    /**
     * @var JwtTokenHandler
     */
    private JwtTokenHandler $token;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * RegistrationController constructor
     *
     * @param JwtTokenHandler $token
     * @param UserRepository  $userRepository
     */
    public function __construct(JwtTokenHandler $token, UserRepository $userRepository)
    {
        $this->token          = $token;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/inscription", name="app_register")
     *
     * @param Request        $request
     * @param TemplateMailer $mailer
     * @param Hasher         $passwordHasher
     *
     * @return Response
     */
    public function register(Request $request, TemplateMailer $mailer, Hasher $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $user->getEmail();
            $this->userRepository->createPendingUser($user, $passwordHasher, $form->get('plainPassword')->getData());
            $mailer->sendValidationMail($email);

            return $this->render('messages/registration/waiting-validation-account.html.twig', [
                'email' => $email,
            ]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/inscription/{header}/{payload}/{signature}", name="app_register_checkmail")
     *
     * @param string $header
     * @param string $payload
     * @param string $signature
     *
     * @return RedirectResponse
     */
    public function checkMail(string $header, string $payload, string $signature): RedirectResponse
    {
        $token = implode('.', func_get_args());
        $this->storeInSession('RegisterToken', $token);

        return ($this->token->tokenChecker($token))
            ? $this->redirectToRoute('app_register_validation')
            : $this->redirectToRoute('app_error_visitor_link');
    }

    /**
     * @Route("/inscription/validation", name="app_register_validation")
     *
     * @return Response
     */
    public function validation(): Response
    {
        $email = $this->token->getMail($this->getFromSession('RegisterToken'));
        $user   = $this->userRepository->findOneByEmail($email);
        $status = $user->getStatus();

        if ($status->getId() !== 1) {
            return $this->render('messages/registration/user-already-validated.html.twig', []);
        }

        $this->userRepository->userActivation($user);
        $this->sessionInvalidate();

        return $this->render('messages/registration/account-confirmation.html.twig', []);
    }
}
