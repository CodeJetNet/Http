<?php

namespace CodeJet\Http\Factory;

use CodeJet\Http\Response;
use Interop\Http\Factory\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * Create a new response.
     *
     * @param integer $code HTTP status code
     *
     * @return ResponseInterface
     */
    public function createResponse($code = 200)
    {
        return (new Response())->withStatus($code);
    }
}
