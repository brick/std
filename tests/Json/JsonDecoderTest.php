<?php

namespace Brick\Std\Tests\Json;

use Brick\Std\Json\JsonDecoder;

use PHPUnit\Framework\TestCase;

/**
 * Tests for class JsonDecoder.
 */
class JsonDecoderTest extends TestCase
{
    /**
     * @dataProvider providerDecode
     *
     * @param string $json
     * @param mixed  $expected
     *
     * @return void
     */
    public function testDecode(string $json, $expected) : void
    {
        $decoder = new JsonDecoder();
        $this->assertSame($expected, $decoder->decode($json));
    }

    /**
     * @return array
     */
    public function providerDecode() : array
    {
        return [
            ['123', 123],
            ['"ABC"', 'ABC'],
            ['["a", "b"]', ['a', 'b']]
        ];
    }

    /**
     * @dataProvider providerDecodeInvalidJson
     * @expectedException \Brick\Std\Json\JsonException
     *
     * @param string $json
     *
     * @return void
     */
    public function testDecodeInvalidJson(string $json) : void
    {
        $decoder = new JsonDecoder();
        $decoder->decode($json);
    }

    /**
     * @return array
     */
    public function providerDecodeInvalidJson() : array
    {
        return [
            [''],
            ['{'],
            ['['],
            [','],
            ['[],'],
            ['123,'],
            ['"123']
        ];
    }

    /**
     * @return void
     */
    public function testDecodeObjectsAsObjects() : void
    {
        $decoder = new JsonDecoder();

        $json = '{"a": "b", "c": "d"}';

        $expected = new \StdClass;
        $expected->a = 'b';
        $expected->c = 'd';

        $this->assertEquals($expected, $decoder->decode($json));
    }

    /**
     * @return void
     */
    public function testDecodeObjectsAsArrays() : void
    {
        $decoder = new JsonDecoder();
        $decoder->decodeObjectsAsArrays(true);

        $json = '{"a": "b", "c": "d"}';

        $expected = ['a' => 'b', 'c' => 'd'];

        $this->assertSame($expected, $decoder->decode($json));
    }

    /**
     * @return void
     */
    public function testDecodeBigIntAsFloat() : void
    {
        $decoder = new JsonDecoder();

        $json = '123456789123456789123456789123456789123456789123456789';

        $expected = 1.2345678912345678E+53;
        $actual = $decoder->decode($json);

        $this->assertTrue(is_float($actual));
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testDecodeBigIntAsString() : void
    {
        $decoder = new JsonDecoder();
        $decoder->decodeBigIntAsString(true);

        $json = '123456789123456789123456789123456789123456789123456789';
        $expected = $json;

        $this->assertSame($expected, $decoder->decode($json));
    }

    /**
     * @expectedException \Brick\Std\Json\JsonException
     * @expectedExceptionMessage Maximum stack depth exceeded
     *
     * @return void
     */
    public function testMaxDepth() : void
    {
        $decoder = new JsonDecoder();
        $decoder->setMaxDepth(1);

        $decoder->decode('{"a": "b"}');
    }
}
