<?php
declare(strict_types=1);

namespace Jcv\DeveloperHub\Infrastructure\Domain\Service\Developer;

use Jcv\DeveloperHub\Domain\Developer\UserName;
use Jcv\DeveloperHub\Domain\Service\Developer\DeveloperFinder;
use Jcv\DeveloperHub\Domain\Service\Developer\DeveloperQueryResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

class GitHubDeveloperFinder implements DeveloperFinder
{
    private Client $client;

    public function __construct(string $user, string $token, ?HandlerStack $stack = null)
    {
        $stack = $stack ?? HandlerStack::create();

        $stack->unshift(Middleware::mapRequest(fn(RequestInterface $request) => $request->withHeader(
            'Authorization',
            'Basic ' . base64_encode("{$user}:{$token}")
        )));

        $this->client = new Client([
            'handler' => $stack,
            'base_uri' => 'https://api.github.com',
        ]);
    }

    public function findByUserName(UserName $userName): ?DeveloperQueryResponse
    {
        $developerData = $this->getUserWithFollowers($userName->userName());

        return $developerData ? DeveloperQueryResponse::fromArray($developerData) : null;
    }

    protected function getUserWithFollowers(string $userName, int $followersPage = 1): ?array
    {
        try {
            $user = $this->findUser($userName);

            $result = [
                'userName' => $user['login'],
                'followBacks' => [
                    'count' => $user['followers'],
                    'userNames' => array_map(
                        fn(array $follower) => $follower['login'],
                        $this->getFollowers($user['login'], $followersPage),
                    ),
                ],
            ];
        } catch (GuzzleException | ServerException $e) {
            //Important: this approach mute all errors. All non "200" responses will be handled as "404" (not found).

            //todo: handle errors and throw exception.
            // handle "404" (not found) as well other server errors (500, 401, ...) and throw "Query Exception"
        }

        return $result ?? null;
    }

    private function getFollowers(string $userName, int $followersPage): array
    {
        $response = $this->client->get("/users/{$userName}/followers", [
            'query' => [
                'page' => $followersPage,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    private function findUser(string $userName): array
    {
        $response = $this->client->get("https://api.github.com/users/{$userName}");

        return json_decode($response->getBody()->getContents(), true);
    }
}
