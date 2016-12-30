<?php

namespace CodeJet\Http;

use Psr\Http\Message\UriInterface;
use InvalidArgumentException;

class Uri implements UriInterface
{
    /**
     * @var string
     */
    private $scheme = '';

    /**
     * @var string
     */
    private $userInfo = '';

    /**
     * @var string
     */
    private $host = '';

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $path = '';

    /**
     * @var string
     */
    private $query = '';

    /**
     * @var string
     */
    private $fragment = '';

    /**
     * @param string|array $uri
     * @throws InvalidArgumentException on non-string $uri argument
     */
    public function __construct($uri = '')
    {
        if (is_array($uri)) {
            $this->parseUriParts($uri);
        }

        if (is_string($uri) && !empty($uri)) {
            $parts = parse_url($uri);
            if (false === $parts) {
                throw new \InvalidArgumentException(
                    'The source URI string appears to be malformed'
                );
            }

            $this->parseUriParts($parts);
        }
    }

    public function __toString()
    {
        return $this->getUriString();
    }

    private function getUriString()
    {
        $uri = '';

        if ($this->hasScheme()) {
            $uri .= sprintf('%s://', $this->getScheme());
        }

        $uri .= $this->getAuthority() . $this->getPath();

        if ($this->hasQuery()) {
            $uri .= sprintf('?%s', $this->getQuery());
        }

        if ($this->hasFragment()) {
            $uri .= sprintf('#%s', $this->getFragment());
        }

        return $uri;
    }

    private function parseUriParts($parts)
    {
        $this->scheme = isset($parts['scheme']) ? $parts['scheme'] : '';
        $this->userInfo = isset($parts['user']) ? $parts['user'] : '';
        $this->host = isset($parts['host']) ? $parts['host'] : '';
        $this->port = isset($parts['port']) ? $parts['port'] : null;
        $this->path = isset($parts['path']) ? $parts['path'] : '';
        $this->query = isset($parts['query']) ? $parts['query'] : '';
        $this->fragment = isset($parts['fragment']) ? $parts['fragment'] : '';

        if (isset($parts['pass'])) {
            $this->userInfo .= ':' . $parts['pass'];
        }
    }

    public function hasScheme()
    {
        if (!empty($this->scheme)) {
            return true;
        }

        return false;
    }

    public function getScheme()
    {
        return strtolower($this->scheme);
    }

    public function getAuthority()
    {
        if (!$this->hasHost()) {
            return '';
        }

        $authority = $this->getHost();

        if ($this->hasUserInfo()) {
            $authority = $this->getUserInfo() . '@' . $authority;
        }

        if ($this->hasPort()) {
            $authority .= ':' . $this->getPort();
        }

        return $authority;
    }

    public function getUserInfo()
    {
        return $this->userInfo;
    }

    public function hasUserInfo()
    {
        if (!empty($this->userInfo)) {
            return true;
        }

        return false;
    }

    public function hasHost()
    {
        if (!empty($this->host)) {
            return true;
        }

        return false;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function hasPort()
    {
        if (!empty($this->port)) {
            return true;
        }

        return false;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function hasPath()
    {
        if (!empty($this->path)) {
            return true;
        }

        return false;
    }

    public function getPath()
    {

        return $this->path;
    }

    public function hasQuery()
    {
        if (!empty($this->query)) {
            return true;
        }

        return false;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function hasFragment()
    {
        if (!empty($this->fragment)) {
            return true;
        }

        return false;
    }

    public function getFragment()
    {
        return $this->fragment;
    }

    public function withScheme($scheme)
    {
        return $this->withModifiedParts(
            ['scheme' => $scheme]
        );
    }

    public function withUserInfo($user, $password = null)
    {
        return $this->withModifiedParts(
            [
                'user' => $user,
                'pass' => $password
            ]
        );
    }

    public function withHost($host)
    {
        return $this->withModifiedParts(
            ['host' => $host]
        );
    }

    public function withPort($port)
    {
        return $this->withModifiedParts(
            ['port' => $port]
        );
    }

    public function withPath($path)
    {
        return $this->withModifiedParts(
            ['path' => $path]
        );
    }

    public function withQuery($query)
    {
        return $this->withModifiedParts(
            ['query' => $query]
        );
    }

    public function withFragment($fragment)
    {
        return $this->withModifiedParts(
            ['fragment' => $fragment]
        );
    }

    /**
     * @param array $modifiedParts
     * @return Uri
     */
    private function withModifiedParts($modifiedParts)
    {
        $currentParts = parse_url($this->getUriString());

        $newUriParts = array_merge($currentParts, $modifiedParts);

        return new self($newUriParts);
    }
}
