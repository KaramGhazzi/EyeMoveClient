<?php
declare(strict_types=1);

namespace Tests;

use ReflectionException;
use ReflectionProperty;
use SooMedia\EyeMove\EyeMoveClient;
use PHPUnit\Framework\TestCase;
use SooMedia\EyeMove\Services\Interfaces\DocumentServiceInterface;
use SooMedia\EyeMove\Services\Interfaces\ObjectServiceInterface;
use SooMedia\EyeMove\Services\Interfaces\PhotoServiceInterface;

/**
 * Class EyeMoveClientTest
 *
 * @package Tests
 * @coversDefaultClass \SooMedia\EyeMove\EyeMoveClient
 */
final class EyeMoveClientTest extends TestCase
{
    /**
     * @covers ::authenticate
     * @throws ReflectionException
     */
    public function testAuthenticate(): void
    {
        $parameters = [
            'username' => 'username',
            'password' => 'password',
            'customer' => 'customer',
        ];

        $client = new EyeMoveClient();

        $result = $client->authenticate(
            $parameters['username'],
            $parameters['password'],
            $parameters['customer']
        );

        $this->assertSame($client, $result);

        foreach ($parameters as $key => $value) {
            $reflection = new ReflectionProperty(
                EyeMoveClient::class,
                $key
            );

            $reflection->setAccessible(true);

            $this->assertSame($value, $reflection->getValue($client));
        }
    }

    /**
     * @covers ::objects
     */
    public function testObjects(): void
    {
        $client = new EyeMoveClient();

        $client->authenticate('username', 'password', 'customer');

        $this->assertInstanceOf(
            ObjectServiceInterface::class,
            $client->objects()
        );
    }

    /**
     * @covers ::photos
     */
    public function testPhotos(): void
    {
        $client = new EyeMoveClient();

        $client->authenticate('username', 'password', 'customer');

        $this->assertInstanceOf(
            PhotoServiceInterface::class,
            $client->photos()
        );
    }

    /**
     * @covers ::documents
     */
    public function testDocuments(): void
    {
        $client = new EyeMoveClient();

        $client->authenticate('username', 'password', 'customer');

        $this->assertInstanceOf(
            DocumentServiceInterface::class,
            $client->documents()
        );
    }
}
