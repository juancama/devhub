<?php
declare(strict_types=1);

namespace Colvin\DeveloperHub\Infrastructure\Domain\Service\Developer;

use Colvin\DeveloperHub\Domain\Developer\UserName;
use Colvin\DeveloperHub\Domain\Service\Developer\DeveloperFinder;
use Colvin\DeveloperHub\Domain\Service\Developer\DeveloperQueryResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

class GitHubDeveloperFinder implements DeveloperFinder
{
    private Client $client;

    public function __construct(string $user, string $pass, ?HandlerStack $stack = null)
    {
        $stack = $stack ?? HandlerStack::create();

        $stack->push(Middleware::mapRequest(fn(RequestInterface $request) => $request->withHeader(
            'Authorization',
            'Basic ' . base64_encode("{$user}:{$pass}")
        )));

        $this->client = new Client([
            'stack' => $stack,
            'base_uri' => 'https://api.github.com',
        ]);
    }

    public function findByUserName(UserName $userName): ?DeveloperQueryResponse
    {
        $developerData = $this->getUser($userName);

        return $developerData ? DeveloperQueryResponse::fromArray($developerData) : null;
    }

    protected function getUser(UserName $userName): ?array
    {
        try {
            $response = $this->client->get('/search/users', [
                'query' => [
                    'q' => $userName->userName(),
                    'per_page' => 1,
                ],
            ]);

            $response = json_decode($response->getBody()->getContents(), true);

            $result = array_map(fn(array $user) => [
                'userName' => $user['login'],
                'followersUserNames' => array_map(
                    fn(array $follower) => $follower['login'],
                    json_decode(
                        $this->client->get($user['followers_url'])->getBody()->getContents(),
                        true
                    )
                ),
            ], $response['items'] ?? []);

            return $result ? array_shift($result) : null;
        } catch (GuzzleException | ServerException $e) {
            //Important: this approach mute all errors. All non "200" responses will be handled as "404" (not found).

            //todo: handle errors and throw exception.
            // handle "404" (not found) as well other server errors (500, 401, ...) and throw "Query Exception"
        }
    }
}
