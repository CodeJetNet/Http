<?php

namespace CodeJet\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Request
 * @package CodeJet\Http
 */
class Request extends Message implements RequestInterface
{
    const DEFAULT_REQUEST_TARGET = "/";

    /**
     * Request Method SHOULD be one of GET,PUT,POST,PATCH,DELETE
     * @var string
     */
    protected $method = 'GET';

    /**
     * @var UriInterface
     */
    protected $uri;

    /**
     * @var string
     */
    protected $requestTarget;

    /**
     * @inheritdoc
     */
    public function getRequestTarget()
    {
        if ($this->requestTarget) {
            return $this->requestTarget;
        }

        $requestTarget = self::DEFAULT_REQUEST_TARGET;

        if ($this->uri) {
            $requestTarget = $this->uri->getPath();

            if (!empty($this->uri->getQuery())) {
                $requestTarget .= '?' . $this->uri->getQuery();
            }
        }

        return $requestTarget;
    }

    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @inheritdoc
     */
    public function withRequestTarget($requestTarget)
    {
        $clone = clone $this;
        $clone->requestTarget = $requestTarget;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withMethod($method)
    {
        $clone = clone $this;
        $clone->method = $method;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @inheritdoc
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $clone = clone $this;
        $clone->uri = $uri;

        if (!empty($uri->getHost()) && $preserveHost === false) {
            return $clone->withHeader('host', $uri->getHost());
        }

        if (empty($clone->getHeader('host')) && !empty($uri->getHost()) && $preserveHost === true) {
            $clone = $clone->withHeader('host', $uri->getHost());
        }

        return $clone;
    }
}
