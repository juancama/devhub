<?php
declare(strict_types=1);

namespace Jcv\Tests\DeveloperHub;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

trait MockHttpClient
{
    protected static array $mockHttpClientHistory = [];
    protected array $mockHttpClientQueue = [];
    protected ?HandlerStack $mockHttpClientStack;
    private ?MockHandler $mockHttpClientHandler;

    public function setupMockHttpClient(?MockHandler $handler = null, ?HandlerStack $stack = null)
    {
        $this->mockHttpClientHandler = $handler ?? new MockHandler($this->mockHttpClientQueue);

        $this->mockHttpClientStack = $stack ?? HandlerStack::create($this->mockHttpClientHandler);

        $this->mockHttpClientStack->push(Middleware::history(static::$mockHttpClientHistory), 'mock.history');
    }

    protected function teardownMockHttpClient()
    {
        $this->mockHttpClientHandler->reset();
        static::$mockHttpClientHistory = [];
    }

    protected function countRequestCalls()
    {
        return count(static::$mockHttpClientHistory);
    }

    protected function setHttpClientMockQueue(array $value)
    {
        $this->mockHttpClientHandler->reset();
        $this->mockHttpClientHandler->append(...$value);
    }

    protected function httpClientMockQueueAppend(...$value)
    {
        $this->mockHttpClientHandler->append(...$value);
    }

    protected function getLastRequest(): RequestInterface
    {
        return $this->mockHttpClientHandler->getLastRequest();
    }

    protected static function makeJsonResponse(array $payload = [], int $code = 200, array $headers = []): Response
    {
        return new Response($code, $headers, json_encode($payload));
    }
}
