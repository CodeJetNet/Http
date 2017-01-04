<?php

namespace CodeJet\Http;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{

    protected $handle;

    /**
     * Stream constructor.
     * @param $stream
     */
    public function __construct($stream)
    {
        if (!$this->isStream($stream)) {
            throw new InvalidArgumentException('Must be a stream.');
        }

        $this->handle = $stream;
    }

    /**
     * @param $stream
     * @return bool
     */
    protected function isStream($stream)
    {
        if (is_resource($stream) && get_resource_type($stream) === 'stream') {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        if (!$this->isReadable()) {
            return '';
        }

        if ($this->isSeekable()) {
            $this->rewind();
        }

        return stream_get_contents($this->handle);
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        if (!$this->handle) {
            return;
        }

        $handle = $this->detach();
        fclose($handle);
    }

    /**
     * @inheritdoc
     */
    public function detach()
    {
        $handle = $this->handle;
        $this->handle = null;

        return $handle;
    }

    /**
     * @inheritdoc
     */
    public function getSize()
    {
        $fstatData = fstat($this->handle);

        if (isset($fstatData['size'])) {
            return $fstatData['size'];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function tell()
    {
        if (!$this->handle) {
            throw new \RuntimeException('Cannot tell position, stream is closed.');
        }

        return ftell($this->handle);
    }

    /**
     * @inheritdoc
     */
    public function eof()
    {
        if (!$this->handle) {
            return true;
        }

        return feof($this->handle);
    }

    /**
     * @inheritdoc
     */
    public function isSeekable()
    {
        if (!$this->handle) {
            return false;
        }

        if ($this->getMetadata('seekable')) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->isSeekable()) {
            throw new \RuntimeException('Stream is not seekable.');
        }

        fseek($this->handle, $offset, $whence);
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        if (!$this->isSeekable()) {
            throw new \RuntimeException('Cannot rewind, stream is not seekable.');
        }

        $this->seek(0);
    }

    /**
     * @inheritdoc
     */
    public function isWritable()
    {
        if (!$this->handle) {
            return false;
        }

        $mode = $this->getMetadata('mode');

        if (!$mode) {
            return false;
        }

        // Nix 'b'inary and 't'ext only identifiers from the mode.
        $mode = rtrim(rtrim($mode, 'b'), 't');

        if ($mode === 'r') {
            // 'r' is the only read-only mode.
            // The rest are read-write or write-only.
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function write($string)
    {
        if (!$this->isWritable($this->handle)) {
            throw new \RuntimeException('Stream is not writable.');
        }

        if (!is_string($string)) {
            throw new \RuntimeException('Stream write value must be a string.');
        }

        return fwrite($this->handle, $string);
    }

    /**
     * @inheritdoc
     */
    public function isReadable()
    {
        if (!$this->handle) {
            return false;
        }

        $mode = $this->getMetadata('mode');

        if (!$mode) {
            return false;
        }

        // Nix 'b'inary and 't'ext only identifiers from the mode.
        $mode = rtrim(rtrim($mode, 'b'), 't');
        // Grab the last character of the mode.
        $readIdentifier = substr($mode, -1, 1);

        if ($readIdentifier === 'r' || $readIdentifier === '+') {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function read($length)
    {
        if (!$this->isReadable()) {
            throw new \RuntimeException('Stream is not readable.');
        }

        $data = fread($this->handle, $length);

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getContents()
    {
        if (!$this->isReadable()) {
            throw new \RuntimeException('Stream is not readable.');
        }

        return stream_get_contents($this->handle);
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($key = null)
    {
        $metaData = stream_get_meta_data($this->handle);

        if (is_null($key)) {
            return $metaData;
        }

        if (isset($metaData[$key])) {
            return $metaData[$key];
        }

        return null;
    }
}
