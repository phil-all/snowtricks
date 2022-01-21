<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Usefull tools for session.
 * @package App\Helper
 */
trait SessionTrait
{
    /**
     * Gets the current session.
     *
     * @return SessionInterface
     */
    private function getCurrentSession(): SessionInterface
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();

        return $request->getSession();
    }

    /**
     * Stores an attribute in the session for later reuse.
     *
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    private function storeInSession(string $name, string $value): void
    {
        $this->getCurrentSession()->set($name, $value);
    }

    /**
     * Return a session attribute.
     *
     * @param string $name
     *
     * @return string|null
     */
    private function getFromSession(string $name): ?string
    {
        return $this->getCurrentSession()->get($name);
    }

    /**
     * Invcalidates the current session
     *
     * @return void
     */
    private function sessionInvalidate(): void
    {
        $this->getCurrentSession()->invalidate();
    }
}
