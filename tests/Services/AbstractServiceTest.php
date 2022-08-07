<?php
declare(strict_types=1);

namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use SoapFault;
use SooMedia\EyeMove\Exceptions\RequestFailedException;
use SooMedia\EyeMove\Services\AbstractEyeMoveSoapClient;
use SooMedia\EyeMove\Services\AbstractService;
use SooMedia\EyeMove\Services\ObjectService;

/**
 * Class AbstractServiceTest
 *
 * @package Tests\Services
 * @coversDefaultClass \SooMedia\EyeMove\Services\AbstractService
 */
final class AbstractServiceTest extends TestCase
{
    /**
     * @covers ::__construct
     * @throws ReflectionException
     */
    public function testConstruct(): void
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

        foreach ($parameters as $key => $value) {
            $reflection = new ReflectionProperty(
                AbstractService::class,
                $key
            );

            $reflection->setAccessible(true);

            $this->assertSame($value, $reflection->getValue($service));
        }
    }

    /**
     * @covers ::getDebugInfo
     * @covers ::cleanRequest
     * @throws SoapFault
     */
    public function testGetDebugInfo(): void
    {
        $soapClient = $this->getMockBuilder(AbstractEyeMoveSoapClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                '__getLastRequest',
                '__getLastRequestHeaders',
                '__getLastResponse',
                '__getLastResponseHeaders',
            ])
            ->getMock();

        $request = '<?xml version="1.0" encoding="UTF-8"?>';
        $request .= '<Envelope>';
        $request .= '    <Header>';
        $request .= '        <AuthHeader>';
        $request .= '            <Username>some_username</Username>';
        $request .= '            <Password>some_password</Password>';
        $request .= '            <Customer>some_customer</Customer>';
        $request .= '        </AuthHeader>';
        $request .= '    </Header>';
        $request .= '    <Body>';
        $request .= '        <Add>';
        $request .= '            <Gegevens>';
        $request .= '                <WoningDocumentBestand>';
        $request .= '                    <Bestandsnaam>filename.txt</Bestandsnaam>';
        $request .= '                    <Bestand>Filedata</Bestand>';
        $request .= '                </WoningDocumentBestand>';
        $request .= '            </Gegevens>';
        $request .= '        </Add>';
        $request .= '    </Body>';
        $request .= '</Envelope>';

        $soapClient->expects($this->once())
            ->method('__getLastRequest')
            ->willReturn($request);

        $soapClient->expects($this->once())
            ->method('__getLastRequestHeaders')
            ->willReturn('request headers');

        $soapClient->expects($this->once())
            ->method('__getLastResponse')
            ->willReturn('response');

        $soapClient->expects($this->once())
            ->method('__getLastResponseHeaders')
            ->willReturn('response headers');

        $service = $this->getMockForAbstractClass(
            AbstractService::class,
            [],
            '',
            false
        );

        $service->expects($this->once())
            ->method('getSoapClient')
            ->willReturn($soapClient);

        $cleanRequest = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $cleanRequest .= '<Envelope>';
        $cleanRequest .= '    <Header>';
        $cleanRequest .= '        <AuthHeader>';
        $cleanRequest .= '            <Username>username</Username>';
        $cleanRequest .= '            <Password>password</Password>';
        $cleanRequest .= '            <Customer>customer</Customer>';
        $cleanRequest .= '        </AuthHeader>';
        $cleanRequest .= '    </Header>';
        $cleanRequest .= '    <Body>';
        $cleanRequest .= '        <Add>';
        $cleanRequest .= '            <Gegevens>';
        $cleanRequest .= '                <WoningDocumentBestand>';
        $cleanRequest .= '                    <Bestandsnaam>filename.txt</Bestandsnaam>';
        $cleanRequest .= '                    <Bestand>data</Bestand>';
        $cleanRequest .= '                </WoningDocumentBestand>';
        $cleanRequest .= '            </Gegevens>';
        $cleanRequest .= '        </Add>';
        $cleanRequest .= '    </Body>';
        $cleanRequest .= "</Envelope>\n";

        $this->assertSame([
            'last_request' => $cleanRequest,
            'last_request_headers' => 'request headers',
            'last_response' => 'response',
            'last_response_headers' => 'response headers',
        ], $service->getDebugInfo());
    }

    /**
     * @covers ::processSoapResponse
     */
    public function testProcessSoapResponse(): void
    {
        $response = json_decode(json_encode([
            'AddResult' => [
                'Succeeded' => true,
                'Resultaat' => [
                    'foo' => 'bar',
                ],
            ],
        ]));

        $service = $this->getMockForAbstractClass(AbstractService::class, [
            'username',
            'password',
            'customer',
        ]);

        $reflection = new ReflectionMethod(
            AbstractService::class,
            'processSoapResponse'
        );

        $reflection->setAccessible(true);

        $result = $reflection->invoke($service, $response, 'AddResult');

        $this->assertSame(['foo' => 'bar'], $result);
    }

    /**
     * @covers ::processSoapResponse
     */
    public function testProcessSoapResponseException(): void
    {
        $this->expectException(RequestFailedException::class);

        $response = json_decode(json_encode([
            'AddResult' => [
                'Succeeded' => false,
                'Errors' => [
                    'This went wrong.',
                ],
            ],
        ]));

        $service = $this->getMockForAbstractClass(AbstractService::class, [
            'username',
            'password',
            'customer',
        ]);

        $reflection = new ReflectionMethod(
            AbstractService::class,
            'processSoapResponse'
        );

        $reflection->setAccessible(true);

        $reflection->invoke($service, $response, 'AddResult');
    }
}
