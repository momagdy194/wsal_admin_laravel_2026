<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use App\Base\Exceptions\CustomValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Jobs\Notifications\Exception\SendExceptionToEmailNotification;
use App\Models\Setting;
use Log;
use Illuminate\Support\Facades\Config;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
        \App\Base\Exceptions\CustomValidationException::class,
        \App\Base\Exceptions\UnknownUserTypeException::class,
        // \League\OAuth2\Server\Exception\OAuthServerException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {        
        $isDebugSendMailOpen = Config::get('app.debug_sendmail_open');
        $debugSendMailEmail = Config::get('app.debug_sendmail_email');

        if ($isDebugSendMailOpen && $debugSendMailEmail != '' && $exception instanceof Throwable && !in_array(get_class($exception), $this->dontReport)) {
            $debugSetting = Config::get('app.debug');
            $appName = Config::get('app.name');

            Config::set('app.debug', true);

            if (ExceptionHandler::isHttpException($exception)) {
                /** @var \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $httpException */
                $httpException = $exception;
                $content = ExceptionHandler::toIlluminateResponse(ExceptionHandler::renderHttpException($httpException), $exception);
            } else {
                $content = ExceptionHandler::toIlluminateResponse(ExceptionHandler::convertExceptionToResponse($exception), $exception);
            }

            Config::set('app.debug', $debugSetting);

            try {
                $request = request();

                $exceptionStack = (isset($content->original)) ? $content->original : $exception->getMessage();

                $emailTemplateModel['exceptionStack'] = $exceptionStack;
                $emailTemplateModel['request'] = $request;

                // $t2 = \Mail::send('email.errors.exception', $emailTemplateModel, function ($m) use ($debugSendMailEmail, $appName) {
                //     $m->to($debugSendMailEmail)->subject($appName . 'CRASH Report');
                // });

                // dispatch(new SendExceptionToEmailNotification($emailTemplateModel, $debugSendMailEmail));
            } catch (Throwable $e2) {
                dd($e2);
            }
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($this->expectsJson($request)) {
            return $this->getJsonResponse($exception);
        }


        return parent::render($request, $exception);
    }

    protected function getJsonResponse(Throwable $exception)
    {
        $exception = $this->prepareException($exception);

        $statusCode = $this->getStatusCode($exception);

        $exceptionMessage = $exception->getMessage();
        // Normalize OAuth-style auth errors to 401 with a clear message
        $authErrorMessages = ['invalid_grant', 'invalid_token', 'expired_token', 'revoked_token'];
        if (in_array(strtolower($exceptionMessage), $authErrorMessages, true) || $exception instanceof AuthenticationException) {
            $statusCode = Response::HTTP_UNAUTHORIZED;
            $message = 'Unauthenticated. Invalid or expired token.';
        } elseif ($exception instanceof NotFoundHttpException || !($message = $exceptionMessage)) {
            $message = sprintf('%d %s', $statusCode, Response::$statusTexts[$statusCode] ?? 'Error');
        } else {
            $message = $exceptionMessage;
        }

        $data = [
            'success' => false,
            'message' => $message,
            'status_code' => $statusCode,
        ];
        // Include the actual exception message for server errors (not for validation - errors are in 'errors')
        $isValidation = $exception instanceof ValidationException || $exception instanceof CustomValidationException;
        if (!$isValidation && $exceptionMessage !== '' && $exceptionMessage !== null) {
            $data['exception_message'] = $exceptionMessage;
        }

        if ($isValidation) {
            $data['status_code'] = $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data['errors'] = $exception instanceof ValidationException
                ? $exception->validator->errors()->getMessages()
                : $exception->getMessages();
        }

        if ($code = $exception->getCode()) {
            $data['code'] = $code;
        }

        // When debug is on, return the real error so you can see what went wrong
        if ($this->runningInDebugMode()) {
            $data['exception_message'] = $exception->getMessage();
            $data['debug'] = [
                'message' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'class' => get_class($exception),
                'trace' => explode("\n", $exception->getTraceAsString()),
            ];
            if ($exception instanceof QueryException) {
                $data['debug']['sql'] = $exception->getSql();
                $data['debug']['bindings'] = $exception->getBindings();
            }
        }

        return response()->json($data, $statusCode);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($this->expectsJson($request)) {
            return response()->json(['error' => 'Unauthenticated.'], Response::HTTP_UNAUTHORIZED);
        }
        $admin_url = Setting::where('name','admin_login')->pluck('value')->first();

        $admin_url = "login/".$admin_url;

        return redirect()->guest($admin_url);
    }

    /**
     * Get the exception status code
     *
     * @param Throwable $exception
     * @param int $defaultStatusCode
     * @return int
     */
    protected function getStatusCode(Throwable $exception, $defaultStatusCode = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        if ($this->isHttpException($exception)) {
            /** @var \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $exception */
            return $exception->getStatusCode();
        }

        return $exception instanceof AuthenticationException ?
        Response::HTTP_UNAUTHORIZED :
        $defaultStatusCode;
    }

    /**
     * Check if the application is running with debug enabled.
     *
     * @return bool
     */
    protected function runningInDebugMode()
    {
        return app_debug_enabled();
    }

    /**
     * Check if the current request expects a JSON response.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function expectsJson($request)
    {
        if ($request->expectsJson()) {
            return true;
        }
        // Treat these web routes as JSON when they send JSON (e.g. XHR from admin panel)
        $jsonSegments = ['api', 'roles', 'zones'];
        return !empty($request->segments()) && in_array($request->segments()[0], $jsonSegments, true);
    }
}
