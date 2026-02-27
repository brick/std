<?php

declare(strict_types=1);

namespace Brick\Std\Tests\Curl;

use Brick\Std\Curl\Curl;
use Brick\Std\Curl\CurlException;
use PHPUnit\Framework\TestCase;

use function substr;

use const CURLINFO_EFFECTIVE_URL;
use const CURLINFO_HTTP_CODE;
use const CURLOPT_URL;

class CurlTest extends TestCase
{
    public function testGetInfoWithSpecificOption(): void
    {
        $curl = new Curl();

        self::assertSame(0, $curl->getInfo(CURLINFO_HTTP_CODE));
    }

    public function testGetInfo(): void
    {
        $curl = new Curl();

        $info = $curl->getInfo();

        self::assertSame('', $info['url']);
        self::assertSame(0, $info['http_code']);
    }

    public function testSetOption(): void
    {
        $curl = new Curl();
        $curl->setOption(CURLOPT_URL, 'http://example.com');

        self::assertSame('http://example.com', $curl->getInfo(CURLINFO_EFFECTIVE_URL));
    }

    public function testSetOptions(): void
    {
        $curlOpts = [
            CURLOPT_URL => 'http://example.com',
        ];
        $curl = new Curl();
        $curl->setOptions($curlOpts);

        self::assertSame('http://example.com', $curl->getInfo(CURLINFO_EFFECTIVE_URL));
    }

    public function testGetVersion(): void
    {
        $curl = new Curl();

        self::assertArrayHasKey('version_number', $curl->getVersion());
    }

    public function testExecute(): void
    {
        $curl = new Curl('file://' . __FILE__);

        self::assertSame('<?php', substr($curl->execute(), 0, 5));
    }

    public function testExecuteShouldThrowCurlException(): void
    {
        $curl = new Curl();

        $this->expectException(CurlException::class);
        $this->expectExceptionMessage('cURL request failed: No URL set');
        $curl->execute();
    }
}
