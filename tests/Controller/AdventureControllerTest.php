<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\SessionHelperTrait;
use App\Adventure\Log;
use App\Adventure\Game;

/**
 * Application tests for AdventureController.
 */
class AdventureControllerTest extends WebTestCase
{
    use SessionHelperTrait;

    private $client;
    private $session;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->session = $this->createSession($this->client, []);
    }

    protected function tearDown(): void
    {
        $this->session->invalidate();
        parent::tearDown();
    }

    /**
     * Test case for start route.
     */
    public function testStart()
    {
        $this->client->request('GET', '/proj/game');

        // Assert successfull
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.log', 'Welcome!');
    }

    /**
     * Test case for init route.
     */
    public function testInit()
    {
        $this->client->request('GET', '/proj/game/init');

        // Assert redirect
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/proj/game/location');
    }

    /**
     * Test case for render location route.
     */
    public function testRenderLocation()
    {
        $expected = 'Welcome!';

        // Create and configure log mock
        $log = $this->createMock(Log::class);
        $log->expects($this->any())
            ->method('getEntries')
            ->willReturn([$expected]);


        $this->session->set('game_log', $log);
        $this->session->save();

        $this->client->request('GET', '/proj/game/location');

        // Assert request is successful
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Assert the rendered page content contains the expected entry
        $this->assertSelectorTextContains('.log', $expected);
    }

    /**
     * Test case for perform action route.
     */
    public function testPerformAction()
    {
        $input = 'test';
        $response = 'This is a test.';

        // Create and configure game mock
        $game = $this->createMock(Game::class);
        $game->method('processAction')
            ->with($input)
            ->willReturn($response);

        // Add game mock to mock session
        $this->session->set('game', $game);
        $this->session->set('game_log', new Log()); // Use a real log object to retain added entries for later
        $this->session->save();

        $this->client->request('POST', '/proj/game/action', ['input' => $input]);

        // Assert redirect
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/proj/game/location');

        $this->client->followRedirect(); // Follow the redirect

        // Retrieve the real log and check it contains the expected entries
        // (Meaning both the input and the response from the game mock instance)
        $log = $this->session->get('game_log');
        $this->assertSame([$input, $response], $log->getEntries());

        // Assert the rendered page content also contains the expected entries
        $this->assertSelectorTextContains('.log', $input);
        $this->assertSelectorTextContains('.log', $response);
    }

    /**
     * Test case for action route with 'restart'.
     */
    public function testActionRouteWithResetSessionRedirect()
    {
        $this->client->request('POST', '/proj/game/action', ['input' => 'restart']);

        // Assert redirect back to init route
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/proj/game/init');
    }
}
