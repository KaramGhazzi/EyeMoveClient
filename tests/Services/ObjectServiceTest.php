<?php
declare(strict_types=1);

namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use SoapFault;
use SooMedia\EyeMove\Exceptions\RequestFailedException;
use SooMedia\EyeMove\Services\AbstractEyeMoveSoapClient;
use SooMedia\EyeMove\Services\ObjectService;
use SooMedia\EyeMove\Services\SoapClients\ObjectSoapClient;
use SooMedia\EyeMove\Services\SoapClients\ObjectsSoapClient;

/**
 * Class ObjectServiceTest
 *
 * @package SooMedia\EyeMove\Services
 * @coversDefaultClass \SooMedia\EyeMove\Services\ObjectService
 */
final class ObjectServiceTest extends TestCase
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

        $service = new ObjectService(
            $parameters['username'],
            $parameters['password'],
            $parameters['customer']
        );

        $objectsSoapClient = $service->getSoapClient();

        $this->assertInstanceOf(ObjectsSoapClient::class, $objectsSoapClient);

        $objectSoapClient = $service->getSoapClient([
            'class' => ObjectSoapClient::class,
        ]);

        $this->assertInstanceOf(ObjectSoapClient::class, $objectSoapClient);
    }

    /**
     * @covers ::list
     * @covers ::processSoapResponse
     * @throws RequestFailedException
     * @throws SoapFault
     */
    public function testList(): void
    {
        $result = [
            123,
            456,
        ];

        $responseData = json_decode(json_encode([
            'RetrieveResult' => [
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
            ->with($this->equalTo('Retrieve'))
            ->willReturn($responseData);

        $service = $this->getMockBuilder(ObjectService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getSoapClient'])
            ->getMock();

        $service->expects($this->once())
            ->method('getSoapClient')
            ->with($this->equalTo([
                'class' => ObjectsSoapClient::class,
            ]))
            ->willReturn($soapClient);

        $response = $service->list();

        $this->assertSame($result, $response);
    }

    /**
     * @covers ::show
     * @covers ::processSoapResponse
     * @throws RequestFailedException
     * @throws SoapFault
     */
    public function testShow(): void
    {
        $recordId = 12345;

        $requestData = [
            [
                'RecID' => $recordId,
            ],
        ];

        $result = [
            'foo' => 'bar',
        ];

        $responseData = json_decode(json_encode([
            'RetrieveResult' => [
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
                $this->equalTo('Retrieve'),
                $this->equalTo($requestData)
            )
            ->willReturn($responseData);

        $service = $this->getMockBuilder(ObjectService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getSoapClient'])
            ->getMock();

        $service->expects($this->once())
            ->method('getSoapClient')
            ->with($this->equalTo([
                'class' => ObjectSoapClient::class,
            ]))
            ->willReturn($soapClient);

        $response = $service->show($recordId);

        $this->assertSame($result, $response);
    }
}
