<?php
declare(strict_types=1);

namespace SooMedia\EyeMove\Services;

use SoapFault;
use SooMedia\EyeMove\Exceptions\RequestFailedException;
use SooMedia\EyeMove\Services\Interfaces\ObjectServiceInterface;
use SooMedia\EyeMove\Services\SoapClients\ObjectSoapClient;
use SooMedia\EyeMove\Services\SoapClients\ObjectsSoapClient;

/**
 * Class ObjectService
 *
 * @package SooMedia\EyeMove\Services
 */
class ObjectService extends AbstractService implements ObjectServiceInterface
{
    /**
     * Get the SOAP client for this service.
     *
     * @param  array $options
     * @return AbstractEyeMoveSoapClient
     * @throws SoapFault
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public function getSoapClient(
        array $options = []
    ): AbstractEyeMoveSoapClient {
        $class = $options['class'] ?? ObjectsSoapClient::class;

        $this->soapClient = new $class(
            $this->username,
            $this->password,
            $this->customer,
            $options
        );

        return $this->soapClient;
    }

    /**
     * List object IDs.
     *
     * @param  array $soapClientOptions
     * @return array
     * @throws SoapFault
     * @throws RequestFailedException
     */
    public function list(array $soapClientOptions = []): array
    {
        $response = $this->getSoapClient(array_merge($soapClientOptions, [
            'class' => ObjectsSoapClient::class,
        ]))->__soapCall('Retrieve', []);

        return $this->processSoapResponse($response, 'RetrieveResult');
    }

    /**
     * Retrieve one object from the web service.
     *
     * @param  int   $recordId
     * @param  array $soapClientOptions
     * @return array
     * @throws SoapFault
     * @throws RequestFailedException
     */
    public function show(int $recordId, array $soapClientOptions = []): array
    {
        $response = $this->getSoapClient(array_merge($soapClientOptions, [
            'class' => ObjectSoapClient::class,
        ]))->__soapCall('Retrieve', [
            [
                'RecID' => $recordId,
            ],
        ]);

        return $this->processSoapResponse($response, 'RetrieveResult');
    }
}
