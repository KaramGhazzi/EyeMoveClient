<?php
declare(strict_types=1);

namespace SooMedia\EyeMove\Services\Interfaces;

use SoapFault;

/**
 * Interface ServiceInterface
 *
 * @package SooMedia\EyeMove\Services\Interfaces
 */
interface ServiceInterface
{
    /**
     * Get the debug info of the last request made.
     *
     * Note: this only works if the SoapClient was created with the trace option
     * set to true.
     *
     * @return array
     * @throws SoapFault
     */
    public function getDebugInfo(): array;
}
