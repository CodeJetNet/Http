<?php

namespace CodeJet\Http\Factory;

use CodeJet\Http\Request;
use CodeJet\Http\Uri;
use Interop\Http\Factory\RequestFactoryInterface;
use Psr\Http\Message\UriInterface;

class RequestFactory implements RequestFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createRequest($method, $uri)
    {
        if (!$uri instanceof UriInterface) {
            $uri = new Uri($uri);
        }

        return (new Request())
            ->withMethod($method)
            ->withUri($uri);
    }
}
