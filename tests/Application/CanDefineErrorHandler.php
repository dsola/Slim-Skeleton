<?php

declare(strict_types=1);

namespace Tests\Application;

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\HttpErrorHandlerSettings;
use App\Application\Handlers\ShutdownHandler;
use App\Application\Settings\SettingsInterface;
use Psr\Http\Message\RequestInterface;
use Slim\App;

trait CanDefineErrorHandler
{
    private function defineShutDownHandlerInApplication(
        RequestInterface $request,
        App $app,
        HttpErrorHandlerSettings $errorHandlerSettings
    ): void {
        $responseFactory = $app->getResponseFactory();
        $callableResolver = $app->getCallableResolver();
        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
        $this->registerShutDownFunction($request, $errorHandler, $errorHandlerSettings);
        $this->setErrorHandlerInApp($app, $errorHandler);
    }

    private function registerShutDownFunction(
        RequestInterface $request,
        HttpErrorHandler $errorHandler,
        HttpErrorHandlerSettings $errorHandlerSettings
    ): void {
        $shutdownHandler = new ShutdownHandler(
            $request,
            $errorHandler,
            $errorHandlerSettings
        );
        register_shutdown_function($shutdownHandler);
    }

    private function setErrorHandlerInApp(App $app, HttpErrorHandler $errorHandler): void
    {
        $settings = $app
            ->getContainer()
            ->get(SettingsInterface::class);
        $errorMiddleware = $app->addErrorMiddleware(
            $settings->get('displayErrorDetails'),
            $settings->get('logError'),
            $settings->get('displayErrorDetails')
        );
        $errorMiddleware->setDefaultErrorHandler($errorHandler);
    }
}
