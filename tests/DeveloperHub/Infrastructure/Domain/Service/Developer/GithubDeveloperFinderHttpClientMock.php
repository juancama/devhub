<?php
declare(strict_types=1);

namespace Jcv\Tests\DeveloperHub\Infrastructure\Domain\Service\Developer;

use Jcv\DeveloperHub\Domain\Developer\UserName;
use Jcv\DeveloperHub\Infrastructure\Domain\Service\Developer\GitHubDeveloperFinder;
use Jcv\Tests\DeveloperHub\MockHttpClient;

trait GithubDeveloperFinderHttpClientMock
{
    use MockHttpClient;

    protected string $githubUser = 'myGitHubUser';
    protected string $gitHubToken = '1234567890';
    private ?array $followersResponsePayloadMock;
    private ?array $developerResponsePayloadMock;
    protected ?UserName $searchUserName;
    private ?GitHubDeveloperFinder $client;

    protected function setupGitHubDeveloperFinderMock()
    {
        $this->setupMockHttpClient();

        $this->searchUserName = UserName::fromString('colvin');

        $this->developerResponsePayloadMock = [
            'items' => [
                [
                    'login' => $this->searchUserName->userName(),
                    'followers_url' => "https://api.github.com/users/{$this->searchUserName->userName()}/followers",
                ],
            ],
        ];

        $this->followersResponsePayloadMock = [
            ['login' => 'jhon'],
            ['login' => 'mike'],
            ['login' => 'anne'],
        ];

        $this->client = new GitHubDeveloperFinder(
            $this->githubUser,
            $this->gitHubToken,
            $this->mockHttpClientStack
        );
    }

    protected function basicAuth(): string
    {
        return 'Basic ' . base64_encode("{$this->githubUser}:{$this->gitHubToken}");
    }

    protected function developerWithoutFollowersResponsePayload(): array
    {
        return [
            'userName' => $this->developerResponsePayloadMock['items'][0]['login'],
            'followBacks' =>
                [
                    'count' => 0,
                    'userNames' => [],
                ],
        ];
    }

    protected function developerWithFollowersResponsePayload()
    {
        return [
            'userName' => $this->developerResponsePayloadMock['items'][0]['login'],
            'followBacks' =>
                [
                    'count' => count($this->followersResponsePayloadMock),
                    'userNames' => array_map(fn($follower) => $follower['login'], $this->followersResponsePayloadMock),
                ],
        ];
    }

    protected function developerWillBeFound()
    {
        $this->httpClientMockQueueAppend(
            static::makeJsonResponse($this->developerResponsePayloadMock)
        );
    }

    protected function developerFollowersWillBeFound()
    {
        $this->httpClientMockQueueAppend(
            $this::makeJsonResponse($this->followersResponsePayloadMock)
        );
    }

    protected function developerFollowersWillBeEmpty()
    {
        $this->httpClientMockQueueAppend(
            static::makeJsonResponse()
        );
    }
}
