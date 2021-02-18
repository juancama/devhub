<?php
declare(strict_types=1);

namespace Colvin\Tests\DeveloperHub\Infrastructure\Service\Developer;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

trait MockHttpClient
{
    protected static array $history = [];
    protected array $queueResponses = [];

    private function httpClientTeardown()
    {
        static::$history = [];
    }

    protected function countRequestCalls()
    {
        return count(static::$history);
    }

    protected function getLastRequest(): Request
    {
        $transaction = end(static::$history);

        return $transaction['request'];
    }

    protected function getFirstRequest(): Request
    {
        $transaction = reset(static::$history);

        return $transaction['request'];
    }

    protected function getRequest(int $index): Request
    {
        return static::$history[$index];
    }

    protected function makeJsonResponse(array $payload = [], int $code = 200, array $headers = []): Response
    {
        return new Response($code, $headers, json_encode($payload));
    }

    protected function mockClient(callable $fn)
    {
        $stack = HandlerStack::create(new MockHandler($this->queueResponses));

        $client = $fn($stack);

        $stack->push(Middleware::history(static::$history));

        return $client;
    }
}
