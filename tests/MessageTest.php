<?php

namespace CodeJet\Http;

use CodeJet\Http\Stub\MessageStub;
use CodeJet\Http\Stub\MockStream;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    // Protocol

    public function testCanSetAndGetProtocolVersion()
    {
        $message = new MessageStub();

        // Default Protocol Version
        $this->assertSame('1.1', $message->getProtocolVersion());

        $messageWithNewProtocol = $message->withProtocolVersion('1.0');
        $this->assertNotSame($message, $messageWithNewProtocol);
        $this->assertSame('1.0', $messageWithNewProtocol->getProtocolVersion());
    }

    /**
     * The PSR7 Docs don't say the withProtocolVersion() method can throw an exception
     * so this may get purged.?.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testCanOnlySetValidProtocolVersions()
    {
        $message = new MessageStub();
        $message->withProtocolVersion('invalid-protocol-version');
    }

    // Headers

    public function testGetHeadersWhenNoneAreSetReturnsEmptyArray()
    {
        $message = new MessageStub();
        $this->assertTrue(is_array($message->getHeaders()));
    }

    public function testGetNonExistentHeaderReturnsEmptyArray()
    {
        $message = new MessageStub();
        $missingHeader = $message->getHeader('this-header-does-not-exist');
        $this->assertTrue(is_array($missingHeader));
        $this->assertTrue(empty($missingHeader));
    }

    public function testGetNonExistentHeaderLineReturnsEmptyString()
    {
        $message = new MessageStub();
        $missingHeaderLine = $message->getHeaderLine('this-header-does-not-exist');
        $this->assertTrue(is_string($missingHeaderLine));
        $this->assertEmpty($missingHeaderLine);
    }

    public function testGetHeaderLineReturnsCSVOfHeaderValues()
    {
        $message = (new MessageStub)
            ->withHeader('accept', 'text/html')
            ->withAddedHeader('accept', 'application/xhtml+xml');

        $this->assertSame('text/html, application/xhtml+xml', $message->getHeaderLine('accept'));
    }

    public function testCanSetHeader()
    {
        $message = new MessageStub();
        $this->assertTrue(empty($message->getHeaders()));

        $messageWithHeaders = $message->withHeader('content-language', 'sw');

        $this->assertNotSame($message, $messageWithHeaders);
        $this->assertSame('sw', $messageWithHeaders->getHeader('content-language'));
    }

    public function testGetHeaderIsNotCaseSensitive()
    {
        $message = (new MessageStub())->withHeader('CONTENT-language', 'sw');
        $this->assertSame('sw', $message->getHeader('content-LANGUAGE'));
    }

    /**
     * PSR7 Spec states: While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified...  So, 'withHeader' must need to replace
     * the stored case for the header name.
     */
    public function testWithHeaderReplacesStoredHeaderCase()
    {
        $message = (new MessageStub())->withHeader('CONTENT-language', 'sw');
        $this->assertTrue(key_exists('CONTENT-language', $message->getHeaders()));

        $messageWithReplacedHeader = $message->withHeader('content-LANGUAGE', 'sw');
        $this->assertTrue(key_exists('content-LANGUAGE', $messageWithReplacedHeader->getHeaders()));
        $this->assertFalse(key_exists('CONTENT-language', $messageWithReplacedHeader->getHeaders()));
    }

    public function testCanAddHeaderValue()
    {
        $message = (new MessageStub())
            ->withHeader('cookie', 'tastes=good')
            ->withAddedHeader('cookie', 'type=oatmeal');

        $cookieHeaders = $message->getHeader('cookie');
        $this->assertTrue((count($cookieHeaders) == 2));
    }

    public function testCanRemoveHeader()
    {
        $message = (new MessageStub())->withHeader('content-language', 'sw');
        $this->assertSame('sw', $message->getHeader('content-language'));

        $messageWithoutHeader = $message->withoutHeader('content-language');
        $this->assertNotSame($message, $messageWithoutHeader);
        $this->assertTrue(empty($messageWithoutHeader->getHeader('content-language')));
    }

    // Body

    public function testCanSetAndGetBody()
    {
        $message = new MessageStub();
        $this->assertTrue(is_null($message->getBody()));

        $mockStream = new MockStream();
        $messageWithBody = $message->withBody($mockStream);

        $this->assertNotSame($message, $messageWithBody);
        $this->assertSame($mockStream, $messageWithBody->getBody());
    }
}
