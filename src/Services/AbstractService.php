<?php
declare(strict_types=1);

namespace SooMedia\EyeMove\Services;

use DOMDocument;
use SoapClient;
use SoapFault;
use SooMedia\EyeMove\Exceptions\RequestFailedException;
use SooMedia\EyeMove\Services\Interfaces\ServiceInterface;

/**
 * Class AbstractService
 *
 * @package SooMedia\EyeMove\Services
 */
abstract class AbstractService implements ServiceInterface
{
    /**
     * The authentication username.
     *
     * @var string
     */
    protected $username;

    /**
     * The authentication password.
     *
     * @var string
     */
    protected $password;

    /**
     * The authentication customer.
     *
     * @var string
     */
    protected $customer;

    /**
     * The SOAP client for this service.
     *
     * @var SoapClient
     */
    protected $soapClient;

    /**
     * AbstractService constructor.
     *
     * @param  string $username
     * @param  string $password
     * @param  string $customer
     */
    public function __construct(
        string $username,
        string $password,
        string $customer
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->customer = $customer;
    }

    /**
     * Get the SOAP client for this service.
     *
     * @param  array $options
     * @return AbstractEyeMoveSoapClient
     * @throws SoapFault
     */
    abstract public function getSoapClient(
        array $options = []
    ): AbstractEyeMoveSoapClient;

    /**
     * Get the debug info of the last request made.
     *
     * Note: this only works if the SoapClient was created with the trace option
     * set to true.
     *
     * @return array
     * @throws SoapFault
     */
    public function getDebugInfo(): array
    {
        $soapClient = $this->getSoapClient();

        return [
            'last_request' => $this->cleanRequest(
                $soapClient->__getLastRequest()
            ),
            'last_request_headers' => $soapClient->__getLastRequestHeaders(),
            'last_response' => $soapClient->__getLastResponse(),
            'last_response_headers' => $soapClient->__getLastResponseHeaders(),
        ];
    }

    /**
     * Clean the request XML.
     *
     * @param  string|null $request
     * @return string|null
     */
    protected function cleanRequest(?string $request): ?string
    {
        if (!$request) {
            return $request;
        }

        $dom = new DOMDocument();

        $result = $dom->loadXML($request, LIBXML_PARSEHUGE);

        if (!$result) {
            return $request;
        }

        $dom->getElementsByTagName('Username')->item(0)->nodeValue = 'username';
        $dom->getElementsByTagName('Password')->item(0)->nodeValue = 'password';
        $dom->getElementsByTagName('Customer')->item(0)->nodeValue = 'customer';
        $dom->getElementsByTagName('Bestand')->item(0)->nodeValue = 'data';

        return $dom->saveXML();
    }

    /**
     * Process the response.
     *
     * @param  object $response
     * @param  string $resultKey
     * @return mixed
     * @throws RequestFailedException
     */
    protected function processSoapResponse(
        object $response,
        string $resultKey
    ) {
        $data = json_decode(json_encode($response), true);

        $result = $data[$resultKey];

        if (!$result['Succeeded']) {
            throw new RequestFailedException(
                'Request to EyeMove web service failed.',
                $result['Errors']
            );
        }

        return $result['Resultaat'];
    }

    /**
     * Process the response of Guzzle Client requests.
     *
     * @param  object|null $response
     * @param  string      $resultKey
     * @return mixed
     * @throws RequestFailedException
     */
    protected function processGuzzleResponse(
        object $response,
        string $resultKey
    ) {
        $responseDom = new DOMDocument();

        $responseDom->loadXML((string) $response->getBody());

        $getResultElement = $responseDom->getElementsByTagName($resultKey)[0];

        $resultElement = $getResultElement->getElementsByTagName('Resultaat')[0];

        $result = $resultElement->nodeValue;

        $errorContainer = $getResultElement->getElementsByTagName('Errors')->item(0);

        if (!$errorContainer) {
            if (is_numeric($result)) {
                return intval($result);
            }

            if ($result === 'true') {
                return true;
            }

            return $result;
        }

        $errors = [];

        foreach ($errorContainer->childNodes as $errorElement) {
            $errors[] = $errorElement->nodeValue;
        }

        throw new RequestFailedException(
            'Request to EyeMove web service failed.',
            (array) $errors
        );
    }
}
