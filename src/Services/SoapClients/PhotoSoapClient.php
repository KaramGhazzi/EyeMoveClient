<?php
declare(strict_types=1);

namespace SooMedia\EyeMove\Services\SoapClients;

use SooMedia\EyeMove\Services\AbstractEyeMoveSoapClient;

/**
 * Class PhotoSoapClient
 *
 * @package SooMedia\EyeMove\Services\SoapClients
 */
class PhotoSoapClient extends AbstractEyeMoveSoapClient
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
