<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Arr;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if (request()->is('cpanel/*')) {
            if($this->isHttpException($exception)){
                switch ($exception->getStatusCode()) {
                    case 401:
                        return view('admin.error.401');
                    case 404:
                        return view('admin.error.404');
                        break;
                }
            }
            return parent::render($request, $exception);
        }else{
            if($this->isHttpException($exception)){
                switch ($exception->getStatusCode()) {
                    case 400:
                        return redirect('/');
                    case 404:
                        return redirect('/404');
                        break;
                    case 405:
                        return redirect('/404');
                        break;
                }
            }
            return parent::render($request, $exception);
        }
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
        if ($request->is('api/*')){
            return response()->json(['status'=>'error','errors' => 'Unauthenticated','result'=>0], 401);
        }

        $guard = Arr::get($exception->guards(),0);
        switch ($guard) {
            case 'admin':
                    return redirect()->guest(route('admin.login'));
                break;

            default:
                    return redirect()->guest(route('login'));
                break;
        }
    }
}
