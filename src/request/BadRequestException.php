<?php
namespace Sf\Request;

use Exception;

class BadRequestException extends HttpException {
    /**
     * @param string $message
     * @param Exception $previous
     */
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, 400, 'Bad Request', $previous);
    }
}
