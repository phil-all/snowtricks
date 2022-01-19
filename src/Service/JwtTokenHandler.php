<?php

namespace App\Service;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * A light handler Json Web tokens, usefull to generate, decode and check conformity, signature and expiration.
 * @package App\Service
 */
class JwtTokenHandler
{
    /**
     * @var array
     */
    private const HEADER = [
        'alg' => 'HS256',
        'typ' => 'JWT'
    ];

    /**
     * @var string
     */
    private string $token;

    /**
     * @var KernelInterface
     */
    private KernelInterface $kernel;

    /**
     * @var string
     */
    private string $key;

    /**
     * JwtTokenHandler constructor
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->setkey();
    }

    /**
     * Generates a JWT token.
     *
     * @param string $subject sub claim in token payload
     * @param string $email
     * @param integer|null $gap difference in seconds between token timestamp and expiration
     *
     * @return string
     */
    public function generateToken(string $subject, string $email, ?int $gap): string
    {
        $payload = [
            'sub' => $subject,
            'iat' => time(),
            'exp' => time() + $gap,
            'email' => $email
        ];

        $encodedHeader    = $this->encodedatas(self::HEADER);
        $encodedPayload   = $this->encodedatas($payload);
        $encodedSignature = $this->encodeSignature($encodedHeader, $encodedPayload);

        return $encodedHeader . '.' . $encodedPayload . '.' . $encodedSignature;
    }

    /**
     * Split a token in an array
     *
     * @param string $token
     *
     * @return array
     */
    public function tokenInArray(string $token): array
    {
        return explode('.', $token, 3);
    }

    /**
     * Checks if a token is valid, in analysing its compodsition, its signature,
     * its expiration date and if containing user email.
     *
     * @param string $token
     *
     * @return boolean
     */
    public function tokenChecker(string $token): bool
    {
        $this->setToken($token);

        return ($this->isJWT()
            && $this->isSignatureCorrect()
            && $this->isNotExpired()
            && $this->isUserMail()
        );
    }

    /**
     * Get user email from token payload
     *
     * @param string $token
     *
     * @return string
     */
    public function getMail(string $token): string
    {
        $payload = $this->decodeDatas($token, 1);

        return $payload['email'];
    }

    /**
     * Return a cleaned encoded string first in JSON and then in base64, used for :
     * - header
     * - payload
     *
     * @param array $array
     *
     * @return string
     */
    private function encodedatas(array $array): string
    {
        $jsonEncode = json_encode($array);

        $jsonEncode = ($jsonEncode === false) ? 'error data encoding' : $jsonEncode;

        return $this->base64Cleaner(base64_encode($jsonEncode));
    }

    /**
     * Return a cleaned encoded signature.
     *
     * @param string $encodedHeader
     * @param string $encodedPayload
     *
     * @return string
     */
    private function encodeSignature(string $encodedHeader, string $encodedPayload): string
    {
        $hash = hash_hmac(
            'sha256',
            $encodedHeader . '.' . $encodedPayload,
            base64_encode($this->key),
            true
        );

        if ($hash !== false) {
            return $this->base64Cleaner(base64_encode($hash));
        }
    }

    /**
     * Return json decoded data from token, used for :
     * - header
     * - payload
     *
     * @param string $token
     * @param integer $typeKey give wich data is concerned as folow:
     *  - 0 for header
     *  - 1 for payload
     *
     * @return mixed
     */
    private function decodeDatas(string $token, int $typeKey): mixed
    {
        $token = explode('.', $token);

        $data = mb_convert_encoding(
            $token[$typeKey],
            'UTF-8',
            'BASE64'
        );

        return json_decode($data, true);
    }

    /**
     * Clean a base64 encoded string, in replacing JWT unsuported characters as follow:
     * - '+' replace by '-'
     * - '/' replace by '_'
     * - '=' deleted
     *
     * @param string $string
     * @return string
     */
    private function base64Cleaner(string $string): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], $string);
    }

    /**
     * Checks if token composition is conform to Json Web Token.
     *
     * @return boolean
     */
    private function isJWT(): bool
    {
        return (preg_match('~^[\w\-]+\.[\w\-]+\.[\w\-]+$~', $this->token)) ? true : false;
    }

    /**
     * Checks a given token signature.
     *
     * @return boolean
     */
    private function isSignatureCorrect(): bool
    {
        $token            = $this->tokenInArray($this->token);
        $header           = $token[0];
        $payload          = $token[1];
        $givenSignature   = $token[2];
        $correctSignature = $this->encodeSignature($header, $payload);

        return ($givenSignature === $correctSignature);
    }

    /**
     * Checks if a token is expired.
     *
     * @return boolean
     */
    private function isNotExpired(): bool
    {
        $payload = $this->decodeDatas($this->token, 1);

        return (!array_key_exists('exp', $payload)) ? false : (time() < $payload['exp']);
    }

    /**
     * Checks if payload token contains an user mail.
     *
     * @return boolean
     */
    private function isUserMail(): bool
    {
        $payload = $this->decodeDatas($this->token, 1);

        return (array_key_exists('email', $payload));
    }

    /**
     * Set token.
     *
     * @param string $token
     *
     * @return void
     */
    private function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * Set signature secret key.
     *
     * @return void
     */
    private function setkey(): void
    {
        $dotenv = new Dotenv();
        $dotenv->load($this->kernel->getProjectDir() . '/.env.local');

        $this->key = (getenv('JWT_KEY') !== false) ? getenv('JWT_KEY') : '';
    }
}
