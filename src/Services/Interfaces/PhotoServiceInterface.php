<?php
declare(strict_types=1);

namespace SooMedia\EyeMove\Services\Interfaces;

/**
 * Interface PhotoServiceInterface
 *
 * @package SooMedia\EyeMove\Services\Interfaces
 */
interface PhotoServiceInterface extends ServiceInterface
{
    /**
     * Get all photos.
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
     * Get a photo.
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
     * Add a photo to the object.
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
     * Update a photo.
     *
     * @param  int    $fileId
     * @param  int    $objectId
     * @param  int    $order
     * @param  string $filename
     * @param  string $fileData
     * @param  array  $requestOptions
     * @param  array  $guzzleClientOptions
     * @return bool
     */
    public function update(
        int $fileId,
        int $objectId,
        int $order,
        string $filename,
        string $fileData,
        array $requestOptions,
        array $guzzleClientOptions = []
    ): bool;

    /**
     * Delete a photo.
     *
     * @param  int   $photoId
     * @param  array $soapClientOptions
     * @return bool
     */
    public function delete(
        int $photoId,
        array $soapClientOptions = []
    ): bool;
}
