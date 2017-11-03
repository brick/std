<?php

namespace Brick\Std\Tests\Json;

use Brick\Std\Json\JsonEncoder;

use PHPUnit\Framework\TestCase;

/**
 * Tests for class JsonEncoder.
 */
class JsonEncoderTest extends TestCase
{
    /**
     * @dataProvider providerEncode
     *
     * @param mixed  $value
     * @param string $expected
     *
     * @return void
     */
    public function testEncode($value, string $expected) : void
    {
        $encoder = new JsonEncoder();
        $this->assertSame($expected, $encoder->encode($value));
    }

    /**
     * @return array
     */
    public function providerEncode() : array
    {
        return [
            [123, '123'],
            ['ABC', '"ABC"'],
            [['a', 'b'], '["a","b"]']
        ];
    }

    /**
     * @dataProvider providerEncodeUnsupportedType
     * @expectedException \Brick\Std\Json\JsonException
     *
     * @param mixed $value
     *
     * @return void
     */
    public function testEncodeUnsupportedType($value) : void
    {
        $encoder = new JsonEncoder();
        $encoder->encode($value);
    }

    /**
     * @return array
     */
    public function providerEncodeUnsupportedType() : array
    {
        return [
            [fopen('php://memory', 'wb')]
        ];
    }

    /**
     * @dataProvider providerEscapeTags
     *
     * @param mixed  $value
     * @param bool   $escapeTags
     * @param string $expected
     *
     * @return void
     */
    public function testEscapeTags($value, bool $escapeTags, string $expected) : void
    {
        $encoder = new JsonEncoder();
        $encoder->escapeTags($escapeTags);

        $this->assertSame($expected, $encoder->encode($value));
    }

    /**
     * @return array
     */
    public function providerEscapeTags() : array
    {
        return [
            ['<test>', false, '"<test>"'],
            ['<test>', true, '"\u003Ctest\u003E"']
        ];
    }

    /**
     * @dataProvider providerEscapeAmpersands
     *
     * @param mixed  $value
     * @param bool   $escapeAmpersands
     * @param string $expected
     *
     * @return void
     */
    public function testEscapeAmpersands($value, bool $escapeAmpersands, string $expected) : void
    {
        $encoder = new JsonEncoder();
        $encoder->escapeAmpersands($escapeAmpersands);

        $this->assertSame($expected, $encoder->encode($value));
    }

    /**
     * @return array
     */
    public function providerEscapeAmpersands() : array
    {
        return [
            ['Cats & dogs', false, '"Cats & dogs"'],
            ['Cats & dogs', true, '"Cats \u0026 dogs"']
        ];
    }

    /**
     * @dataProvider providerEscapeApostrophes
     *
     * @param mixed  $value
     * @param bool   $escapeApostrophes
     * @param string $expected
     *
     * @return void
     */
    public function testEscapeApostrophes($value, bool $escapeApostrophes, string $expected) : void
    {
        $encoder = new JsonEncoder();
        $encoder->escapeApostrophes($escapeApostrophes);

        $this->assertSame($expected, $encoder->encode($value));
    }

    /**
     * @return array
     */
    public function providerEscapeApostrophes() : array
    {
        return [
            ['John\'s car', false, '"John\'s car"'],
            ['John\'s car', true, '"John\u0027s car"']
        ];
    }

    /**
     * @dataProvider providerEscapeQuotes
     *
     * @param mixed  $value
     * @param bool   $escapeQuotes
     * @param string $expected
     *
     * @return void
     */
    public function testEscapeQuotes($value, bool $escapeQuotes, string $expected) : void
    {
        $encoder = new JsonEncoder();
        $encoder->escapeQuotes($escapeQuotes);

        $this->assertSame($expected, $encoder->encode($value));
    }

    /**
     * @return array
     */
    public function providerEscapeQuotes() : array
    {
        return [
            ['She said "yes"', false, '"She said \"yes\""'],
            ['She said "yes"', true, '"She said \u0022yes\u0022"']
        ];
    }

    /**
     * @dataProvider providerForceObject
     *
     * @param mixed  $value
     * @param bool   $forceObject
     * @param string $expected
     *
     * @return void
     */
    public function testForceObject($value, bool $forceObject, string $expected) : void
    {
        $encoder = new JsonEncoder();
        $encoder->forceObject($forceObject);

        $this->assertSame($expected, $encoder->encode($value));
    }

    /**
     * @return array
     */
    public function providerForceObject() : array
    {
        return [
            [['a', 'b'], false, '["a","b"]'],
            [['a', 'b'], true, '{"0":"a","1":"b"}']
        ];
    }

    /**
     * @dataProvider providerEncodeNumericStringsAsNumbers
     *
     * @param mixed  $value
     * @param bool   $encodeNumeric
     * @param string $expected
     *
     * @return void
     */
    public function testEncodeNumericStringsAsNumbers($value, bool $encodeNumeric, string $expected) : void
    {
        $encoder = new JsonEncoder();
        $encoder->encodeNumericStringsAsNumbers($encodeNumeric);

        $this->assertSame($expected, $encoder->encode($value));
    }

    /**
     * @return array
     */
    public function providerEncodeNumericStringsAsNumbers() : array
    {
        return [
            [['ABC', '123'], false, '["ABC","123"]'],
            [['ABC', '123'], true, '["ABC",123]']
        ];
    }

    /**
     * @dataProvider providerPrettyPrint
     *
     * @param mixed  $value
     * @param bool   $prettyPrint
     * @param string $expected
     *
     * @return void
     */
    public function testPrettyPrint($value, bool $prettyPrint, string $expected) : void
    {
        $encoder = new JsonEncoder();
        $encoder->prettyPrint($prettyPrint);

        $this->assertSame($expected, $encoder->encode($value));
    }

    /**
     * @return array
     */
    public function providerPrettyPrint() : array
    {
        return [
            [['ABC', '123'], false, '["ABC","123"]'],
            [['ABC', '123'], true, "[\n    \"ABC\",\n    \"123\"\n]"]
        ];
    }

    /**
     * @dataProvider providerEscapeSlashes
     *
     * @param mixed  $value
     * @param bool   $escapeSlashes
     * @param string $expected
     *
     * @return void
     */
    public function testEscapeSlashes($value, bool $escapeSlashes, string $expected) : void
    {
        $encoder = new JsonEncoder();
        $encoder->escapeSlashes($escapeSlashes);

        $this->assertSame($expected, $encoder->encode($value));
    }

    /**
     * @return array
     */
    public function providerEscapeSlashes() : array
    {
        return [
            ['Hello/World', false, '"Hello/World"'],
            ['Hello/World', true, '"Hello\/World"']
        ];
    }

    /**
     * @dataProvider providerEscapeUnicode
     *
     * @param mixed  $value
     * @param bool   $escapeUnicode
     * @param string $expected
     *
     * @return void
     */
    public function testEscapeUnicode($value, bool $escapeUnicode, string $expected) : void
    {
        $encoder = new JsonEncoder();
        $encoder->escapeUnicode($escapeUnicode);

        $this->assertSame($expected, $encoder->encode($value));
    }

    /**
     * @return array
     */
    public function providerEscapeUnicode() : array
    {
        return [
            ['Là', false, '"Là"'],
            ['Là', true, '"L\u00e0"']
        ];
    }

    /**
     * @dataProvider providerPreserveZeroFraction
     *
     * @param mixed  $value
     * @param bool   $preserveZero
     * @param string $expected
     *
     * @return void
     */
    public function testPreserveZeroFraction($value, bool $preserveZero, string $expected) : void
    {
        $encoder = new JsonEncoder();
        $encoder->preserveZeroFraction($preserveZero);

        $this->assertSame($expected, $encoder->encode($value));
    }

    /**
     * @return array
     */
    public function providerPreserveZeroFraction() : array
    {
        return [
            [1.0, false, '1'],
            [1.0, true, '1.0']
        ];
    }

    /**
     * @expectedException \Brick\Std\Json\JsonException
     * @expectedExceptionMessage Maximum stack depth exceeded
     *
     * @return void
     */
    public function testMaxDepth() : void
    {
        $encoder = new JsonEncoder();
        $encoder->setMaxDepth(1);

        $encoder->encode(['a' => ['b' => 'c']]);
    }
}
