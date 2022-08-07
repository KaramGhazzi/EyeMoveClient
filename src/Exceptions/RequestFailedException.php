<?php
declare(strict_types=1);

namespace SooMedia\EyeMove\Exceptions;

/**
 * Class RequestFailedException
 *
 * @package SooMedia\EyeMove\Exceptions
 */
class RequestFailedException extends EyeMoveClientException
{
    /**
     * The request errors.
     *
     * @var array
     */
    protected $errors;

    /**
     * RequestFailedException constructor.
     *
     * @param  string $message
     * @param  array  $errors
     */
    public function __construct(
        $message,
        array $errors
    ) {
        parent::__construct($message);

        $this->errors = $errors;
    }

    /**
     * Get the request errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
