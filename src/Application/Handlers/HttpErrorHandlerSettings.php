<?php
declare(strict_types=1);

namespace App\Application\Handlers;

class HttpErrorHandlerSettings
{
    private ErrorReportingLevel $errorReportingLevel;
    private bool $displayErrorDetails;

    public function __construct(ErrorReportingLevel $errorReportingLevel, bool $displayErrorDetails)
    {
        $this->errorReportingLevel = $errorReportingLevel;
        $this->displayErrorDetails = $displayErrorDetails;
    }

    public function isDisplayErrorDetails(): bool
    {
        return $this->displayErrorDetails;
    }

    public function getErrorReportingLevel(): ErrorReportingLevel
    {
        return $this->errorReportingLevel;
    }
}