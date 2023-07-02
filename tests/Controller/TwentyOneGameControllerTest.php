<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\SessionHelperTrait;
use App\Game\GameManager;
use App\Game\BettingManager;

/**
 * Test cases for TwentyOneGameController.
 */
class TwentyOneGameControllerTest extends WebTestCase
{
    use SessionHelperTrait;

    public function testIndex()
    {
        if (getenv('APP_ENV') !== 'dev') {
            $this->markTestSkipped(
                'This test can only be run on the local host.'
            );
        }

        $client = static::createClient();

        $client->request('GET', '/game');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Provide data sets to simulte form submitted with different bankers.
     * @return mixed[] As data set
     */
    public static function bankerProvider(): array
    {
        return [
            'Easy Banker'   => ['easy'],
            'Medium Banker' => ['medium'],
            'Hard Banker'   => ['hard'],
        ];
    }

    /**
     * Test setting up game with different bankers submitted.
     * @dataProvider bankerProvider
     */
    public function testInitBankerSuccess(string $banker)
    {
        if (getenv('APP_ENV') !== 'dev') {
            $this->markTestSkipped(
                'This test can only be run on the local host.'
            );
        }

        $client = static::createClient();

        $client->request('POST', '/game/init', [
            'banker' => $banker,
        ]);

        // Assert that the response redirects to /play if successful
        $this->assertTrue($client->getResponse()->isRedirect('/game/play'));
    }

    /**
     * Test redirects back when no valid banker has been submitted.
     */
    public function testInitBankerFail()
    {
        if (getenv('APP_ENV') !== 'dev') {
            $this->markTestSkipped(
                'This test can only be run on the local host.'
            );
        }

        $client = static::createClient();

        $client->request('POST', '/game/init', [
            'banker' => null
        ]);

        // Assert that the response redirects back to /init if failed
        $this->assertTrue($client->getResponse()->isRedirect('/game/init'));
    }

    /**
     * Provide data sets to follow both banker and player win paths.
     * @return mixed[] As data set.
     */
    public static function winProvider(): array
    {
        return [
            'Banker wins' => [-1, 'bankerWinsStake'],
            'Player wins' => [1, 'playerWinsStake']
        ];
    }

    /**
     * Test stay redirects when banker or player wins.
     * @dataProvider winProvider
     */
    public function testStay(int $hasWon, string $winMethod)
    {
        $client = static::createClient();

        // Create stubs for manager classes
        $gameManager = $this->getMockBuilder(GameManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['dealBanker', 'checkBankerWon'])
            ->getMock();

        $bettingManager = $this->getMockBuilder(BettingManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods([$winMethod, 'isBetting'])
            ->getMock();

        // Set up expectation for methods
        $gameManager->expects($this->any())
            ->method('dealBanker');

        $gameManager->expects($this->any())
            ->method('checkBankerWon')
            ->willReturn($hasWon);

        $bettingManager->expects($this->any())
            ->method('isBetting')
            ->willReturn(true);

        $bettingManager->expects($this->any())
            ->method($winMethod);

        // Create mock session
        $this->createSession($client, [
            'gameManager' => $gameManager,
            'bettingManager' => $bettingManager
        ]);

        // Make a POST request to the /game/play/stay endpoint
        $client->request('POST', '/game/play/stay');

        // Assert that the response redirects back to /game/play
        $this->assertResponseRedirects('/game/play');
    }

    /**
     * Test hit redirects when banker or player wins.
     * @dataProvider winProvider
     */
    public function testHit(int $hasWon, string $winMethod)
    {
        $client = static::createClient();

        // Create stubs for manager classes
        $gameManager = $this->getMockBuilder(GameManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['dealPlayer', 'checkPlayerWon'])
            ->getMock();

        $bettingManager = $this->getMockBuilder(BettingManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods([$winMethod, 'isBetting'])
            ->getMock();

        // Set up expectation for methods
        $gameManager->expects($this->any())
            ->method('dealPlayer');

        $gameManager->expects($this->any())
            ->method('checkPlayerWon')
            ->willReturn($hasWon);

        $bettingManager->expects($this->any())
            ->method('isBetting')
            ->willReturn(true);

        $bettingManager->expects($this->any())
            ->method($winMethod);

        // Create mock session
        $this->createSession($client, [
            'gameManager' => $gameManager,
            'bettingManager' => $bettingManager
        ]);

        // Make a POST request to the /game/play/hit endpoint
        $client->request('POST', '/game/play/hit');

        // Assert that the response redirects back to /game/play
        $this->assertResponseRedirects('/game/play');
    }
}
