<?php

declare(strict_types=1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Validates that a string is a valid IP address (IPv4 or IPv6).
 */
class IsIpAddress implements ProcessRule
{
    use CheckString;

    private bool $allowIpv4;
    private bool $allowIpv6;

    /**
     * @param bool $allowIpv4 Whether to allow IPv4 addresses (default: true)
     * @param bool $allowIpv6 Whether to allow IPv6 addresses (default: true)
     */
    public function __construct(bool $allowIpv4 = true, bool $allowIpv6 = true)
    {
        $this->allowIpv4 = $allowIpv4;
        $this->allowIpv6 = $allowIpv6;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        $value = $this->checkString($value);

        if (!$this->allowIpv4 && !$this->allowIpv6) {
            // Neither allowed - always fail
            return ValidationResult::errorResult(
                $inputStorage,
                Messages::ERROR_INVALID_IP_ADDRESS
            );
        }

        // If both are allowed, use no flags (default validates both)
        // If only one is allowed, use the appropriate flag
        $flags = 0;
        if ($this->allowIpv4 && !$this->allowIpv6) {
            $flags = FILTER_FLAG_IPV4;
        } elseif ($this->allowIpv6 && !$this->allowIpv4) {
            $flags = FILTER_FLAG_IPV6;
        }

        $valid = filter_var($value, FILTER_VALIDATE_IP, $flags);

        if ($valid === false) {
            return ValidationResult::errorResult(
                $inputStorage,
                Messages::ERROR_INVALID_IP_ADDRESS
            );
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        if ($this->allowIpv4 && $this->allowIpv6) {
            // Both allowed - use ipv4 format as default, or could use a pattern
            $paramDescription->setFormat('ipv4');
        } elseif ($this->allowIpv4) {
            $paramDescription->setFormat('ipv4');
        } elseif ($this->allowIpv6) {
            $paramDescription->setFormat('ipv6');
        }
    }
}
