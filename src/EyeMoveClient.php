<?php
declare(strict_types=1);

namespace SooMedia\EyeMove;

use SooMedia\EyeMove\Services\DocumentService;
use SooMedia\EyeMove\Services\Interfaces\DocumentServiceInterface;
use SooMedia\EyeMove\Services\Interfaces\ObjectServiceInterface;
use SooMedia\EyeMove\Services\Interfaces\PhotoServiceInterface;
use SooMedia\EyeMove\Services\ObjectService;
use SooMedia\EyeMove\Services\PhotoService;

/**
 * Class EyeMoveClient
 *
 * @package SooMedia\EyeMove
 */
class EyeMoveClient implements EyeMoveClientInterface
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
    ): EyeMoveClientInterface {
        $this->username = $username;
        $this->password = $password;
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get the object service.
     *
     * @return ObjectServiceInterface
     */
    public function objects(): ObjectServiceInterface
    {
        return new ObjectService(
            $this->username,
            $this->password,
            $this->customer
        );
    }

    /**
     * Get the photo service.
     *
     * @return PhotoServiceInterface
     */
    public function photos(): PhotoServiceInterface
    {
        return new PhotoService(
            $this->username,
            $this->password,
            $this->customer
        );
    }

    /**
     * Get the document service.
     *
     * @return DocumentServiceInterface
     */
    public function documents(): DocumentServiceInterface
    {
        return new DocumentService(
            $this->username,
            $this->password,
            $this->customer
        );
    }
}
