<?php
declare(strict_types=1);

namespace SooMedia\EyeMove\Services;

use SoapFault;
use SooMedia\EyeMove\Exceptions\RequestFailedException;

/**
 * Class AbstractFileService
 *
 * @package SooMedia\EyeMove\Services
 */
abstract class TestFileService extends AbstractService
{
    /**
     * Add a file to the object.
     *
     * @param  int    $objectId
     * @param  int    $order
     * @param  string $filename
     * @param  string $fileData
     * @param  array  $requestOptions
     * @param  array  $soapClientOptions
     * @return int
     * @throws SoapFault
     * @throws RequestFailedException
     */
    public function add(
        int $objectId,
        int $order,
        string $filename,
        string $fileData,
        array $requestOptions = [],
        array $soapClientOptions = []
    ): int {
        $response = $this->getSoapClient($soapClientOptions)
            ->__soapCall('Add', [
                [
                    'Gegevens' => $this->getRequestData(
                        $objectId,
                        $order,
                        $filename,
                        $fileData,
                        $requestOptions
                    ),
                ],
            ]);

        return $this->processSoapResponse($response, 'AddResult');
    }

    private function getRequestData(
        int $objectId,
        int $order,
        string $filename,
        string $fileData,
        array $requestOptions
    ) {
    }
}
