<?php

declare(strict_types=1);

namespace Sentry\HttpClient;

use Http\Client\Common\Plugin;
use Http\Message\Encoding;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;

/**
 * Allow to encode request body with gzip encoding.
 *
 * If zlib is not installed, this plugin will do nothing.
 */
class GzipEncoderPlugin implements Plugin
{
    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        if (\extension_loaded('zlib')) {
            $request = $request
                ->withHeader('Content-Encoding', 'gzip')
                ->withBody(new Encoding\GzipEncodeStream($request->getBody()));
        }

        return $next($request);
    }
}
