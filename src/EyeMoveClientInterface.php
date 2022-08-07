<?php
declare(strict_types=1);

namespace SooMedia\EyeMove;

use SooMedia\EyeMove\Services\Interfaces\DocumentServiceInterface;
use SooMedia\EyeMove\Services\Interfaces\ObjectServiceInterface;
use SooMedia\EyeMove\Services\Interfaces\PhotoServiceInterface;

/**
 * Class EyeMoveClientInterface
 *
 * @package SooMedia\EyeMove
 */
interface EyeMoveClientInterface
{
    /**
     * Authenticate the client.
     *
     * @param  string $username
     * @param  string $password
     * @param  string $customer
     * @return EyeMoveClientInterface
     */
    public function authenticate(
        string $username,
        string $password,
        string $customer
    ): self;

    /**
     * Get the object service.
     *
     * @return ObjectServiceInterface
     */
    public function objects(): ObjectServiceInterface;

    /**
     * Get the photo service.
     *
     * @return PhotoServiceInterface
     */
    public function photos(): PhotoServiceInterface;

    /**
     * Get the document service.
     *
     * @return DocumentServiceInterface
     */
    public function documents(): DocumentServiceInterface;
}
