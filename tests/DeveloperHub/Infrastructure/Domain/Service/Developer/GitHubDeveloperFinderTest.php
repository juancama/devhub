<?php
declare(strict_types=1);

namespace Jcv\Tests\DeveloperHub\Infrastructure\Domain\Service\Developer;

use Jcv\Shared\Bus\Query\QueryResponse;
use PHPUnit\Framework\TestCase;

class GitHubDeveloperFinderTest extends TestCase
{
    use GithubDeveloperFinderHttpClientMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupGitHubDeveloperFinderMock();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->teardownMockHttpClient();
    }

    /** @test */
    public function should_send_basic_auth_credentials()
    {
        $this->developerWillBeFound();

        $this->developerFollowersWillBeFound();

        $this->searchDeveloper();

        $sentAuth = $this->getLastRequest()->getHeaderLine('Authorization');

        $this->assertEquals($this->basicAuth(), $sentAuth);
    }

    //todo: test api call url and arguments

    /** @test */
    public function should_get_one_developer_by_userName_without_followBacks()
    {
        $this->developerWillBeFound();

        $this->developerFollowersWillBeEmpty();

        $developer = $this->searchDeveloper();

        $this->assertEquals(
            $this->developerWithoutFollowersResponsePayload(),
            $developer->payload()
        );
    }

    /** @test */
    public function should_get_one_developer_by_userName()
    {
        $this->developerWillBeFound();

        $this->developerFollowersWillBeFound();

        $developer = $this->searchDeveloper();

        $this->assertEquals(
            $this->developerWithFollowersResponsePayload(),
            $developer->payload()
        );
    }

    //todo: tests wrong scenarios

    protected function searchDeveloper(): ?QueryResponse
    {
        return $this->client->findByUserName($this->searchUserName);
    }
}
