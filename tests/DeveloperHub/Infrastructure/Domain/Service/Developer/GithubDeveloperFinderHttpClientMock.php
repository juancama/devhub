<?php
declare(strict_types=1);

namespace Jcv\Tests\DeveloperHub\Infrastructure\Domain\Service\Developer;

use Jcv\Tests\DeveloperHub\MockHttpClient;

trait GithubDeveloperFinderHttpClientMock
{
    use MockHttpClient;

    protected string $githubUser = 'myGitHubUser';
    protected string $gitHubToken = '1234567890';
    private ?array $followersResponsePayloadMock;
    private ?array $developerResponsePayloadMock;
    protected ?string $searchUserName;

    protected function setupGitHubDeveloperFinderMock()
    {
        $this->setupMockHttpClient();

        $this->searchUserName = 'githubteacher';

        $this->followersResponsePayloadMock = [
            ['login' => 'jhon'],
            ['login' => 'mike'],
            ['login' => 'anne'],
        ];

        $this->developerResponsePayloadMock = [
            'login' => $this->searchUserName,
            'followers' => count($this->followersResponsePayloadMock),
            'followers_url' => "https://api.github.com/users/{$this->searchUserName}/followers",
        ];
    }

    protected function basicAuth(): string
    {
        return 'Basic ' . base64_encode("{$this->githubUser}:{$this->gitHubToken}");
    }

    protected function developerWithoutFollowersResponsePayload(): array
    {
        return [
            'userName' => $this->developerResponsePayloadMock['login'],
            'followBacks' => [
                'count' => 0,
                'userNames' => [],
            ],
        ];
    }

    protected function developerWithFollowersResponsePayload()
    {
        return [
            'userName' => $this->developerResponsePayloadMock['login'],
            'followBacks' => [
                'count' => count($this->followersResponsePayloadMock),
                'userNames' => array_map(
                    fn($follower) => $follower['login'],
                    $this->followersResponsePayloadMock
                ),
            ],
        ];
    }

    protected function developerWillBeFoundWhitFollowers()
    {
        $this->setHttpClientMockQueue(
            static::makeJsonResponse($this->developerResponsePayloadMock),
            static::makeJsonResponse($this->followersResponsePayloadMock)
        );
    }

    protected function developerWillBeFoundWithoutFollowers()
    {
        $this->developerResponsePayloadMock['followers'] = 0;

        $this->setHttpClientMockQueue(
            static::makeJsonResponse($this->developerResponsePayloadMock),
            static::makeJsonResponse()
        );
    }
}
