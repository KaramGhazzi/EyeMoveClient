<?php
declare(strict_types=1);

namespace SooMedia\EyeMove\Services;

use DOMDocument;
use DOMElement;
use DOMNode;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use SoapFault;
use SooMedia\EyeMove\Exceptions\RequestFailedException;

/**
 * Class AbstractFileService
 *
 * @package SooMedia\EyeMove\Services
 */
abstract class AbstractFileService extends AbstractService
{
    /**
     * Creates a Request Xml for each method.
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
    abstract protected function createRequestXml(
        int $objectId,
        int $order,
        string $filename,
        string $fileData,
        array $requestOptions = [],
        ?int $fileId = null,
        ?string $method = null
    ): DOMDocument;

    /**
     * Gets the Request uri for each file.
     *
     * @param  string $method
     * @return DOMDocument
     */
    abstract protected function getRequestUri(
        string $method
    ): string;

    /**
     * Creates XML body and adds the request data to the body if exists.
     *
     * @param  DOMDocument $doc
     * @param  DOMElement  $parent
     * @param  array       $children
     * @param  string|null $prefix
     * @return void
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    protected function addData(
        DOMDocument $doc,
        DOMElement $parent,
        array $children,
        string $prefix = ''
    ): void {
        foreach ($children as $key => $content) {
            $child = $doc->createElement($prefix . $key);

            $parent->appendChild($child);

            if (is_array($content)) {
                $this->addData($doc, $child, $content, $prefix);
            } else {
                $child->nodeValue = strval($content);
            }
        }
    }

    /**
     * Gets the result key string for each file.
     *
     * @return string
     */
    abstract protected function getResultKey(): string;

    /**
     * Creates a DomDocument parents and appends child and its value to it.
     *
     * @param  DOMDocument $doc
     * @param  string      $child
     * @param  DOMNode     $parent
     * @param  string      $value
     * @return DOMElement
     */
    public function createAndAppend(
        DOMDocument $doc,
        string $child,
        DOMNode $parent,
        string $value
    ): DOMElement {
        $element = $doc->createElement($child, $value);

        $parent->appendChild($element);

        return $element;
    }

    /**
     * Get all files.
     *
     * @param  int    $objectId
     * @param  int    $fileId
     * @param  int    $order
     * @param  string $filename
     * @param  string $fileData
     * @param  array  $requestOptions
     * @param  array  $guzzleClientOptions
     * @return array
     * @throws RequestFailedException
     */
    public function list(
        int $objectId,
        int $fileId,
        int $order,
        string $filename,
        string $fileData,
        array $requestOptions = [],
        array $guzzleClientOptions = []
    ): array {
        $method = 'list';

        $response = $this->makeGuzzleRequest(
            $guzzleClientOptions,
            $this->createRequestXml(
                $objectId,
                $order,
                $filename,
                $fileData,
                $requestOptions,
                $fileId,
                $method,
            ),
            $method
        );

        return $this->processGuzzleResponse($response, $this->getResultKey());
    }

    /**
     * Get a file.
     *
     * @param  int    $objectId
     * @param  int    $fileId
     * @param  int    $order
     * @param  string $filename
     * @param  string $fileData
     * @param  array  $requestOptions
     * @param  array  $guzzleClientOptions
     * @return array
     * @throws RequestFailedException
     */
    public function show(
        int $objectId,
        int $fileId,
        int $order,
        string $filename,
        string $fileData,
        array $requestOptions = [],
        array $guzzleClientOptions = []
    ): array {
        $method = 'show';

        $response = $this->makeGuzzleRequest(
            $guzzleClientOptions,
            $this->createRequestXml(
                $objectId,
                $order,
                $filename,
                $fileData,
                $requestOptions,
                $fileId,
                $method,
            ),
            $method
        );

        return $this->processGuzzleResponse($response, 'GetResult');
    }

    /**
     * Add a file to the object.
     *
     * @param  int    $objectId
     * @param  int    $order
     * @param  string $filename
     * @param  string $fileData
     * @param  array  $requestOptions
     * @param  array  $guzzleClientOptions
     * @return int
     * @throws RequestFailedException
     */
    public function add(
        int $objectId,
        int $order,
        string $filename,
        string $fileData,
        array $requestOptions = [],
        array $guzzleClientOptions = []
    ): int {
        $method = 'add';

        $response = $this->makeGuzzleRequest(
            $guzzleClientOptions,
            $this->createRequestXml(
                $objectId,
                $order,
                $filename,
                $fileData,
                $requestOptions,
                null,
                $method
            )
        );

        return $this->processGuzzleResponse($response, 'AddResult');
    }

    /**
     * Update a file on the object.
     *
     * @param  int    $fileId
     * @param  int    $objectId
     * @param  int    $order
     * @param  string $filename
     * @param  string $fileData
     * @param  array  $requestOptions
     * @param  array  $guzzleClientOptions
     * @return bool
     * @throws RequestFailedException
     */
    public function update(
        int $fileId,
        int $objectId,
        int $order,
        string $filename,
        string $fileData,
        array $requestOptions = [],
        array $guzzleClientOptions = []
    ): bool {
        $method = 'update';

        $response = $this->makeGuzzleRequest(
            $guzzleClientOptions,
            $this->createRequestXml(
                $objectId,
                $order,
                $filename,
                $fileData,
                $requestOptions,
                $fileId,
                $method,
            ),
            $method
        );

        return $this->processGuzzleResponse($response, 'UpdateResult');
    }

    /**
     * Delete a file.
     *
     * @param  int   $fileId
     * @param  array $soapClientOptions
     * @return bool
     * @throws SoapFault
     * @throws RequestFailedException
     */
    public function delete(
        int $fileId,
        array $soapClientOptions = []
    ): bool {
        $response = $this->getSoapClient($soapClientOptions)
            ->__soapCall('Delete', [
                [
                    'RecID' => $fileId,
                ],
            ]);

        return $this->processSoapResponse($response, 'DeleteResult');
    }

    /**
     * Get the response of guzzle client call
     *
     * @param  array       $guzzleClientOptions
     * @param  DOMDocument $doc
     * @param  string      $method
     * @return ResponseInterface
     */
    protected function makeGuzzleRequest(
        array $guzzleClientOptions,
        DOMDocument $doc,
        string $method = ''
    ) {
        $client = new Client(array_merge([
            'verify'=> false,
            'base_uri' => 'https://ws.eye-move.nl',
        ], $guzzleClientOptions));

        echo $doc->saveXML();

        return $client->request('POST', $this->getRequestUri($method), [
            'body' => $doc->saveXML(),
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF8',
            ],
        ]);
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
    abstract protected function getRequestData(
        int $objectId,
        int $order,
        string $filename,
        string $fileData,
        array $requestOptions = []
    ): array;
}
