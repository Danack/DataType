<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Exception\InvalidRulesExceptionData;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\IsIpAddress;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class IsIpAddressTest extends BaseTestCase
{
    public function provideTestWorks()
    {
        // IPv4 addresses
        yield ['127.0.0.1'];
        yield ['192.168.1.1'];
        yield ['10.0.0.1'];
        yield ['255.255.255.255'];
        yield ['0.0.0.0'];
        
        // IPv6 addresses
        yield ['2001:0db8:85a3:0000:0000:8a2e:0370:7334'];
        yield ['2001:db8:85a3::8a2e:370:7334'];
        yield ['::1'];
        yield ['2001:db8::1'];
    }

    /**
     * @dataProvider provideTestWorks
     * @covers \DataType\ProcessRule\IsIpAddress
     */
    public function testWorks(string $testValue)
    {
        $rule = new IsIpAddress();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($testValue, $validationResult->getValue());
    }

    public function provideTestWorksIpv4Only()
    {
        yield ['127.0.0.1'];
        yield ['192.168.1.1'];
        yield ['10.0.0.1'];
    }

    /**
     * @dataProvider provideTestWorksIpv4Only
     * @covers \DataType\ProcessRule\IsIpAddress
     */
    public function testWorksIpv4Only(string $testValue)
    {
        $rule = new IsIpAddress(true, false);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($testValue, $validationResult->getValue());
    }

    public function provideTestWorksIpv6Only()
    {
        yield ['2001:0db8:85a3:0000:0000:8a2e:0370:7334'];
        yield ['2001:db8:85a3::8a2e:370:7334'];
        yield ['::1'];
    }

    /**
     * @dataProvider provideTestWorksIpv6Only
     * @covers \DataType\ProcessRule\IsIpAddress
     */
    public function testWorksIpv6Only(string $testValue)
    {
        $rule = new IsIpAddress(false, true);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($testValue, $validationResult->getValue());
    }

    /**
     * @covers \DataType\ProcessRule\IsIpAddress
     */
    public function testOnlyString()
    {
        $testValue = 15;

        $rule = new IsIpAddress();
        $processedValues = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);

        $this->expectException(InvalidRulesExceptionData::class);
        $this->expectExceptionMessageMatchesTemplateString(
            \DataType\Messages::BAD_TYPE_FOR_STRING_PROCESS_RULE
        );

        $rule->process(
            $testValue, $processedValues, $dataStorage
        );
    }

    public function provideTestErrors()
    {
        yield ['not an ip', Messages::ERROR_INVALID_IP_ADDRESS];
        yield ['256.1.1.1', Messages::ERROR_INVALID_IP_ADDRESS];
        yield ['1.1.1', Messages::ERROR_INVALID_IP_ADDRESS];
        yield ['1.1.1.1.1', Messages::ERROR_INVALID_IP_ADDRESS];
        yield ['', Messages::ERROR_INVALID_IP_ADDRESS];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \DataType\ProcessRule\IsIpAddress
     * @param string $testValue
     * @param string $expected_error
     */
    public function testErrors(string $testValue, string $expected_error)
    {
        $rule = new IsIpAddress();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);

        $validationResult = $rule->process(
            $testValue,
            $processedValues,
            $dataStorage
        );

        $this->assertTrue($validationResult->anyErrorsFound());

        $this->assertValidationProblemRegexp(
            '/foo',
            $expected_error,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ProcessRule\IsIpAddress
     */
    public function testIpv4RejectedWhenOnlyIpv6Allowed()
    {
        $rule = new IsIpAddress(false, true);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', '127.0.0.1');

        $validationResult = $rule->process(
            '127.0.0.1',
            $processedValues,
            $dataStorage
        );

        $this->assertTrue($validationResult->anyErrorsFound());
        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ERROR_INVALID_IP_ADDRESS,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ProcessRule\IsIpAddress
     */
    public function testIpv6RejectedWhenOnlyIpv4Allowed()
    {
        $rule = new IsIpAddress(true, false);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', '2001:db8::1');

        $validationResult = $rule->process(
            '2001:db8::1',
            $processedValues,
            $dataStorage
        );

        $this->assertTrue($validationResult->anyErrorsFound());
        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ERROR_INVALID_IP_ADDRESS,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ProcessRule\IsIpAddress
     */
    public function testDescriptionBothAllowed()
    {
        $rule = new IsIpAddress();
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame('ipv4', $description->getFormat());
    }

    /**
     * @covers \DataType\ProcessRule\IsIpAddress
     */
    public function testDescriptionIpv4Only()
    {
        $rule = new IsIpAddress(true, false);
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame('ipv4', $description->getFormat());
    }

    /**
     * @covers \DataType\ProcessRule\IsIpAddress
     */
    public function testDescriptionIpv6Only()
    {
        $rule = new IsIpAddress(false, true);
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame('ipv6', $description->getFormat());
    }

    /**
     * @covers \DataType\ProcessRule\IsIpAddress
     */
    public function testNeitherIpv4NorIpv6Allowed()
    {
        $rule = new IsIpAddress(false, false);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', '127.0.0.1');

        $validationResult = $rule->process(
            '127.0.0.1',
            $processedValues,
            $dataStorage
        );

        $this->assertTrue($validationResult->anyErrorsFound());
        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ERROR_INVALID_IP_ADDRESS,
            $validationResult->getValidationProblems()
        );
    }
}
