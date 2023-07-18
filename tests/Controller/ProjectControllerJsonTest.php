<?php

use App\Adventure\Log;
use App\Tests\DatabaseHelperTrait;
use App\Tests\SessionHelperTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProjectControllerJsonTest extends WebTestCase
{
    use DatabaseHelperTrait;
    use SessionHelperTrait;

    /**
     * @var KernelBrowser
     */
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    /**
     * Test case for locations route with no ID specified.
     */
    public function testLocations(): void
    {
        $this->client->request('GET', '/proj/api/locations');

        /** @var string*/
        $content = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($content);

        // Perform additional assertions on the response content
        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
        $this->assertNotEmpty($responseData);
    }

    /**
     * Test case for locations route with no ID specified and no data available.
     */
    public function testLocationsNotFound(): void
    {
        /** @var EntityManagerInterface */
        $entityManager = $this->getEntityManager('game');
        $this->truncateTable($entityManager, 'location'); // Empty table

        $this->client->request('GET', '/proj/api/locations');

        /** @var string*/
        $content = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJson($content);

        // Perform additional assertions on the response content
        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('error', $responseData);
    }

    /**
     * Test case for locations route with ID.
     */
    public function testLocationById(): void
    {
        if (getenv('APP_ENV') !== 'test') {
            $this->markTestSkipped(
                'This test can only be run on the test environment.'
            );
        }

        $expected = [
            'id' => 11,
            'name' => 'Cave',
            'description' => 'A dark cave.',
            'details' => 'The cave is damp and cold.'
        ];

        $this->client->request('GET', '/proj/api/locations/11');

        /** @var string*/
        $content = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($content);

        // Perform additional assertions on the response content
        $responseData = json_decode($content, true);
        $this->assertSame($expected, $responseData);
    }

    /**
     * Test case for locations route with ID not found.
     */
    public function testLocationByIdNotFound(): void
    {
        if (getenv('APP_ENV') !== 'test') {
            $this->markTestSkipped(
                'This test can only be run on the test environment.'
            );
        }

        $this->client->request('GET', '/proj/api/locations/55'); // No location with ID 55

        /** @var string*/
        $content = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJson($content);

        // Perform additional assertions on the response content
        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('error', $responseData);
    }

    /**
     * Test case for items route with no ID specified.
     */
    public function testItems(): void
    {
        $this->client->request('GET', '/proj/api/items');

        /** @var string*/
        $content = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($content);

        // Perform additional assertions on the response content
        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
        $this->assertNotEmpty($responseData);
    }

    /**
     * Test case for items route with no ID specified and no data available.
     */
    public function testItemsNotFound(): void
    {
        /** @var EntityManagerInterface */
        $entityManager = $this->getEntityManager('game');
        $this->truncateTable($entityManager, 'item'); // Empty table

        $this->client->request('GET', '/proj/api/items');

        /** @var string*/
        $content = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJson($content);

        // Perform additional assertions on the response content
        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('error', $responseData);
    }

    /**
     * Test case for items route with ID.
     */
    public function testItemById(): void
    {
        if (getenv('APP_ENV') !== 'test') {
            $this->markTestSkipped(
                'This test can only be run on the test environment.'
            );
        }

        $expected = [
            'id' => 99,
            'name' => 'sword',
            'locationId' => 11,
            'description' => 'A powerful sword.'
        ];

        $this->client->request('GET', '/proj/api/items/99');

        /** @var string*/
        $content = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($content);

        // Perform additional assertions on the response content
        $responseData = json_decode($content, true);
        $this->assertSame($expected, $responseData);
    }

    /**
     * Test case for items route with ID not found.
     */
    public function testItemByIdNotFound(): void
    {
        if (getenv('APP_ENV') !== 'test') {
            $this->markTestSkipped(
                'This test can only be run on the test environment.'
            );
        }

        $this->client->request('GET', '/proj/api/items/55'); // No item with ID 55

        /** @var string*/
        $content = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJson($content);

        // Perform additional assertions on the response content
        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('error', $responseData);
    }

    public function testLog(): void
    {
        $session = $this->createSession($this->client, []);

        // Create and configure log mock
        $log = $this->createMock(Log::class);
        $log->expects($this->any())
            ->method('getEntries')
            ->willReturn(['This is a test log']);

        $session->set('game_log', $log);
        $session->save();

        $this->client->request('GET', '/proj/api/log');

        /** @var string*/
        $content = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($content);

        // Perform additional assertions on the response content
        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
        $this->assertContains('This is a test log', $responseData);
    }

    public function testLogNoEntriesFound(): void
    {
        $session = $this->createSession($this->client, []);

        // Create and configure log mock
        $log = $this->createMock(Log::class);
        $log->expects($this->any())
            ->method('getEntries')
            ->willReturn([]); // No logged entries

        $session->set('game_log', $log);
        $session->save();

        $this->client->request('GET', '/proj/api/log');

        /** @var string*/
        $content = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJson($content);

        // Perform additional assertions on the response content
        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('error', $responseData);
    }

    /**
     * Test case for connect route with successful request.
     */
    public function testConnect(): void
    {
        if (getenv('APP_ENV') !== 'test') {
            $this->markTestSkipped(
                'This test can only be run on the test environment.'
            );
        }

        $this->client->request('POST', '/proj/api/connect', [
            'from_location' => '22',
            'to_location' => '11',
            'direction' => 'south'
        ]);

        /** @var string*/
        $content = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJson($content);

        // Perform additional assertions on the response content
        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
        $this->assertEquals('Locations connected successfully', $responseData['message']);
    }

    /**
     * Test case for connect route with same location id result in bad request.
     */
    public function testConnectSameLocation(): void
    {
        if (getenv('APP_ENV') !== 'test') {
            $this->markTestSkipped(
                'This test can only be run on the test environment.'
            );
        }

        $this->client->request('POST', '/proj/api/connect', [
            'from_location' => '11',
            'to_location' => '11',
            'direction' => 'north'
        ]);

        /** @var string*/
        $content = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJson($content);

        // Perform additional assertions on the response content
        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
        $this->assertEquals('Cannot connect a location to itself', $responseData['error']);
    }

    /**
     * Test case for connect route with already existing connection result in bad request.
     */
    public function testConnectConnectionAlreadyExists(): void
    {
        if (getenv('APP_ENV') !== 'test') {
            $this->markTestSkipped(
                'This test can only be run on the test environment.'
            );
        }

        $this->client->request('POST', '/proj/api/connect', [
            'from_location' => '11',
            'to_location' => '22',
            'direction' => 'north'
        ]);

        /** @var string*/
        $content = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJson($content);

        // Perform additional assertions on the response content
        $responseData = json_decode($content, true);
        $this->assertIsArray($responseData);
        $this->assertEquals('Locations are already connected in the specified direction', $responseData['error']);
    }

    /**
     * @return object The entity manager.
     */
    private function getEntityManager(string $name = 'default')
    {
        $container = $this->client->getContainer();

        /** @var ManagerRegistry $doctrine */
        $doctrine = $container->get('doctrine');

        $entityManager = $doctrine->getManager($name);

        return $entityManager;
    }
}
