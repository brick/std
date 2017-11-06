<?php

declare(strict_types=1);

namespace Brick\Std\Tests\Json;

use Brick\Std\Json\JsonDecoder;
use Brick\Std\Json\JsonException;

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

        $expected = new \stdClass;
        $expected->a = 'b';
        $expected->c = 'd';

        $this->assertEquals($expected, $decoder->decode($json));
    }

    /**
     * @return void
     */
    public function testDecodeObjectAsArray() : void
    {
        $decoder = new JsonDecoder();
        $decoder->decodeObjectAsArray(true);

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
     * @dataProvider providerInvalidMaxDepth
     * @expectedException \InvalidArgumentException
     *
     * @param int $maxDepth
     *
     * @return void
     */
    public function testInvalidMaxDepth(int $maxDepth) : void
    {
        $decoder = new JsonDecoder();
        $decoder->setMaxDepth($maxDepth);
    }

    /**
     * @return array
     */
    public function providerInvalidMaxDepth() : array
    {
        return [
            [-1],
            [0x7fffffff]
        ];
    }

    /**
     * @dataProvider providerMaxDepth
     *
     * @param string $json            The JSON string to encode.
     * @param int    $maxDepth        The max depth to configure.
     * @param bool   $expectException Whether decode() should throw an exception.
     *
     * @return void
     */
    public function testMaxDepth(string $json, int $maxDepth, bool $expectException) : void
    {
        $decoder = new JsonDecoder();
        $decoder->setMaxDepth($maxDepth);

        if ($expectException) {
            $this->expectException(JsonException::class);
        }

        $decoder->decode($json);

        if (! $expectException) {
            $this->addToAssertionCount(1); // no assertion here
        }
    }

    /**
     * @return array
     */
    public function providerMaxDepth() : array
    {
        return [
            ['123', 0, false],
            ['123', 1, false],
            ['[]', 0, true],
            ['[]', 1, false],
            ['[]', 2, false],
            ['["a"]', 0, true],
            ['["a"]', 1, false],
            ['["a"]', 2, false],
            ['{"a":[]}', 0, true],
            ['{"a":[]}', 1, true],
            ['{"a":[]}', 2, false],
            ['{"a":[]}', 3, false],
            ['{}', 0, true],
            ['{}', 1, false],
            ['{}', 2, false],
            ['{"x":1}', 0, true],
            ['{"x":1}', 1, false],
            ['{"x":1}', 2, false],
            ['{"x":{}}', 0, true],
            ['{"x":{}}', 1, true],
            ['{"x":{}}', 2, false],
            ['{"x":{}}', 3, false],
            ['{"x":[]}', 0, true],
            ['{"x":[]}', 1, true],
            ['{"x":[]}', 2, false],
            ['{"x":[]}', 3, false],
            ['{"x":[{}]}', 0, true],
            ['{"x":[{}]}', 1, true],
            ['{"x":[{}]}', 2, true],
            ['{"x":[{}]}', 3, false],
            ['{"x":[{}]}', 4, false],
        ];
    }
}
