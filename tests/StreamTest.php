<?php

namespace CodeJet\Http;

class StreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructOnlyAcceptsStreamResourceHandler()
    {
        $stream = new Stream('strings-are-not-stream-handlers');
    }

    public function testCanDetachStreamHandler()
    {
        $handle = fopen('php://temp', 'r');
        $stream = new Stream($handle);

        // Detached resources is returned
        $this->assertSame($handle, $stream->detach());

        // Null is returned since the resource is already detached.
        $this->assertNull($stream->detach());
    }

    public function testCanCloseStreamHandler()
    {
        $handle = fopen('php://temp', 'r');
        $stream = new Stream($handle);
        $stream->close();

        $this->assertFalse(is_resource($handle));
    }

    // Meta Data.

    public function testCanGetStreamMetaData()
    {
        $handle = fopen('php://temp', 'r');
        $stream = new Stream($handle);

        $this->assertSame($stream->getMetadata(), stream_get_meta_data($handle));
    }

    public function testCanGetSingleMetaDataItemValue()
    {
        $handle = fopen('php://temp', 'r');
        $stream = new Stream($handle);

        $metaData = stream_get_meta_data($handle);

        $this->assertSame($metaData['uri'], $stream->getMetadata('uri'));
    }

    public function testGetNonExistentMetaDataItemReturnsNull()
    {
        $handle = fopen('php://temp', 'r');
        $stream = new Stream($handle);

        $this->assertNull($stream->getMetadata('this-key-does-not-exist'));
    }

    // Is Seekable.

    public function testIsSeekable()
    {
        $handle = fopen('php://temp', 'r+');
        $stream = new Stream($handle);

        $metaData = stream_get_meta_data($handle);

        $this->assertSame($metaData['seekable'], $stream->isSeekable());
    }

    // Is Readable.

    public function testIsReadable()
    {
        $readOnlyStream = new Stream(fopen('php://stdin', 'r'));
        $this->assertTrue($readOnlyStream->isReadable());

        $writeOnlyStream = new Stream(fopen('php://stdout', 'w'));
        $this->assertFalse($writeOnlyStream->isReadable());
    }

    public function testIsNotReadableAfterClose()
    {
        $stream = new Stream(fopen('php://temp', 'r'));
        $stream->close();

        $this->assertFalse($stream->isReadable());
    }

    // Read

    public function testReadPartOfStreamStream()
    {
        $handle = fopen('php://temp', 'w+');
        fwrite($handle, 'some awesome text');
        rewind($handle);

        $stream = new Stream($handle);

        $this->assertSame('some', $stream->read(4));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testReadAfterCloseThrowsRuntimeException()
    {
        $stream = new Stream(fopen('php://temp', 'r'));
        $stream->close();
        $stream->read(4089);
    }

    // Get Contents

    public function testGetRemainingStreamContent()
    {
        $handle = fopen('php://temp', 'w+');
        fwrite($handle, 'some awesome text');
        fseek($handle, -4, SEEK_END);

        $stream = new Stream($handle);

        $this->assertSame('text', $stream->getContents());
    }

    // Is Writable.

    public function testIsWritable()
    {
        $writeOnlyStream = new Stream(fopen('php://stdout', 'w'));
        $this->assertTrue($writeOnlyStream->isWritable());

        $readOnlyStream = new Stream(fopen('php://stdin', 'r'));
        $this->assertFalse($readOnlyStream->isWritable());
    }

    // Write

    public function testWriteToStream()
    {
        $handle = fopen('php://temp', 'w+');

        $stream = new Stream($handle);
        $stream->write('some awesome text');

        rewind($handle);

        $this->assertSame('some awesome text', stream_get_contents($handle));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCannotWriteAfterClose()
    {
        $stream = new Stream(fopen('php://temp', 'w+'));
        $stream->close();
        $stream->write('This will throw an exception');
    }

    // Seek

    public function testSeek()
    {
        $handle = fopen('php://temp', 'w+');
        fwrite($handle, 'some awesome text');

        $stream = new Stream($handle);
        $stream->seek(5);

        $this->assertSame('awesome text', stream_get_contents($handle));
    }

    // Tell

    public function testTell()
    {
        $handle = fopen('php://temp', 'w+');
        fwrite($handle, 'some awesome text');
        fseek($handle, 5);

        $stream = new Stream($handle);

        $this->assertSame(5, $stream->tell());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCannotTellAfterClose()
    {
        $stream = new Stream(fopen('php://temp', 'w+'));
        $stream->close();
        $stream->tell();
    }

    // Rewind

    public function testRewind()
    {
        $handle = fopen('php://temp', 'w+');
        fwrite($handle, 'some awesome text');

        $stream = new Stream($handle);
        $stream->rewind();

        $this->assertSame(0, ftell($handle));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCannotRewindAfterStreamClose()
    {
        $stream = new Stream(fopen('php://stdout', 'a'));
        $stream->close();
        $stream->rewind();
    }

    // End of file.

    public function testEndOfFile()
    {
        $handle = fopen('php://temp', 'r');

        $stream = new Stream($handle);

        while (!feof($handle)) {
            fread($handle, 4089);
        }

        $this->assertTrue($stream->eof());
    }

    public function testEndOfFileReturnsTrueAfterClosed()
    {
        $stream = new Stream(fopen('php://temp', 'r'));
        $stream->close();

        $this->assertTrue($stream->eof());
    }

    // Get Size.

    public function getSizeReturnNullIfStreamIsDetached()
    {
        $stream = new Stream(fopen('php://temp', 'r'));
        $stream->detach();

        $this->assertNull($stream->getSize());
    }

    public function testGetSize()
    {
        $handle = fopen('php://temp', 'r');
        fwrite($handle, 'An awesome string.');

        $stream = new Stream($handle);

        $fstatData = fstat($handle);

        $this->assertSame($fstatData['size'], $stream->getSize());
    }

    // To String

    public function testToStringReturnsAllContentDespiteSeekLocation()
    {
        $handle = fopen('php://temp', 'w+');
        fwrite($handle, 'some awesome text');
        fseek($handle, 5);

        $stream = new Stream($handle);

        $this->assertSame('some awesome text', (string)$stream);
    }

    public function testToStringReturnsEmptyStringWhenStreamNotReadable()
    {
        $stream = new Stream(fopen('php://stdout', 'a'));
        $string = (string)$stream;

        $this->assertTrue(is_string($string));
        $this->assertTrue(empty($string));
    }

    public function testToStringReturnsEmptyStringWhenStreamClosed()
    {
        $stream = new Stream(fopen('php://temp', 'r'));
        $stream->close();
        $string = (string)$stream;

        $this->assertTrue(is_string($string));
        $this->assertTrue(empty($string));
    }
}
