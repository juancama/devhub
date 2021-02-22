<?php
declare(strict_types=1);

namespace Jcv\Tests\DeveloperHub\Application\Developer;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Jcv\DeveloperHub\Application\Developer\FindDeveloperByUserNameQuery;
use Jcv\DeveloperHub\Application\Developer\FindDeveloperByUserNameUseCase;
use Jcv\DeveloperHub\Domain\Service\Developer\DeveloperQueryResponse;
use Jcv\Shared\Bus\Query\QueryResponse;
use Jcv\Tests\DeveloperHub\Infrastructure\Domain\Service\Developer\GithubDeveloperFinderHttpClientMock;
use Jcv\Tests\DeveloperHub\IntegrationTestCase;

/**
 * @group integration
 */
class FindDeveloperByUserNameUseCaseIntegrationTest extends IntegrationTestCase
{
    use GithubDeveloperFinderHttpClientMock;

    private ?FindDeveloperByUserNameUseCase $useCase = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupGitHubDeveloperFinderMock();

        $this->setupMockHttpClient(
            $this->getService(MockHandler::class),
            $this->getService(HandlerStack::class)
        );

        $this->useCase = $this->getService(FindDeveloperByUserNameUseCase::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->teardownMockHttpClient();
    }

    /** @test */
    public function should_find_developer_by_userName()
    {
        $this->developerWillBeFoundWhitFollowers();

        $developer = $this->searchDeveloperByUserName();

        $expectedResponse = DeveloperQueryResponse::fromArray(
            $this->developerWithFollowersResponsePayload()
        );

        $this->assertEquals($expectedResponse, $developer);
    }

    /** @test */
    public function should_find_developer_by_userName_without_followBacks()
    {
        $this->developerWillBeFoundWithoutFollowers();

        $developer = $this->searchDeveloperByUserName();

        $expectedResponse = DeveloperQueryResponse::fromArray(
            $this->developerWithFollowersResponsePayload()
        );

        $this->assertEquals($expectedResponse, $developer);
    }

    //todo: tests wrong scenarios

    private function searchDeveloperByUserName(): QueryResponse
    {
        return $this->useCase->__invoke(
            new FindDeveloperByUserNameQuery(
                $this->searchUserName
            )
        );
    }
}
