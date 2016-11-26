<?php
declare(strict_types=1);
namespace Viserio\Cookie\Tests;

use DateTime;
use Mockery as Mock;
use Narrowspark\TestingHelper\Traits\MockeryTrait;
use Psr\Http\Message\ServerRequestInterface as Request;
use Viserio\Cookie\Cookie;
use Viserio\Cookie\RequestCookie;
use Cake\Chronos\Chronos;

class RequestCookieTest extends \PHPUnit_Framework_TestCase
{
    use MockeryTrait;

    public function setUp()
    {
        parent::setUp();

        Chronos::setTestNow(new Chronos('Friday, 20-May-2011 15:25:52'));
    }

    /**
     * @dataProvider provideParsesFromCookieStringData
     */
    public function testFromSetCookieHeader($cookieString, Cookie $expectedCookie)
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('getHeader')->with('Set-Cookie')->andReturn($cookieString);

        $cookie = new RequestCookie();
        $setCookie = $cookie->fromSetCookieHeader($request);
        // $setCookie = $setCookie->withExpires($setCookie->getExpiresTime() - 1);

        $this->assertEquals($expectedCookie, $setCookie);
    }

    /**
     * @dataProvider provideParsesFromCookieStringData
     */
    public function testFromCookieHeader($cookieString, Cookie $expectedCookie)
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('getHeaderLine')->with('Cookie')->andReturn($cookieString);

        $cookie = new RequestCookie();
        $setCookie = $cookie->fromCookieHeader($request);

        $this->assertEquals($expectedCookie, $setCookie);
    }

    public function provideParsesFromCookieStringData()
    {
        return [
            [
                'some;',
                (new Cookie('some')),
            ],
            [
                'someCookie=',
                new Cookie('someCookie'),
            ],
            [
                'someCookie=someValue',
                (new Cookie('someCookie'))
                    ->withValue('someValue'),
            ],
            [
                'LSID=DQAAAK%2FEaem_vYg; Expires=Wed, 13 Jan 2021 22:23:01 GMT; Path=/accounts; Secure; HttpOnly',
                (new Cookie('LSID'))
                    ->withValue('DQAAAK/Eaem_vYg')
                    ->withPath('/accounts')
                    ->withExpires(new DateTime('Wed, 13 Jan 2021 22:23:01 GMT'))
                    ->withSecure(true)
                    ->withHttpOnly(true),
            ],
            [
                'HSID=AYQEVn%2F.DKrdst; Expires=Wed, 13 Jan 2021 22:23:01 GMT; Path=/; Domain=foo.com; HttpOnly',
                (new Cookie('HSID'))
                    ->withValue('AYQEVn/.DKrdst')
                    ->withDomain('.foo.com')
                    ->withPath('/')
                    ->withExpires(new DateTime('Wed, 13 Jan 2021 22:23:01 GMT'))
                    ->withHttpOnly(true),
            ],
            [
                'SSID=Ap4P%2F.GTEq; Expires=Wed, 13 Jan 2021 22:23:01 GMT; Path=/; Domain=foo.com; Secure; HttpOnly',
                (new Cookie('SSID'))
                    ->withValue('Ap4P/.GTEq')
                    ->withDomain('foo.com')
                    ->withPath('/')
                    ->withExpires(new DateTime('Wed, 13 Jan 2021 22:23:01 GMT'))
                    ->withSecure(true)
                    ->withHttpOnly(true),
            ],
            [
                'lu=Rg3vHJZnehYLjVg7qi3bZjzg; Domain=.example.com; Path=/; Expires=Tue, 15 Jan 2013 21:47:38 GMT; HttpOnly',
                (new Cookie('lu'))
                    ->withValue('Rg3vHJZnehYLjVg7qi3bZjzg')
                    ->withExpires(new DateTime('Tue, 15-Jan-2013 21:47:38 GMT'))
                    ->withPath('/')
                    ->withDomain('.example.com')
                    ->withHttpOnly(true),
            ],
            [
                'lu=Rg3vHJZnehYLjVg7qi3bZjzg; Domain=.example.com; Path=/; Max-Age=500; Secure; HttpOnly',
                (new Cookie('lu'))
                    ->withValue('Rg3vHJZnehYLjVg7qi3bZjzg')
                    ->withMaxAge(500)
                    ->withPath('/')
                    ->withDomain('.example.com')
                    ->withSecure(true)
                    ->withHttpOnly(true),
            ],
            [
                'lu=Rg3vHJZnehYLjVg7qi3bZjzg; Domain=.example.com; Path=/; Expires=Tue, 15 Jan 2013 21:47:38 GMT; Max-Age=500; Secure; HttpOnly',
                (new Cookie('lu'))
                    ->withValue('Rg3vHJZnehYLjVg7qi3bZjzg')
                    ->withExpires(new DateTime('Tue, 15-Jan-2013 21:47:38 GMT'))
                    ->withMaxAge(500)
                    ->withPath('/')
                    ->withDomain('.example.com')
                    ->withSecure(true)
                    ->withHttpOnly(true),
            ],
            [
                'lu=Rg3vHJZnehYLjVg7qi3bZjzg; Domain=.example.com; Path=/; Expires=Tue, 15 Jan 2013 21:47:38 GMT; Max-Age=500; Secure; HttpOnly',
                (new Cookie('lu'))
                    ->withValue('Rg3vHJZnehYLjVg7qi3bZjzg')
                    ->withExpires(new DateTime('Tue, 15 Jan 2013 21:47:38 GMT'))
                    ->withMaxAge(500)
                    ->withPath('/')
                    ->withDomain('.example.com')
                    ->withSecure(true)
                    ->withHttpOnly(true),
            ],
            [
                'lu=Rg3vHJZnehYLjVg7qi3bZjzg; Domain=.example.com; Path=/; Expires=Tue, 15 Jan 2013 21:47:38 GMT; Max-Age=500; Secure; HttpOnly',
                (new Cookie('lu'))
                    ->withValue('Rg3vHJZnehYLjVg7qi3bZjzg')
                    ->withExpires(new DateTime('Tue, 15-Jan-2013 21:47:38 GMT'))
                    ->withMaxAge(500)
                    ->withPath('/')
                    ->withDomain('.example.com')
                    ->withSecure(true)
                    ->withHttpOnly(true),
            ],
            [
                'lu=Rg3vHJZnehYLjVg7qi3bZjzg; Domain=.example.com; Path=/; Expires=Tue, 15 Jan 2013 21:47:38 GMT; Max-Age=500; Secure; HttpOnly; SameSite=strict',
                (new Cookie('lu'))
                    ->withValue('Rg3vHJZnehYLjVg7qi3bZjzg')
                    ->withExpires(new DateTime('Tue, 15-Jan-2013 21:47:38 GMT'))
                    ->withMaxAge(500)
                    ->withPath('/')
                    ->withDomain('.example.com')
                    ->withSecure(true)
                    ->withHttpOnly(true)
                    ->withSameSite('strict'),
            ],
        ];
    }
}
