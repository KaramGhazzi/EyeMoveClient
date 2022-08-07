<?php
declare(strict_types=1);

namespace SooMedia\EyeMove\Services;

use DOMDocument;
use SoapFault;
use SooMedia\EyeMove\Services\Interfaces\PhotoServiceInterface;
use SooMedia\EyeMove\Services\SoapClients\PhotoSoapClient;

/**
 * Class PhotoService
 *
 * @package SooMedia\EyeMove\Services
 */
class PhotoService extends AbstractFileService implements PhotoServiceInterface
{
    /**
     * Gets the request URI for each file.
     *
     * @param  string $method
     * @return DOMDocument
     */
    protected function getRequestUri(
        string $method
    ): string {
        if ($method === 'show' || $method === 'list') {
            return 'https://ws.eye-move.nl/*****';
        }

        return 'https://ws.eye-move.nl/****';
    }

    /**
     * Get the SOAP client for this service.
     *
     * @param  array $options
     * @return AbstractEyeMoveSoapClient|PhotoSoapClient
     * @throws SoapFault
     */
    public function getSoapClient(
        array $options = []
    ): AbstractEyeMoveSoapClient {
        if (!isset($this->soapClient)) {
            $this->soapClient = new PhotoSoapClient(
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
            'http://schemas.xmlsoap.org/soap/***/',
            'SOAP-ENV:Envelope'
        );

        $doc->appendChild($root);

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
            $root->setAttributeNS(
                'http://www.w3.org/2000/xmlns/',
                'xmlns:ns1',
                'http://ws.eye-move.nl/***'
            );
            $this->createAndAppend($doc, 'ns1:List', $body, '');
            return $doc;
        }

        if ($method === 'show') {
            $root->setAttributeNS(
                'http://www.w3.org/2000/xmlns/',
                'xmlns:ns1',
                'http://ws.eye-move.nl/***'
            );
            $get = $this->createAndAppend($doc, 'ns1:Get', $body, '');
            $this->createAndAppend($doc, 'ns1:WoningID', $get, strval($objectId));
            return $doc;
        }

        $root->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:ns1',
            'http://ws.eye-move.nl/Foto'
        );

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
    public function getRequestData(
        int $objectId,
        int $order,
        string $filename,
        string $fileData,
        array $requestOptions = []
    ): array {
        $data = [
            'WoningID' => $objectId,
            'Volgorde' => $order,
            'Fotobestand' => [
                'Bestandsnaam' => $filename,
                'Bestand' => base64_encode($fileData),
            ],
        ];

        $data = array_merge($data, array_filter([
            'NVMMediaType' => $requestOptions['nvmMediaType'] ?? null,
            'MediaID' => $requestOptions['mediaId'] ?? null,
            'Fototype' => $requestOptions['photoType'] ?? null,
            'Bijschrift' => $requestOptions['description'] ?? null,
            'Hoofdfoto' => $requestOptions['mainPhoto'] ?? null,
            'Funda' => $requestOptions['funda'] ?? null,
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
        return 'ListResult';
    }
}
