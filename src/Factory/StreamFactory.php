<?php

namespace CodeJet\Http\Factory;

use CodeJet\Http\Stream;
use Interop\Http\Factory\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class StreamFactory implements StreamFactoryInterface
{
    /**
     * Create a new stream from a string.
     *
     * The stream SHOULD be created with a temporary resource.
     *
     * @param string $content
     *
     * @return StreamInterface
     */
    public function createStream($content = '')
    {
        $handle = fopen('php://temp', 'w+');
        fwrite($handle, $content);

        return new Stream($handle);
    }

    /**
     * Create a stream from an existing file.
     *
     * The file MUST be opened using the given mode, which may be any mode
     * supported by the `fopen` function.
     *
     * @param string $file
     * @param string $mode
     *
     * @return StreamInterface
     */
    public function createStreamFromFile($file, $mode = 'r')
    {
        return new Stream(
            fopen($file, $mode)
        );
    }

    /**
     * Create a new stream from an existing resource.
     *
     * The stream MUST be readable and may be writable.
     *
     * @param resource $resource
     *
     * @return StreamInterface
     */
    public function createStreamFromResource($resource)
    {
        return new Stream($resource);
    }
}
