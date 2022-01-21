<?php

namespace App\Service;

use Symfony\Component\Mime\Address;
use Psr\Container\ContainerInterface;
use App\Service\JwtTokenHandler as Token;
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
    private $container;

    /**
     * @var Token
     */
    private Token $token;

    /**
     * TemplateMailer constructor
     *
     * @param MailerInterface    $mailer
     * @param ContainerInterface $container
     * @param Token              $jwtTokenHandler
     */
    public function __construct(MailerInterface $mailer, ContainerInterface $container, Token $jwtTokenHandler)
    {
        $this->mailer    = $mailer;
        $this->container = $container;
        $this->token     = $jwtTokenHandler;
    }

    /**
     * Send a templated mail for account validation
     *
     * @param string $email
     *
     * @return void
     */
    public function sendValidationMail(string $email): void
    {
        $this->mailer->send($this->getAccountValidationTemplate($email));
    }

    /**
     * Send a templated mail for reset password
     *
     * @param string $email
     *
     * @return void
     */
    public function sendResetPasswordRequestMail(string $email): void
    {
        $this->mailer->send($this->getResetPasswordTemplate($email));
    }

    /**
     * Set account validation request template
     *
     * @param string $email
     *
     * @return TemplatedEmail
     */
    private function getAccountValidationTemplate(string $email): TemplatedEmail
    {
        return $this->setTemplate($email, [
            'subject' => 'Confirmez votre inscription',
            'path'    => 'emails/validation-enquiry.html.twig',
            'target'  => 'app_account_validation'
        ]);
    }

    /**
     * Set reset password request template
     *
     * @param string $email
     *
     * @return TemplatedEmail
     */
    private function getResetPasswordTemplate(string $email): TemplatedEmail
    {
        return $this->setTemplate($email, [
            'subject' => 'Réinitialisation de votre mot de passe',
            'path'    => 'emails/new-password-request.html.twig',
            'target'  => 'app_reset_password_checkmail'
        ]);
    }

    /**
     * Set templated email to be sent
     *
     * @param string $email
     * @param array  $details
     *
     * @return TemplatedEmail
     */
    private function setTemplate(string $email, array $details): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->from(new Address('team.snowtricks@example.com', 'Snowtricks Support'))
            ->to($email)
            ->subject($details['subject'])
            ->htmlTemplate($details['path'])
            ->context($this->setUrlLink($details['target'], $email));
    }

    /**
     * Define an array to context containing url link details
     *
     * @param string  $routeName
     * @param string  $email
     * @param integer $duration token validity in seconds
     *
     * @return array
     */
    private function setUrlLink(string $routeName, string $email, int $duration = 3600): array
    {
        $subject = preg_replace('/app_/', '', $routeName);
        $subject = (is_string($subject)) ? $subject : $routeName;
        $params  = $this->token->tokenInArray($this->token->generateToken($subject, $email, $duration));

        return [
            'url' =>
            $this->container->get('router')->generate(
                $routeName,
                [
                    'header'    => $params[0],
                    'payload'   => $params[1],
                    'signature' => $params[2],
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ];
    }
}
