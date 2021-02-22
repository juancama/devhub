<?php
declare(strict_types=1);

namespace Jcv\Tests\DeveloperHub\Infrastructure\Domain\Service\Developer;

use Jcv\DeveloperHub\Domain\Developer\UserName;
use Jcv\DeveloperHub\Infrastructure\Domain\Service\Developer\GitHubDeveloperFinder;
use Jcv\Shared\Bus\Query\QueryResponse;
use PHPUnit\Framework\TestCase;

class GitHubDeveloperFinderTest extends TestCase
{
    use GithubDeveloperFinderHttpClientMock;

    private ?GitHubDeveloperFinder $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupGitHubDeveloperFinderMock();

        $this->client = new GitHubDeveloperFinder(
            $this->githubUser,
            $this->gitHubToken,
            $this->mockHttpClientStack
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->teardownMockHttpClient();
    }

    /** @test */
    public function should_send_basic_auth_credentials_in_all_requests()
    {
        $this->developerWillBeFoundWhitFollowers();

        $this->searchDeveloper();

        $this->assertEquals($this->basicAuth(), $this->getRequest(0)->getHeaderLine('Authorization'));
        $this->assertEquals($this->basicAuth(), $this->getRequest(1)->getHeaderLine('Authorization'));
    }

    /** @test */
    public function should_find_developer_using_users_api_call()
    {
        $this->developerWillBeFoundWhitFollowers();

        $this->searchDeveloper();

        $request = $this->getFirstRequest();

        $this->assertEquals("/users/{$this->searchUserName}", $request->getUri()->getPath());
    }

    /** @test */
    public function should_get_followers_using_user_followers_api_call()
    {
        $this->developerWillBeFoundWhitFollowers();

        $this->searchDeveloper();

        $request = $this->getLastRequest();

        $this->assertEquals("/users/{$this->searchUserName}/followers", $request->getUri()->getPath());
    }

    //todo: tests wrong scenarios

    protected function searchDeveloper(): ?QueryResponse
    {
        return $this->client->findByUserName(
            UserName::fromString($this->searchUserName)
        );
    }
}
