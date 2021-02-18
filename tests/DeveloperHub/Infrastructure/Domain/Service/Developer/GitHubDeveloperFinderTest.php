<?php
declare(strict_types=1);

namespace Jcv\Tests\DeveloperHub\Infrastructure\Domain\Service\Developer;

use Jcv\DeveloperHub\Domain\Developer\UserName;
use Jcv\DeveloperHub\Infrastructure\Domain\Service\Developer\GitHubDeveloperFinder;
use Jcv\Shared\Bus\Query\QueryResponse;
use PHPUnit\Framework\TestCase;

class GitHubDeveloperFinderTest extends TestCase
{
    use MockHttpClient;

    protected string $githubUser = 'myGitHubUser';
    protected string $gitHubToken = '1234567890';
    protected ?UserName $searchUserName;
    private ?array $followersResponsePayload;
    private ?array $developerResponsePayload;

    protected function setUp(): void
    {
        parent::setUp();

        $this->searchUserName = UserName::fromString('colvin');

        $this->developerResponsePayload = [
            'items' => [
                [
                    'login' => $this->searchUserName->userName(),
                    'followers_url' => "https://api.github.com/users/{$this->searchUserName->userName()}/followers",
                ],
            ],
        ];

        $this->followersResponsePayload = [
            ['login' => 'jhon'],
            ['login' => 'mike'],
            ['login' => 'anne'],
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->httpClientTeardown();
    }

    /** @test */
    public function should_send_basic_auth_credentials()
    {
        $this->queueResponses = [
            $this->makeJsonResponse(),
        ];

        $this->searchDeveloper();

        $request = $this->getLastRequest();

        $expectedToken = 'Basic ' . base64_encode("{$this->githubUser}:{$this->gitHubToken}");

        $this->assertEquals([$expectedToken], $request->getHeader('Authorization'));
    }

    /** @test */
    public function should_get_one_developer_by_userName_without_followBacks()
    {
        $this->queueResponses = [
            $this->makeJsonResponse($this->developerResponsePayload),
            $this->makeJsonResponse(),
        ];

        $user = $this->searchDeveloper();

        $expectedPayload = [
            'userName' => $this->developerResponsePayload['items'][0]['login'],
            'followBacks' =>
                [
                    'count' => 0,
                    'userNames' => [],
                ],
        ];

        $this->assertEquals($expectedPayload, $user->payload());
    }

    /** @test */
    public function should_get_one_developer_by_userName()
    {
        $this->queueResponses = [
            $this->makeJsonResponse($this->developerResponsePayload),
            $this->makeJsonResponse($this->followersResponsePayload),
        ];

        $user = $this->searchDeveloper();

        $expectedPayload = [
            'userName' => $this->developerResponsePayload['items'][0]['login'],
            'followBacks' =>
                [
                    'count' => count($this->followersResponsePayload),
                    'userNames' => array_map(fn($follower) => $follower['login'], $this->followersResponsePayload),
                ],
        ];

        $this->assertEquals($expectedPayload, $user->payload());
    }

    //todo: tests wrong scenarios

    protected function searchDeveloper(): ?QueryResponse
    {
        $client = $this->mockClient(fn($stack) => new GitHubDeveloperFinder(
            $this->githubUser,
            $this->gitHubToken,
            $stack
        ));

        return $client->findByUserName($this->searchUserName);
    }
}
