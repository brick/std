<?php

declare(strict_types=1);

namespace Brick\Std\Tests\Curl;

use Brick\Std\Curl\Curl;
use PHPUnit\Framework\TestCase;

class CurlTest extends TestCase
{
    public function testConstructorShouldInitialCurl()
    {
        $curl = new Curl();

        $this->assertInternalType('object', $curl);
    }

    public function testCurlClassCanBeCloned()
    {
        $curl = new Curl();
        $newCurl = clone $curl;

        $this->assertInstanceOf(Curl::class, $newCurl);
    }

    public function testGetInfoWithSpecificOption()
    {
        $curl = new Curl();

        $this->assertSame(0, $curl->getInfo(CURLINFO_HTTP_CODE));
    }

    public function testGetInfo()
    {
        $expectedArray = [
            'url' => '',
            'content_type' => null,
            'http_code' => 0,
            'header_size' => 0,
            'request_size' => 0,
            'filetime' => 0,
            'ssl_verify_result' => 0,
            'redirect_count' => 0,
            'total_time' => 0.0,
            'namelookup_time' => 0.0,
            'connect_time' => 0.0,
            'pretransfer_time' => 0.0,
            'size_upload' => 0.0,
            'size_download' => 0.0,
            'speed_download' => 0.0,
            'speed_upload' => 0.0,
            'download_content_length' => -1.0,
            'upload_content_length' => -1.0,
            'starttransfer_time' => 0.0,
            'redirect_time' => 0.0,
            'redirect_url' => '',
            'primary_ip' => '',
            'certinfo' => [],
            'primary_port' => 0,
            'local_ip' => '',
            'local_port' => 0,
        ];
        $curl = new Curl();

        $this->assertSame($expectedArray, $curl->getInfo());
    }

    public function testSetOptionShouldBeSet()
    {
        $curl = new Curl();

        $this->assertNull($curl->setOption(CURLOPT_URL, 'http://exampl.ecom'));
    }

    public function testSetOptionsShouldBeSet()
    {
        $curlOpts = [
            CURLOPT_URL => 'http://exampl.ecom',
            CURLOPT_RETURNTRANSFER => true,
        ];
        $curl = new Curl();

        $this->assertNull($curl->setOptions($curlOpts));
    }

    public function testGetVersion()
    {
        $curl = new Curl();

        $this->assertInternalType('array', $curl->getVersion());
        $this->assertContains('version_number', $curl->getVersion());
    }

    public function testExecute()
    {
        $curl = new Curl('http://api.waqi.info/feed/taipei/?token=demo');

        $this->assertSame('ok', json_decode($curl->execute(), true)['status']);
    }

    /**
     * @expectedException        Brick\Std\Curl\CurlException
     * @expectedExceptionMessage cURL request failed: No URL set!.
     */
    public function testExecuteShouldReturnCurlException()
    {
        $curl = new Curl();
        $curl->execute();
    }
}
