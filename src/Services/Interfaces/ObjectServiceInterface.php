<?php
declare(strict_types=1);

namespace SooMedia\EyeMove\Services\Interfaces;

/**
 * Interface ObjectServiceInterface
 *
 * @package SooMedia\EyeMove\Services\Interfaces
 */
interface ObjectServiceInterface extends ServiceInterface
{
    /**
     * List object IDs.
     *
     * @param  array $soapClientOptions
     * @return array
     */
    public function list(array $soapClientOptions = []): array;

    /**
     * Retrieve one object from the web service.
     *
     * @param  int   $recordId
     * @param  array $soapClientOptions
     * @return array
     */
    public function show(int $recordId, array $soapClientOptions = []): array;
}
