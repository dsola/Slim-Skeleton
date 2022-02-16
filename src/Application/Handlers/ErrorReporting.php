<?php
declare(strict_types=1);

namespace App\Application\Handlers;

class ErrorReporting
{
    public const ALL = 'all';
    public const ERROR_AND_WARNING = 'error_and_warning';
    public const ONLY_ERRORS = 'only_errors';
}