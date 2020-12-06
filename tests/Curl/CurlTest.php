<?php

declare(strict_types=1);

namespace Brick\Std\Tests\Curl;

use Brick\Std\Curl\Curl;
use Brick\Std\Curl\CurlException;
use PHPUnit\Framework\TestCase;

class CurlTest extends TestCase
{
    public function testGetInfoWithSpecificOption()
    {
        $curl = new Curl();

        $this->assertSame(0, $curl->getInfo(CURLINFO_HTTP_CODE));
    }

    public function testGetInfo()
    {
        $curl = new Curl();

        $info = $curl->getInfo();

        self::assertSame('', $info['url']);
        self::assertSame(0, $info['http_code']);
    }

    public function testSetOption()
    {
        $curl = new Curl();
        $curl->setOption(CURLOPT_URL, 'http://example.com');

        $this->assertSame('http://example.com', $curl->getInfo(CURLINFO_EFFECTIVE_URL));
    }

    public function testSetOptions()
    {
        $curlOpts = [
            CURLOPT_URL => 'http://example.com',
        ];
        $curl = new Curl();
        $curl->setOptions($curlOpts);

        $this->assertSame('http://example.com', $curl->getInfo(CURLINFO_EFFECTIVE_URL));
    }

    public function testGetVersion()
    {
        $curl = new Curl();

        $this->assertArrayHasKey('version_number', $curl->getVersion());
    }

    public function testExecute()
    {
        $curl = new Curl('file://' . __FILE__);

        $this->assertSame('<?php', substr($curl->execute(), 0, 5));
    }

    public function testExecuteShouldThrowCurlException()
    {
        $curl = new Curl();

        $this->expectException(CurlException::class);
        $this->expectDeprecationMessage('cURL request failed: No URL set!');
        $curl->execute();
    }
}
