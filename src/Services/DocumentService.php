<?php
declare(strict_types=1);

namespace SooMedia\EyeMove\Services;

use DOMDocument;
use SoapFault;
use SooMedia\EyeMove\Services\Interfaces\DocumentServiceInterface;
use SooMedia\EyeMove\Services\SoapClients\DocumentSoapClient;

/**
 * Class DocumentService
 *
 * @package SooMedia\EyeMove\Services
 */
class DocumentService extends AbstractFileService implements
    DocumentServiceInterface
{
    /**
     * Gets the Request uri for each file.
     *
     * @param  string $method
     * @return DOMDocument
     */
    protected function getRequestUri(
        string $method
    ): string {
        return 'https://ws.eye-move.nl/****.asmx';
    }

    /**
     * Get the SOAP client for this service.
     *
     * @param  array $options
     * @return AbstractEyeMoveSoapClient|DocumentSoapClient
     * @throws SoapFault
     */
    public function getSoapClient(
        array $options = []
    ): AbstractEyeMoveSoapClient {
        if (!isset($this->soapClient)) {
            $this->soapClient = new DocumentSoapClient(
                $this->username,
                $this->password,
                $this->customer,
                $options
            );
        }

        return $this->soapClient;
    }

    /**
     * Creates a DomDocument or XML.
     *
     * @param  int         $objectId
     * @param  int         $order
     * @param  string      $filename
     * @param  string      $fileData
     * @param  array       $requestOptions
     * @param  int|null    $fileId
     * @param  string|null $method
     * @return DOMDocument
     */
    public function createRequestXml(
        int $objectId,
        int $order,
        string $filename,
        string $fileData,
        array $requestOptions = [],
        ?int $fileId = null,
        ?string $method = null
    ): DOMDocument {
        $doc = new DOMDocument('1.0', 'utf-8');

        $root = $doc->createElementNS(
            'http://schemas.xmlsoap.org/soap/envelope/',
            'SOAP-ENV:Envelope'
        );

        $doc->appendChild($root);

        $root->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:ns1',
            'http://ws.eye-move.nl/***'
        );

        $this->addData($doc, $root, [
            'SOAP-ENV:Header' => [
                'ns1:AuthHeader' => [
                    'ns1:Username' => $this->username,
                    'ns1:Password' => $this->password,
                    'ns1:Customer' => $this->customer,
                ],
            ],
            'SOAP-ENV:Body' => [],
        ]);

        $body = $doc->getElementsByTagName('SOAP-ENV:Body')->item(0);

        if ($method === 'list') {
            $getAll = $this->createAndAppend($doc, 'ns1:GetAll', $body, '');
            $this->createAndAppend($doc, 'ns1:WoningID', $getAll, strval($objectId));
            return $doc;
        }

        if ($method === 'show') {
            $get = $this->createAndAppend($doc, 'ns1:Get', $body, '');
            $this->createAndAppend($doc, 'ns1:RecID', $get, strval($fileId));
            return $doc;
        }

        $childTag = $method === 'add' ? 'ns1:Add' : 'ns1:Update';

        $child = $this->createAndAppend($doc, $childTag, $body, '');

        if ($method === 'update') {
            $this->createAndAppend($doc, 'ns1:RecID', $child, strval($fileId));
        }

        $data = $this->createAndAppend($doc, 'ns1:Gegevens', $child, '');

        $this->addData(
            $doc,
            $data,
            $this->getRequestData(
                $objectId,
                $order,
                $filename,
                $fileData,
                $requestOptions
            ),
            'ns1:'
        );

        return $doc;
    }

    /**
     * Get the request data.
     *
     * @param  int    $objectId
     * @param  int    $order
     * @param  string $filename
     * @param  string $fileData
     * @param  array  $requestOptions
     * @return array
     */
    protected function getRequestData(
        int $objectId,
        int $order,
        string $filename,
        string $fileData,
        array $requestOptions = []
    ): array {
        $data = [
            'WoningID' => $objectId,
            'Volgorde' => $order,
            'WoningDocumentBestand' => [
                'Bestandsnaam' => $filename,
                'Bestand' => base64_encode($fileData),
            ],
        ];

        $data = array_merge($data, array_filter([
            'RecID' => $requestOptions['recordId'] ?? null,
            'WoningDocumentType' => $requestOptions['documentType'] ?? null,
            'Omschrijving' => $requestOptions['description'] ?? null,
            'OorspronkelijkeBestandsnaam' => $requestOptions['originalFilename'] ?? null,
            'NaarFunda' => $requestOptions['toFunda'] ?? null,
            'DocumentStatus' => $requestOptions['documentStatus'] ?? null,
            'Invoerdatum' => $requestOptions['created_at'] ?? null,
            'DatumLaatsteWijziging' => $requestOptions['updated_at'] ?? null,
        ], function ($element) {
            return $element !== null;
        }));

        return $data;
    }

    /**
     * Gets the result key string for each file.
     *
     * @return string
     */
    protected function getResultKey(): string
    {
        return 'GetAllResult';
    }
}
