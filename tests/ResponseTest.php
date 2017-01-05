<?php

namespace CodeJet\Http;

use Psr\Http\Message\ResponseInterface;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testGetStatusCode200WhenNoneIsSet()
    {
        $response = new Response();

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetEmptyReasonPhraseWhenNoneIsSet()
    {
        $response = new Response();

        $reasonPhrase = $response->getReasonPhrase();

        $this->assertTrue(is_string($reasonPhrase));
        $this->assertTrue(empty($reasonPhrase));
    }

    public function testChangeStatusReturnsNewObject()
    {
        $response = new Response();

        $responseWithNewStatus = $response->withStatus(200);

        $this->assertInstanceOf(ResponseInterface::class, $responseWithNewStatus);
        $this->assertNotSame($response, $responseWithNewStatus);
    }

    public function testCanChangeCodeAndPhrase()
    {
        $response = new Response();

        $responseWithNewStatus = $response->withStatus(200, 'a sweet reason phrase.');

        $this->assertSame(200, $responseWithNewStatus->getStatusCode());
        $this->assertSame('a sweet reason phrase.', $responseWithNewStatus->getReasonPhrase());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithNonIntegerStatusCodeThrowsInvalidArgumentException()
    {
        $response = new Response();
        $response->withStatus('This is not a 3 digit integer.');
    }
}
