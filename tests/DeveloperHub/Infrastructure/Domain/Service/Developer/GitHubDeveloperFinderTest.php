<?php
declare(strict_types=1);

namespace Jcv\Tests\DeveloperHub\Infrastructure\Domain\Service\Developer;

use Jcv\DeveloperHub\Domain\Developer\UserName;
use Jcv\DeveloperHub\Infrastructure\Domain\Service\Developer\GitHubDeveloperFinder;
use Jcv\Shared\Bus\Query\QueryResponse;
use Jcv\Tests\DeveloperHub\MockHttpClient;
use PHPUnit\Framework\TestCase;

class GitHubDeveloperFinderTest extends TestCase
{
    use MockHttpClient;

    protected string $githubUser = 'myGitHubUser';
    protected string $gitHubToken = '1234567890';
    protected ?UserName $searchUserName;
    private ?array $followersResponsePayloadMock;
    private ?array $developerResponsePayloadMock;
    private ?GitHubDeveloperFinder $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupMockHttpClient();

        $this->client = new GitHubDeveloperFinder(
            $this->githubUser,
            $this->gitHubToken,
            $this->mockHttpClientStack
        );

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
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->teardownMockHttpClient();
    }

    /** @test */
    public function should_send_basic_auth_credentials()
    {
        $this->setHttpClientMockQueue([
            $this::okResponse(),
        ]);

        $this->searchDeveloper();

        $request = $this->getLastRequest();

        $expectedToken = 'Basic ' . base64_encode("{$this->githubUser}:{$this->gitHubToken}");

        $this->assertEquals([$expectedToken], $request->getHeader('Authorization'));
    }

    /** @test */
    public function should_get_one_developer_by_userName_without_followBacks()
    {
        $this->setHttpClientMockQueue([
            $this::makeJsonResponse($this->developerResponsePayloadMock),
            $this::okResponse(),
        ]);

        $user = $this->searchDeveloper();

        $expectedPayload = [
            'userName' => $this->developerResponsePayloadMock['items'][0]['login'],
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
        $this->setHttpClientMockQueue([
            $this::makeJsonResponse($this->developerResponsePayloadMock),
            $this::makeJsonResponse($this->followersResponsePayloadMock),
        ]);

        $user = $this->searchDeveloper();

        $expectedPayload = [
            'userName' => $this->developerResponsePayloadMock['items'][0]['login'],
            'followBacks' =>
                [
                    'count' => count($this->followersResponsePayloadMock),
                    'userNames' => array_map(fn($follower) => $follower['login'], $this->followersResponsePayloadMock),
                ],
        ];

        $this->assertEquals($expectedPayload, $user->payload());
    }

    //todo: tests wrong scenarios

    protected function searchDeveloper(): ?QueryResponse
    {
        return $this->client->findByUserName($this->searchUserName);
    }
}
