<?php
declare(strict_types=1);

namespace SooMedia\EyeMove\Services\Interfaces;

/**
 * Interface DocumentServiceInterface
 *
 * @package SooMedia\EyeMove\Services\Interfaces
 */
interface DocumentServiceInterface extends ServiceInterface
{
    /**
     * Get all documents.
     *
     * @param  int    $objectId
     * @param  int    $fileId
     * @param  int    $order
     * @param  string $filename
     * @param  string $fileData
     * @param  array  $requestOptions
     * @param  array  $guzzleClientOptions
     * @return int
     */
    public function list(
        int $objectId,
        int $fileId,
        int $order,
        string $filename,
        string $fileData,
        array $requestOptions,
        array $guzzleClientOptions
    );

    /**
     * Get a document.
     *
     * @param  int    $objectId
     * @param  int    $fileId
     * @param  int    $order
     * @param  string $filename
     * @param  string $fileData
     * @param  array  $requestOptions
     * @param  array  $guzzleClientOptions
     * @return int
     */
    public function show(
        int $objectId,
        int $fileId,
        int $order,
        string $filename,
        string $fileData,
        array $requestOptions,
        array $guzzleClientOptions
    );

    /**
     * Add a document to the object.
     *
     * @param  int    $objectId
     * @param  int    $order
     * @param  string $filename
     * @param  string $fileData
     * @param  array  $requestOptions
     * @param  array  $guzzleClientOptions
     * @return int
     */
    public function add(
        int $objectId,
        int $order,
        string $filename,
        string $fileData,
        array $requestOptions,
        array $guzzleClientOptions = []
    ): int;

    /**
     * Update a document.
     *
     * @param  int    $filetId
     * @param  int    $objectId
     * @param  int    $order
     * @param  string $filename
     * @param  string $fileData
     * @param  array  $requestOptions
     * @param  array  $guzzleClientOptions
     * @return bool
     */
    public function update(
        int $filetId,
        int $objectId,
        int $order,
        string $filename,
        string $fileData,
        array $requestOptions,
        array $guzzleClientOptions = []
    ): bool;

    /**
     * Delete a document.
     *
     * @param  int   $documentId
     * @param  array $soapClientOptions
     * @return bool
     */
    public function delete(
        int $documentId,
        array $soapClientOptions = []
    ): bool;
}
