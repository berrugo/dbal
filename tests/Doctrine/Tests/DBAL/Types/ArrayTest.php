<?php

namespace Doctrine\Tests\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ArrayType;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Doctrine\Tests\DbalTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use function serialize;

class ArrayTest extends DbalTestCase
{
    /** @var AbstractPlatform|MockObject */
    private $platform;

    /** @var ArrayType */
    private $type;

    protected function setUp() : void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->type     = Type::getType('array');
    }

    public function testArrayConvertsToDatabaseValue()
    {
        self::assertIsString($this->type->convertToDatabaseValue([], $this->platform));
    }

    public function testArrayConvertsToPHPValue()
    {
        self::assertIsArray($this->type->convertToPHPValue(serialize([]), $this->platform));
    }

    public function testConversionFailure()
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage("Could not convert database value to 'array' as an error was triggered by the unserialization: 'unserialize(): Error at offset 0 of 7 bytes'");
        $this->type->convertToPHPValue('abcdefg', $this->platform);
    }

    public function testNullConversion()
    {
        self::assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    /**
     * @group DBAL-73
     */
    public function testFalseConversion()
    {
        self::assertFalse($this->type->convertToPHPValue(serialize(false), $this->platform));
    }
}
