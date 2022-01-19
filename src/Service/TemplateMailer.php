<?php

namespace App\Service;

use App\Entity\User;
use App\Service\JwtTokenHandler;
use Psr\Container\ContainerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * A service used to send templating emails.
 * @package App\Service
 */
class TemplateMailer
{
    /**
     * @var MailerInterface;
     */
    private $mailer;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(
        MailerInterface $mailer,
        ContainerInterface $container
    ) {
        $this->mailer = $mailer;
        $this->container = $container;
    }

    public function sendValidationMail(User $user, JwtTokenHandler $token)
    {
        $userMail = $user->getEmail();

        $tokenInArray = $token->tokenInArray(
            $token->generateToken('account_validation', $userMail, 3600)
        );

        $email = (new TemplatedEmail())
            ->from('team.snowtricks@example.com')
            ->to($userMail)
            ->subject('Confirmez votre inscription')
            ->htmlTemplate('emails/validation-enquiry.html.twig')
            ->context([
                'url' => $this->container->get('router')->generate(
                    'app_account_validation',
                    [
                        'header'    => $tokenInArray[0],
                        'payload'   => $tokenInArray[1],
                        'signature' => $tokenInArray[2],
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            ]);

        $this->mailer->send($email);
    }
}
