<?php

declare(strict_types = 1);

namespace TypeSpecTest\DataStorage;

use TypeSpec\DataStorage\ArrayDataStorage;
use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpecTest\BaseTestCase;
use TypeSpec\Exception\InvalidLocationException;
//use function TypeSpec\getJsonPointerParts;
use function JsonSafe\json_decode_safe;

/**
 * @covers \TypeSpec\DataStorage\ArrayDataStorage
 */
class ArrayDataStorageTest extends BaseTestCase
{
    public function testValueNotAvailable()
    {
        $dataStorage = ArrayDataStorage::fromArray([]);
        $dataStorageAtFoo = $dataStorage->moveKey('foo');

        $available = $dataStorageAtFoo->isValueAvailable();
        $this->assertFalse($available);
    }

    public function testMovingSeparatesPosition()
    {
        $dataStorage = ArrayDataStorage::fromArray([]);
        $dataStorageAtFoo = $dataStorage->moveKey('foo');
        $dataStorageAtFooBar = $dataStorage->moveKey('bar');

        $this->assertSame('/foo', $dataStorageAtFoo->getPath());
        $this->assertSame('/bar', $dataStorageAtFooBar->getPath());
    }

    public function testValueCorrect()
    {
        $dataStorage = ArrayDataStorage::fromArray(['foo' => 'bar']);
        $dataStorageAtFoo = $dataStorage->moveKey('foo');

        $available = $dataStorageAtFoo->isValueAvailable();
        $this->assertTrue($available);
        $this->assertSame('bar', $dataStorageAtFoo->getCurrentValue());
    }


    public function testInvalidLocation()
    {
        $dataStorage = ArrayDataStorage::fromArray(['foo' => 'bar']);
        $dataStorageAtFoo = $dataStorage->moveKey('foo');
        $this->assertTrue($dataStorageAtFoo->isValueAvailable());

        $dataStorageAtJohn = $dataStorage->moveKey('john');
        $this->assertFalse($dataStorageAtJohn->isValueAvailable());

        $this->expectException(InvalidLocationException::class);
        $dataStorageAtJohn->getCurrentValue();
    }

    public function testBadData()
    {
        $dataStorage = TestArrayDataStorage::createMissing('foo');
        $this->assertFalse($dataStorage->isValueAvailable());
    }



    public function providesPathsAreCorrect()
    {
        yield ['/3', [3]];
        yield ['/', []];
        yield ['/0', [0]];

        yield ['/0/foo', [0, 'foo']];
        yield ['/0/foo/2', [0, 'foo', 2]];
        yield ['/foo', ['foo']];
        yield ['/foo/2', ['foo', 2]];

        yield ['/foo/bar', ['foo', 'bar']];
        yield ['/foo/bar/3', ['foo', 'bar', 3]];
    }

    /**
     * @dataProvider providesPathsAreCorrect
     */
    public function testPathsAreCorrect($expected, $pathParts)
    {
        $dataStorage = ArrayDataStorage::fromArray([]);

        foreach ($pathParts as $pathPart) {
            $dataStorage = $dataStorage->moveKey($pathPart);
        }

        $this->assertSame($expected, $dataStorage->getPath());
    }
}
