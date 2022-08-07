<?php
declare(strict_types=1);

namespace SooMedia\EyeMove\Services;

use SoapClient;
use SoapFault;
use SoapHeader;

/**
 * Class AbstractEyeMoveSoapClient
 *
 * @package SooMedia\EyeMove\Services
 */
abstract class AbstractEyeMoveSoapClient extends SoapClient
{
    /**
     * Default connection timeout.
     */
    public const CONNECTION_TIMEOUT = 60;

    /**
     * The URI of the WDSL file.
     *
     * @var string
     */
    protected $wsdl;

    /**
     * The SOAP header namespace.
     *
     * @var string
     */
    protected $headerNamespace;

    /**
     * Default SoapClient options.
     *
     * @var array
     */
    protected $options = [
        'soap_version' => SOAP_1_2,
        'keep_alive' => false,
        'connection_timeout' => self::CONNECTION_TIMEOUT,
    ];

    /**
     * AbstractSoapClient constructor.
     *
     * @param  string $username
     * @param  string $password
     * @param  string $customer
     * @param  array  $options
     * @throws SoapFault
     */
    public function __construct(
        string $username,
        string $password,
        string $customer,
        array $options = []
    ) {
        $options = array_merge($this->options, $options);

        parent::__construct($this->wsdl, $options);

        $authHeader = new SoapHeader(
            $options['headerNamespace'] ?? $this->headerNamespace,
            'AuthHeader',
            [
                'Username' => $username,
                'Password' => $password,
                'Customer' => $customer,
            ]
        );

        $this->__setSoapHeaders($authHeader);
    }
}
