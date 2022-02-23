<?php
declare(strict_types=1);

namespace App\Application\Handlers;

use ReflectionClass;

class ErrorReportingLevel
{
    public const ALL = 'all';
    public const ERROR_AND_WARNING = 'error_and_warning';
    public const ONLY_ERRORS = 'only_errors';

    public function __construct(string $level)
    {
        $reflectionClass = new ReflectionClass($this);
        if (!in_array($level, $reflectionClass->getConstants(), true)) {
            throw new \InvalidArgumentException("The error level $level is not recognizable by the system.");
        }
    }
}