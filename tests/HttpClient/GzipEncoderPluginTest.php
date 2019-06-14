<?php

declare(strict_types=1);

namespace Sentry\Tests\HttpClient;

use PHPUnit\Framework\TestCase;
use Sentry\HttpClient\GzipEncoderPlugin;
use Sentry\Tests\Transport\PromiseMock;
use Zend\Diactoros\Request;

final class GzipEncoderPluginTest extends TestCase
{
    public function testGzipEncoding(): void
    {
        if (!\extension_loaded('zlib')) {
            $this->markTestSkipped('zlib extension must be loaded to test the gzip encoding.');
        }

        $input = '{"toBeEncoded":true}';

        $request = new Request(null, null, fopen('data://text/plain,' . $input, 'r'));

        $encodingPlugin = new GzipEncoderPlugin();

        $encodedRequest = $encodingPlugin->handleRequest($request, static function ($request) {
            return new PromiseMock($request);
        }, static function () {
        })->wait();

        // base64encoded version of the gzipped input
        $gzippedBody = 'H4sIAAAAAAAAE6tWKsl3SnXNS85PSU1RsiopKk2tBQAmY4BsFAAAAA==';

        $this->assertSame($gzippedBody, base64_encode($encodedRequest->getBody()->getContents()));
    }
}
