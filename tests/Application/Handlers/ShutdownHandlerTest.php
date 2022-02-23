<?php
declare(strict_types=1);

namespace Tests\Application\Handlers;

use App\Application\Actions\Action;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\ErrorReportingLevel;
use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\HttpErrorHandlerSettings;
use App\Application\Handlers\ShutdownHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Slim\Factory\AppFactory;
use Tests\TestCase;

class ShutdownHandlerTest extends TestCase
{
    /** @test **/
    public function report_error_when_all_error_levels_are_accepted()
    {
        $app = $this->getAppInstance();
        $container = $app->getContainer();
        $logger = $container->get(LoggerInterface::class);
        $responseFactory = $app->getResponseFactory();
        $callableResolver = $app->getCallableResolver();
        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
        $request = $this->createRequest('GET', '/test-action-response-code');
        $shutdownHandler = new ShutdownHandler(
            $request,
            $errorHandler,
            new HttpErrorHandlerSettings(
                new ErrorReportingLevel(ErrorReportingLevel::ALL),
                true
            )
        );
        register_shutdown_function($shutdownHandler);

        $testAction = new class ($logger) extends Action {
            public function __construct(
                LoggerInterface $loggerInterface
            ) {
                parent::__construct($loggerInterface);
            }

            public function action(): Response
            {
                throw new RuntimeException('This error must be always reported.');
            }
        };
        // Add Error Middleware
        $errorMiddleware = $app->addErrorMiddleware(
            true,
            true,
            true
        );
        $errorMiddleware->setDefaultErrorHandler($errorHandler);

        $app->get('/test-action-response-code', $testAction);

        $response = $app->handle($request);

        self::assertEquals(500, $response->getStatusCode());
    }
}
