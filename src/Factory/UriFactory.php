<?php

namespace CodeJet\Http\Factory;

use CodeJet\Http\Uri;
use Interop\Http\Factory\UriFactoryInterface;
use InvalidArgumentException;

class UriFactory implements UriFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createUri($uri = '')
    {
        if (!is_string($uri)) {
            throw new InvalidArgumentException(sprintf(
                'URI passed to constructor must be a string; received "%s"',
                (is_object($uri) ? get_class($uri) : gettype($uri))
            ));
        }

        return new Uri($uri);
    }
}
