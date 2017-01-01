<?php

namespace CodeJet\Http;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

abstract class Message implements MessageInterface
{
    /**
     * @var string
     */
    protected $protocolVersion = '1.1';

    /**
     * @var array[string]
     */
    private $validProtocolVersions = ['1.0', '1.1'];

    /**
     * @var array[string]
     */
    protected $headers = [];

    /**
     * @var StreamInterface
     */
    protected $body;

    /**
     * @var array[string]
     */
    protected $normalizedHeaderIndex = [];

    /**
     * @inheritDoc
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * @inheritDoc
     */
    public function withProtocolVersion($protocolVersion)
    {
        if (!in_array($protocolVersion, $this->validProtocolVersions)) {
            throw new InvalidArgumentException('Invalid Protocol Version.');
        }

        $clone = clone $this;
        $clone->protocolVersion = $protocolVersion;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    public function hasHeader($name)
    {
        if (isset($this->normalizedHeaderIndex[strtolower($name)])) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getHeader($name)
    {
        if (!$this->hasHeader($name)) {
            return [];
        }

        return $this->headers[$this->normalizedHeaderIndex[strtolower($name)]];
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine($name)
    {
        if (!$this->hasHeader($name)) {
            return '';
        }

        return implode(", ", $this->getHeader($name));
    }

    /**
     * @inheritDoc
     */
    public function withHeader($name, $value)
    {
        $clone = clone $this;

        if ($this->hasHeader($name)) {
            unset($clone->headers[$this->normalizedHeaderIndex[strtolower($name)]]);
        }

        $clone->normalizedHeaderIndex[strtolower($name)] = $name;
        $clone->headers[$name] = $value;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withAddedHeader($name, $value)
    {
        if (!$this->hasHeader($name)) {
            return $this->withHeader($name, $value);
        }

        $headerIndex = $this->normalizedHeaderIndex[strtolower($name)];

        $clone = clone $this;

        if (!is_array($clone->headers[$headerIndex])) {
            $clone->headers[$headerIndex] = [
                $clone->headers[$headerIndex]
            ];
        }

        array_push($clone->headers[$headerIndex], $value);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader($name)
    {
        if (!$this->hasHeader($name)) {
            return $this;
        }

        $clone = clone $this;
        $normalizedHeaderIndex = $this->normalizedHeaderIndex[strtolower($name)];
        unset($clone->headers[$normalizedHeaderIndex]);
        unset($clone->normalizedHeaderIndex[strtolower($name)]);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function withBody(StreamInterface $body)
    {
        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }
}
