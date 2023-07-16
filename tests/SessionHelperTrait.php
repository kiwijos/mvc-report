<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\SessionFactoryInterface;

trait SessionHelperTrait
{
    /**
     * @param KernelBrowser $client Current client.
     * @param mixed[]       $data  Session data.
     * @return SessionInterface
     */
    public function createSession(KernelBrowser $client, array $data): SessionInterface
    {
        /** @var SessionFactoryInterface */
        $sessionCreator = $client->getContainer()->get('session.factory');
        $session = $sessionCreator->createSession();

        $session->setId('session-mock-id');
        $session->start();

        foreach ($data as $key => $value) {
            $session->set($key, $value);
        }

        $session->save();

        $sessionCookie = new Cookie(
            'MOCKSESSID',
            'session-mock-id',
            null,
            null,
            'localhost',
        );

        $client->getCookieJar()->set($sessionCookie);

        return $session;
    }
}
