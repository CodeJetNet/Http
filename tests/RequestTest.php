<?php

namespace CodeJet\Http;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    // Request Method

    public function testCanSetAndGetRequestMethod()
    {
        $request = new Request();
        $postRequest = $request->withMethod('POST');

        $this->assertNotSame($request, $postRequest);
        $this->assertSame('POST', $postRequest->getMethod());
    }

    // Request Target

    public function testRequestTargetReturnsDefault()
    {
        $request = new Request();
        $this->assertSame('/', $request->getRequestTarget());
    }

    public function testCanSetAndGetRequestTarget()
    {
        $request = new Request();
        $requestWithRequestTarget = $request->withRequestTarget('/path?query=skittles');
        $this->assertNotSame($request, $requestWithRequestTarget);

        $this->assertSame('/path?query=skittles', $requestWithRequestTarget->getRequestTarget());
    }

    public function testCanGetRequestTargetFromUri()
    {
        $uri = new Uri('http://test.example.com/path?query=skittles');
        $request = (new Request())->withUri($uri);

        $this->assertSame('/path?query=skittles', $request->getRequestTarget());
    }

    // Uri

    public function testCanSetAndGetUri()
    {
        $request = new Request();

        $uri = new Uri();

        $requestWithUri = $request->withUri($uri);

        $this->assertNotSame($request, $requestWithUri);
        $this->assertSame($uri, $requestWithUri->getUri());
    }

    public function testSetUriAlsoSetsHostHeader()
    {
        $request = new Request();
        $uri = new Uri('http://test.example.com');
        $requestWithHostHeader = $request->withUri($uri);

        $this->assertSame('test.example.com', $requestWithHostHeader->getHeader('host'));
    }

    public function testSetUriReplacesHostHeader()
    {
        $request = (new Request())->withHeader('host', 'overwritten.host.example.com');
        $uri = new Uri('http://updated.host.example.com');
        $requestWithUpdatedHostHeader = $request->withUri($uri);

        $this->assertSame('updated.host.example.com', $requestWithUpdatedHostHeader->getHeader('host'));
    }

    public function testCanPreserveHostWhenSettingUriWithDifferentHost()
    {
        $request = (new Request())->withHeader('host', 'preserved.host.example.com');
        $uri = new Uri('http://unused.host.example.com');
        $requestWithPreservedHostHeader = $request->withUri($uri, true);

        $this->assertSame('preserved.host.example.com', $requestWithPreservedHostHeader->getHeader('host'));
    }
}
