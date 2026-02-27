<?php

declare(strict_types=1);

namespace Brick\Std\Tests\Json;

use Brick\Std\Json\JsonEncoder;
use Brick\Std\Json\JsonException;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

use function fopen;

/**
 * Tests for class JsonEncoder.
 */
class JsonEncoderTest extends TestCase
{
    #[DataProvider('providerEncode')]
    public function testEncode(mixed $value, string $expected): void
    {
        $encoder = new JsonEncoder();
        self::assertSame($expected, $encoder->encode($value));
    }

    public static function providerEncode(): array
    {
        return [
            [123, '123'],
            ['ABC', '"ABC"'],
            [['a', 'b'], '["a","b"]'],
        ];
    }

    #[DataProvider('providerEncodeUnsupportedType')]
    public function testEncodeUnsupportedType(mixed $value): void
    {
        $encoder = new JsonEncoder();

        $this->expectException(JsonException::class);
        $encoder->encode($value);
    }

    public static function providerEncodeUnsupportedType(): array
    {
        return [
            [fopen('php://memory', 'wb')],
        ];
    }

    #[DataProvider('providerEscapeTags')]
    public function testEscapeTags(mixed $value, bool $escapeTags, string $expected): void
    {
        $encoder = new JsonEncoder();
        $encoder->escapeTags($escapeTags);

        self::assertSame($expected, $encoder->encode($value));
    }

    public static function providerEscapeTags(): array
    {
        return [
            ['<test>', false, '"<test>"'],
            ['<test>', true, '"\u003Ctest\u003E"'],
        ];
    }

    #[DataProvider('providerEscapeAmpersands')]
    public function testEscapeAmpersands(mixed $value, bool $escapeAmpersands, string $expected): void
    {
        $encoder = new JsonEncoder();
        $encoder->escapeAmpersands($escapeAmpersands);

        self::assertSame($expected, $encoder->encode($value));
    }

    public static function providerEscapeAmpersands(): array
    {
        return [
            ['Cats & dogs', false, '"Cats & dogs"'],
            ['Cats & dogs', true, '"Cats \u0026 dogs"'],
        ];
    }

    #[DataProvider('providerEscapeApostrophes')]
    public function testEscapeApostrophes(mixed $value, bool $escapeApostrophes, string $expected): void
    {
        $encoder = new JsonEncoder();
        $encoder->escapeApostrophes($escapeApostrophes);

        self::assertSame($expected, $encoder->encode($value));
    }

    public static function providerEscapeApostrophes(): array
    {
        return [
            ['John\'s car', false, '"John\'s car"'],
            ['John\'s car', true, '"John\u0027s car"'],
        ];
    }

    #[DataProvider('providerEscapeQuotes')]
    public function testEscapeQuotes(mixed $value, bool $escapeQuotes, string $expected): void
    {
        $encoder = new JsonEncoder();
        $encoder->escapeQuotes($escapeQuotes);

        self::assertSame($expected, $encoder->encode($value));
    }

    public static function providerEscapeQuotes(): array
    {
        return [
            ['She said "yes"', false, '"She said \"yes\""'],
            ['She said "yes"', true, '"She said \u0022yes\u0022"'],
        ];
    }

    #[DataProvider('providerForceObject')]
    public function testForceObject(mixed $value, bool $forceObject, string $expected): void
    {
        $encoder = new JsonEncoder();
        $encoder->forceObject($forceObject);

        self::assertSame($expected, $encoder->encode($value));
    }

    public static function providerForceObject(): array
    {
        return [
            [['a', 'b'], false, '["a","b"]'],
            [['a', 'b'], true, '{"0":"a","1":"b"}'],
        ];
    }

    #[DataProvider('providerEncodeNumericStringsAsNumbers')]
    public function testEncodeNumericStringsAsNumbers(mixed $value, bool $encodeNumeric, string $expected): void
    {
        $encoder = new JsonEncoder();
        $encoder->encodeNumericStringsAsNumbers($encodeNumeric);

        self::assertSame($expected, $encoder->encode($value));
    }

    public static function providerEncodeNumericStringsAsNumbers(): array
    {
        return [
            [['ABC', '123'], false, '["ABC","123"]'],
            [['ABC', '123'], true, '["ABC",123]'],
        ];
    }

    #[DataProvider('providerPrettyPrint')]
    public function testPrettyPrint(mixed $value, bool $prettyPrint, string $expected): void
    {
        $encoder = new JsonEncoder();
        $encoder->prettyPrint($prettyPrint);

        self::assertSame($expected, $encoder->encode($value));
    }

    public static function providerPrettyPrint(): array
    {
        return [
            [['ABC', '123'], false, '["ABC","123"]'],
            [['ABC', '123'], true, "[\n    \"ABC\",\n    \"123\"\n]"],
        ];
    }

    #[DataProvider('providerEscapeSlashes')]
    public function testEscapeSlashes(mixed $value, bool $escapeSlashes, string $expected): void
    {
        $encoder = new JsonEncoder();
        $encoder->escapeSlashes($escapeSlashes);

        self::assertSame($expected, $encoder->encode($value));
    }

    public static function providerEscapeSlashes(): array
    {
        return [
            ['Hello/World', false, '"Hello/World"'],
            ['Hello/World', true, '"Hello\/World"'],
        ];
    }

    #[DataProvider('providerEscapeUnicode')]
    public function testEscapeUnicode(mixed $value, bool $escapeUnicode, string $expected): void
    {
        $encoder = new JsonEncoder();
        $encoder->escapeUnicode($escapeUnicode);

        self::assertSame($expected, $encoder->encode($value));
    }

    public static function providerEscapeUnicode(): array
    {
        return [
            ['Là', false, '"Là"'],
            ['Là', true, '"L\u00e0"'],
        ];
    }

    #[DataProvider('providerEscapeLineTerminators')]
    public function testEscapeLineTerminators(mixed $value, bool $escapeLineTerminators, string $expected): void
    {
        $encoder = new JsonEncoder();
        $encoder->escapeUnicode(false);
        $encoder->escapeLineTerminators($escapeLineTerminators);

        self::assertSame($expected, $encoder->encode($value));
    }

    public static function providerEscapeLineTerminators(): array
    {
        return [
            ["\r\n", false, '"\r\n"'],
            ["\r\n", true, '"\r\n"'],
            ["\xe2\x80\xa8\xe2\x80\xa9", false, "\"\xe2\x80\xa8\xe2\x80\xa9\""],
            ["\xe2\x80\xa8\xe2\x80\xa9", true, '"\u2028\u2029"'],
        ];
    }

    #[DataProvider('providerPreserveZeroFraction')]
    public function testPreserveZeroFraction(mixed $value, bool $preserveZero, string $expected): void
    {
        $encoder = new JsonEncoder();
        $encoder->preserveZeroFraction($preserveZero);

        self::assertSame($expected, $encoder->encode($value));
    }

    public static function providerPreserveZeroFraction(): array
    {
        return [
            [1.0, false, '1'],
            [1.0, true, '1.0'],
        ];
    }

    #[DataProvider('providerInvalidMaxDepth')]
    public function testInvalidMaxDepth(int $maxDepth): void
    {
        $encoder = new JsonEncoder();

        $this->expectException(InvalidArgumentException::class);
        $encoder->setMaxDepth($maxDepth);
    }

    public static function providerInvalidMaxDepth(): array
    {
        return [
            [-1],
            [0x7fffffff],
        ];
    }

    /**
     * @param mixed $value           The value to encode.
     * @param int   $maxDepth        The max depth to configure.
     * @param bool  $expectException Whether encode() should throw an exception.
     */
    #[DataProvider('providerMaxDepth')]
    public function testMaxDepth(mixed $value, int $maxDepth, bool $expectException): void
    {
        $encoder = new JsonEncoder();
        $encoder->setMaxDepth($maxDepth);

        if ($expectException) {
            $this->expectException(JsonException::class);
        }

        $encoder->encode($value);

        if (! $expectException) {
            $this->addToAssertionCount(1); // no assertion here
        }
    }

    public static function providerMaxDepth(): array
    {
        $a = new stdClass(); // depth 1

        $b = new stdClass(); // depth 1
        $b->x = 1;

        $c = new stdClass(); // depth 2
        $c->x = $a;

        $d = new stdClass(); // depth 2
        $d->x = [];

        $e = new stdClass(); // depth 3
        $e->x = [$a];

        return [
            [123, 0, false],
            [123, 1, false],
            [[], 0, true],
            [[], 1, false],
            [[], 2, false],
            [['a'], 0, true],
            [['a'], 1, false],
            [['a'], 2, false],
            [['a' => []], 0, true],
            [['a' => []], 1, true],
            [['a' => []], 2, false],
            [['a' => []], 3, false],
            [$a, 0, true],
            [$a, 1, false],
            [$a, 2, false],
            [$b, 0, true],
            [$b, 1, false],
            [$b, 2, false],
            [$c, 0, true],
            [$c, 1, true],
            [$c, 2, false],
            [$c, 3, false],
            [$d, 0, true],
            [$d, 1, true],
            [$d, 2, false],
            [$d, 3, false],
            [$e, 0, true],
            [$e, 1, true],
            [$e, 2, true],
            [$e, 3, false],
            [$e, 4, false],
        ];
    }
}
