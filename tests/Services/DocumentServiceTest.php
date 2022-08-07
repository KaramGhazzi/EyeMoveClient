<?php
declare(strict_types=1);

namespace Tests\Services;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use SoapClient;
use SoapFault;
use SooMedia\EyeMove\Exceptions\RequestFailedException;
use SooMedia\EyeMove\Services\AbstractEyeMoveSoapClient;
use SooMedia\EyeMove\Services\AbstractService;
use SooMedia\EyeMove\Services\DocumentService;

/**
 * Class DocumentServiceTest
 *
 * @package Tests\Services
 * @coversDefaultClass \SooMedia\EyeMove\Services\DocumentService
 */
final class DocumentServiceTest extends TestCase
{
    /**
     * @covers ::getSoapClient
     * @covers \SooMedia\EyeMove\Services\AbstractEyeMoveSoapClient::__construct
     * @throws SoapFault
     */
    public function testGetSoapClient(): void
    {
        $parameters = [
            'username' => 'username',
            'password' => 'password',
            'customer' => 'customer',
        ];

        $service = new DocumentService(
            $parameters['username'],
            $parameters['password'],
            $parameters['customer']
        );

        $soapClient = $service->getSoapClient();

        $this->assertInstanceOf(SoapClient::class, $soapClient);

        $reflection = new ReflectionProperty(
            AbstractService::class,
            'soapClient'
        );

        $reflection->setAccessible(true);

        $this->assertSame($soapClient, $reflection->getValue($service));
    }

    /**
     * @covers ::add
     * @covers ::createAndAppend
     * @covers ::createRequestXml
     * @covers ::getRequestData
     * @covers ::makeGuzzleRequest
     * @covers ::addData
     * @covers ::processGuzzleResponse
     * @throws RequestFailedException
     */
    public function testAdd(): void
    {
        $responseData = '<?xml version="1.0" encoding="utf-8"?>
                <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                  <soap:Body>
                    <AddResponse xmlns="http://ws.eye-move.nl/WoningDocument">
                      <AddResult>
                        <Succeeded>true</Succeeded>
                        <Resultaat>321</Resultaat>
                      </AddResult>
                    </AddResponse>
                  </soap:Body>
                </soap:Envelope>';

        $result = 321;

        $mock = new MockHandler([
            new Response(200, [
                'Content-Type' => 'Application/json',
            ], $responseData),
        ]);

        $container = [];

        $handlerStack = HandlerStack::create($mock);

        $history = Middleware::history($container);

        $handlerStack->push($history);

        $document = new DocumentService('asda ', 'dawdw5', 'deasd');

        $response = $document->add(
            12,
            1,
            'asda',
            'asdasd',
            [],
            ['handler' => $handlerStack],
        );
        $this->assertEquals($result, $response);

        $transaction = $container[0];

        /** @var Request $request */
        $request = $transaction['request'];

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals(
            'https://ws.eye-move.nl/WoningDocument.asmx',
            (string) $request->getUri()
        );
    }

    /**
     * @covers ::update
     * @covers ::createAndAppend
     * @covers ::createRequestXml
     * @covers ::getRequestData
     * @covers ::makeGuzzleRequest
     * @covers ::addData
     * @covers ::processGuzzleResponse
     * @throws RequestFailedException
     */
    public function testUpdate(): void
    {
        $responseData = '<?xml version="1.0" encoding="utf-8"?>
                <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                  <soap:Body>
                    <UpdateResponse xmlns="http://ws.eye-move.nl/WoningDocument">
                      <UpdateResult>
                        <Succeeded>true</Succeeded>
                        <Resultaat>true</Resultaat>
                      </UpdateResult>
                    </UpdateResponse>
                  </soap:Body>
                </soap:Envelope>';
        $result = true;
        $mock = new MockHandler([
            new Response(200, [
                'Content-Type' => 'Application/json',
            ], $responseData),
        ]);

        $container = [];
        $handlerStack = HandlerStack::create($mock);

        $history = Middleware::history($container);

        $handlerStack->push($history);
        $document = new DocumentService('asda ', 'dawdw5', 'deasd');
        $response = $document->update(
            12,
            22,
            1,
            'asda',
            'asdasd',
            [],
            ['handler' => $handlerStack],
        );
        $this->assertEquals($result, $response);

        $transaction = $container[0];

        /** @var Request $request */
        $request = $transaction['request'];

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals(
            'https://ws.eye-move.nl/WoningDocument.asmx',
            (string) $request->getUri()
        );
    }

    /**
     * @covers ::delete
     * @covers ::processSoapResponse
     * @throws RequestFailedException
     * @throws SoapFault
     */
    public function testDelete(): void
    {
        $documentId = 246;

        $requestData = [
            [
                'RecID' => $documentId,
            ],
        ];

        $result = true;

        $responseData = json_decode(json_encode([
            'DeleteResult' => [
                'Succeeded' => true,
                'Resultaat' => $result,
            ],
        ]));

        $soapClient = $this->getMockBuilder(AbstractEyeMoveSoapClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__soapCall'])
            ->getMock();

        $soapClient->expects($this->once())
            ->method('__soapCall')
            ->with(
                $this->equalTo('Delete'),
                $this->equalTo($requestData)
            )
            ->willReturn($responseData);

        $service = $this->getMockBuilder(DocumentService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getSoapClient'])
            ->getMock();

        $service->expects($this->once())
            ->method('getSoapClient')
            ->willReturn($soapClient);

        $response = $service->delete($documentId);

        $this->assertSame($result, $response);
    }
}
