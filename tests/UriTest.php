<?php

namespace CodeJet\Http;

class UriTest extends \PHPUnit_Framework_TestCase
{
    const URI_STRING = 'https://username:password@test.example.com:9000/somePath?neatQuery=superValue#happyFragment';
    const HOST = 'test.example.com';
    const SCHEME = 'https';
    const PORT = 9000;
    const USER_INFO = 'username:password';
    const PATH = '/somePath';
    const QUERY = 'neatQuery=superValue';
    const FRAGMENT = 'happyFragment';

    public function testUriConstructSetsAllParts()
    {
        $uri = new Uri(self::URI_STRING);

        $this->assertTrue($uri->hasScheme());
        $this->assertTrue($uri->hasUserInfo());
        $this->assertTrue($uri->hasHost());
        $this->assertTrue($uri->hasPort());
        $this->assertTrue($uri->hasPath());
        $this->assertTrue($uri->hasQuery());
        $this->assertTrue($uri->hasFragment());
    }

    public function testToStringReturnsSameUri()
    {
        $uri = new Uri(self::URI_STRING);
        $this->assertSame(self::URI_STRING, (string)$uri);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsInvalidArgumentExceptionOnIndistinguishableUriString()
    {
        $uri = new Uri(":");
    }

    // Host

    public function testCanSetAndGetHost()
    {
        $uri = new Uri();

        // Blank Uri should have no host.
        $this->assertFalse($uri->hasHost());

        $uriWithHost = $uri->withHost("test.example.com");

        // The two Uri objects should be different.
        $this->assertNotSame($uri, $uriWithHost);

        // The old (immutable) object should still have a blank host.
        $this->assertFalse($uri->hasHost());

        // Our new Uri object should now have a host.
        $this->assertTrue($uriWithHost->hasHost());
        // and it should be the value we set it to.
        $this->assertSame("test.example.com", $uriWithHost->getHost());
    }

    public function testCanChangeHost()
    {
        $uri = new Uri(self::URI_STRING);
        $this->assertTrue($uri->hasHost());
        $this->assertSame(self::HOST, $uri->getHost());

        $newHost = "test.codejet.net";
        $newUri = $uri->withHost($newHost);
        $this->assertNotSame($uri, $newUri);
        $this->assertSame($newHost, $newUri->getHost());

        $expectedUri = str_ireplace(self::HOST, $newHost, self::URI_STRING);
        $this->assertSame($expectedUri, (string)$newUri);
    }

    public function testHostNormalizedToLowercase()
    {
        $uri = new Uri('http://TEST.EXAMPLE.COM');
        $this->assertSame('test.example.com', $uri->getHost());
        $this->assertSame('http://test.example.com', (string)$uri);
    }

    // Scheme

    public function testCanSetAndGetScheme()
    {
        $uri = new Uri();

        // Blank Uri should have no scheme.
        $this->assertFalse($uri->hasScheme());

        $uriWithScheme = $uri->withScheme('http');

        $this->assertNotSame($uri, $uriWithScheme);
        $this->assertFalse($uri->hasScheme());
        $this->assertTrue($uriWithScheme->hasScheme());
        $this->assertSame('http', $uriWithScheme->getScheme());
    }

    public function testCanChangeScheme()
    {
        $uri = new Uri(self::URI_STRING);
        $this->assertTrue($uri->hasScheme());
        $this->assertSame(self::SCHEME, $uri->getScheme());

        $newScheme = "ftp";
        $uriWithScheme = $uri->withScheme($newScheme);

        $this->assertNotSame($uri, $uriWithScheme);
        $this->assertSame("ftp", $uriWithScheme->getScheme());

        $expectedUri = str_ireplace(self::SCHEME, $newScheme, self::URI_STRING);
        $this->assertSame($expectedUri, (string)$uriWithScheme);
    }

    public function testSchemeNormalizedToLowerCase()
    {
        $uri = (new Uri())->withScheme('HTTP');
        $this->assertSame('http', $uri->getScheme());
    }

    // Port

    public function testCanSetAndGetPort()
    {
        $uri = new Uri();

        // Blank Uri should have no Port.
        $this->assertFalse($uri->hasPort());

        $newPort = 8080;
        $uriWithPort = $uri->withPort($newPort);

        $this->assertNotSame($uri, $uriWithPort);
        $this->assertTrue($uriWithPort->hasPort());
        $this->assertSame($newPort, $uriWithPort->getPort());
    }

    public function testCanChangePort()
    {
        $uri = new Uri(self::URI_STRING);
        $this->assertTrue($uri->hasPort());
        $this->assertSame(self::PORT, $uri->getPort());

        $newPort = 8080;
        $uriWithNewPort = $uri->withPort($newPort);

        $this->assertNotSame($uri, $uriWithNewPort);
        $this->assertSame($newPort, $uriWithNewPort->getPort());

        $expectedUri = str_ireplace(self::PORT, $newPort, self::URI_STRING);
        $this->assertSame($expectedUri, (string)$uriWithNewPort);
    }

    /**
     * @dataProvider defaultSchemeAndPortWithExpectedAuthorityProvider
     */
    public function testDefaultPortNotReturnedViaGetAuthority($uriString, $expectedAuthority)
    {
        $uri = new Uri($uriString);
        $this->assertSame($expectedAuthority, $uri->getAuthority());
    }

    public function defaultSchemeAndPortWithExpectedAuthorityProvider()
    {
        return [
            ['http://test.example.com:80', 'test.example.com'],
            ['https://test.example.com:443', 'test.example.com']
        ];
    }

    public function testDefaultPortsReturnNull()
    {
        $uri = new Uri('http://test.example.com:80');
        $this->assertNull($uri->getPort());
    }

    // User Info

    public function testCanSetAndGetUserInfo()
    {
        $uri = new Uri();
        $this->assertFalse($uri->hasUserInfo());

        $user = "newUser";
        $password = "newPassword";
        $expectedUserInfo = $user . ":" . $password;
        $uriWithUserInfo = $uri->withUserInfo($user, $password);

        $this->assertNotSame($uri, $uriWithUserInfo);
        $this->assertSame($expectedUserInfo, $uriWithUserInfo->getUserInfo());
    }

    public function testCanChangeUserInfo()
    {
        $uri = new Uri(self::URI_STRING);
        $this->assertTrue($uri->hasUserInfo());
        $this->assertSame(self::USER_INFO, $uri->getUserInfo());

        $user = "newUser";
        $password = "newPassword";
        $expectedUserInfo = $user . ":" . $password;
        $uriWithUserInfo = $uri->withUserInfo($user, $password);

        $this->assertNotSame($uri, $uriWithUserInfo);
        $this->assertSame($expectedUserInfo, $uriWithUserInfo->getUserInfo());

        $expectedUri = str_ireplace(self::USER_INFO, $expectedUserInfo, self::URI_STRING);
        $this->assertSame($expectedUri, (string)$uriWithUserInfo);
    }

    // Path

    public function testCanSetAndGetPath()
    {
        $uri = new Uri();
        $this->assertFalse($uri->hasPath());

        $path = "/folder/structure";
        $uriWithPath = $uri->withPath($path);

        $this->assertNotSame($uri, $uriWithPath);
        $this->assertSame($path, $uriWithPath->getPath());
    }

    public function testCanChangePath()
    {
        $uri = new Uri(self::URI_STRING);
        $this->assertTrue($uri->hasPath());
        $this->assertSame(self::PATH, $uri->getPath());

        $newPath = "/folder/structure";
        $uriWithNewPath = $uri->withPath($newPath);

        $this->assertNotSame($uri, $uriWithNewPath);
        $this->assertSame($newPath, $uriWithNewPath->getPath());

        $expectedUri = str_ireplace(self::PATH, $newPath, self::URI_STRING);
        $this->assertSame($expectedUri, (string)$uriWithNewPath);
    }

    // Query

    public function testCanSetAndGetQuery()
    {
        $uri = new Uri();
        $this->assertFalse($uri->hasQuery());

        $query = "awesomeVar=mindPoofValue";
        $uriWithQuery = $uri->withQuery($query);

        $this->assertNotSame($uri, $uriWithQuery);
        $this->assertSame($query, $uriWithQuery->getQuery());
    }

    public function testCanChangeQuery()
    {
        $uri = new Uri(self::URI_STRING);
        $this->assertTrue($uri->hasQuery());
        $this->assertSame(self::QUERY, $uri->getQuery());

        $query = "awesomeVar=mindPoofValue";
        $uriWithNewQuery = $uri->withQuery($query);

        $this->assertNotSame($uri, $uriWithNewQuery);
        $this->assertSame($query, $uriWithNewQuery->getQuery());

        $expectedUri = str_replace(self::QUERY, $query, self::URI_STRING);
        $this->assertSame($expectedUri, (string)$uriWithNewQuery);
    }

    // Fragment

    public function testCanSetAndGetFragment()
    {
        $uri = new Uri();
        $this->assertFalse($uri->hasFragment());

        $fragment = "slurpyTab";
        $uriWithFragment = $uri->withFragment($fragment);

        $this->assertNotSame($uri, $uriWithFragment);
        $this->assertSame($fragment, $uriWithFragment->getFragment());
    }

    public function testCanChangeFragment()
    {
        $uri = new Uri(self::URI_STRING);
        $this->assertTrue($uri->hasFragment());
        $this->assertSame(self::FRAGMENT, $uri->getFragment());

        $fragment = "slurpyTab";
        $uriWithNewFragment = $uri->withFragment($fragment);

        $this->assertNotSame($uri, $uriWithNewFragment);
        $this->assertSame($fragment, $uriWithNewFragment->getFragment());

        $expectedUri = str_replace(self::FRAGMENT, $fragment, self::URI_STRING);
        $this->assertSame($expectedUri, (string)$uriWithNewFragment);
    }
}
