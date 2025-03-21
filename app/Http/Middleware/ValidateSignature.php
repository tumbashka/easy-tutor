<?php

namespace App\Http\Middleware;


use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Routing\Middleware\ValidateSignature as Middleware;
use Illuminate\Support\Arr;


class ValidateSignature extends Middleware
{
    public function handle($request, \Closure $next, ...$args)
    {
        if ($this->hasValidSignature($request)) {
            return $next($request);
        }

        throw new InvalidSignatureException;
    }

    protected function hasValidSignature(Request $request, $absolute = true): bool
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $path = $request->path();
        $url = $absolute ? "{$baseUrl}/{$path}" : $path;

        $query = Arr::query(Arr::except($request->query(), 'signature'));
        $original = $url . ($query ? '?' . $query : '');

        $signature = $request->query('signature');
        $key = app('config')['app.key'];

        return hash_equals(hash_hmac('sha256', $original, $key), $signature) &&
            (!$request->query('expires') || now()->timestamp <= $request->query('expires'));
    }
}
