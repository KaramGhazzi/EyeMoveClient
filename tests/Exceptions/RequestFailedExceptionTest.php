<?php
declare(strict_types=1);

namespace Tests\Exceptions;

use SooMedia\EyeMove\Exceptions\RequestFailedException;
use PHPUnit\Framework\TestCase;

/**
 * Class RequestFailedExceptionTest
 *
 * @package Tests\Exceptions
 * @coversDefaultClass \SooMedia\EyeMove\Exceptions\RequestFailedException
 */
final class RequestFailedExceptionTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getErrors
     */
    public function testErrors()
    {
        $errors = [
            'This is an error',
        ];

        $exception = new RequestFailedException('Some message', $errors);

        $this->assertSame($errors, $exception->getErrors());
    }
}
