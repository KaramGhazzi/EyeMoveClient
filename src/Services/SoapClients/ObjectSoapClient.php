<?php
declare(strict_types=1);

namespace SooMedia\EyeMove\Services\SoapClients;

use SooMedia\EyeMove\Services\AbstractEyeMoveSoapClient;

/**
 * Class ObjectSoapClient
 *
 * @package SooMedia\EyeMove\Services\SoapClients
 * @method object Retrieve(int $recId)
 */
class ObjectSoapClient extends AbstractEyeMoveSoapClient
{
    /**
     * The URI of the WDSL file.
     *
     * @var string
     */
    protected $wsdl = 'https://ws.eye-move.nl/***.asmx?WSDL';

    /**
     * The SOAP header namespace.
     *
     * @var string
     */
    protected $headerNamespace = 'http://ws.eye-move.nl/***';
}
