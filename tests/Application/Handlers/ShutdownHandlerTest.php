<?php
declare(strict_types=1);

namespace Tests\Application\Handlers;

use App\Application\Actions\Action;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\ErrorReportingLevel;
use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\HttpErrorHandlerSettings;
use App\Application\Handlers\ShutdownHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response;
use Tests\Application\CanDefineErrorHandler;
use Tests\TestCase;

class ShutdownHandlerTest extends TestCase
{
    use CanDefineErrorHandler;

    public function testReportErrorWhenAllErrorLevelsAreAccepted()
    {
        $app = $this->getAppInstance();
        $request = $this->createRequest('GET', '/test-action-response-code');
        $this->defineShutDownHandlerInApplication(
            $request,
            $app,
            new HttpErrorHandlerSettings(
                new ErrorReportingLevel(ErrorReportingLevel::ALL),
                true
            )
        );

        $container = $app->getContainer();
        $logger = $container->get(LoggerInterface::class);
        $testAction = new class ($logger) extends Action {
            public function __construct(
                LoggerInterface $loggerInterface
            ) {
                parent::__construct($loggerInterface);
            }

            public function action(): ResponseInterface
            {
                trigger_error('This error must be reported.', E_USER_ERROR);
            }
        };
        $app->get('/test-action-response-code', $testAction);

        $response = $app->handle($request);

        self::assertEquals(500, $response->getStatusCode());
    }

    public function testReportWarningWhenAllErrorLevelsAreAccepted()
    {
        $app = $this->getAppInstance();
        $container = $app->getContainer();
        $request = $this->createRequest('GET', '/test-action-response-code');
        $logger = $container->get(LoggerInterface::class);
        $this->defineShutDownHandlerInApplication(
            $request,
            $app,
            new HttpErrorHandlerSettings(
                new ErrorReportingLevel(ErrorReportingLevel::ALL),
                true
            )
        );

        $testAction = new class ($logger) extends Action {
            public function __construct(
                LoggerInterface $loggerInterface
            ) {
                parent::__construct($loggerInterface);
            }

            public function action(): ResponseInterface
            {
                trigger_error('This warning must be reported.', E_USER_WARNING);

                return new Response;
            }
        };

        $app->get('/test-action-response-code', $testAction);

        $response = $app->handle($request);

        self::assertEquals(500, $response->getStatusCode());
    }


    public function testReportNoticeWhenAllErrorLevelsAreAccepted()
    {
        $app = $this->getAppInstance();
        $container = $app->getContainer();
        $request = $this->createRequest('GET', '/test-action-response-code');
        $logger = $container->get(LoggerInterface::class);
        $this->defineShutDownHandlerInApplication(
            $request,
            $app,
            new HttpErrorHandlerSettings(
                new ErrorReportingLevel(ErrorReportingLevel::ALL),
                true
            )
        );

        $testAction = new class ($logger) extends Action {
            public function __construct(
                LoggerInterface $loggerInterface
            ) {
                parent::__construct($loggerInterface);
            }

            public function action(): ResponseInterface
            {
                trigger_error('This notice must be reported.', E_USER_NOTICE);

                return new Response;
            }
        };

        $app->get('/test-action-response-code', $testAction);

        $response = $app->handle($request);

        self::assertEquals(500, $response->getStatusCode());
    }
}
