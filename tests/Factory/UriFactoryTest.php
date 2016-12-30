<?php

namespace CodeJet\Http\Factory;

class UriFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UriFactory
     */
    protected $uriFactory;

    public function setUp()
    {
        $this->uriFactory = new UriFactory();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsInvalidArgumentExceptionOnIndistinguishableUriString()
    {
        $notAString = new \stdClass();
        $uri = $this->uriFactory->createUri($notAString);
    }
}
