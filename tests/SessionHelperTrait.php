<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;

trait SessionHelperTrait
{
    /**
     * @param KernelBrowser $clien Current client.
     * @param mixed[]       $data  Session data.
     * @return Session
     */
    public function createSession(KernelBrowser $client, array $data): Session
    {
        $session = $client->getContainer()->get('session.factory')->createSession(null);

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