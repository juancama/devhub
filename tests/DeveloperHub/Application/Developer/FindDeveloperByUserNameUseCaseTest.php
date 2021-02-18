<?php
declare(strict_types=1);

namespace Colvin\Test\DeveloperHub\Application\Developer;

use Jcv\DeveloperHub\Application\Developer\FindDeveloperByUserNameQuery;
use Jcv\DeveloperHub\Application\Developer\FindDeveloperByUserNameUseCase;
use Jcv\DeveloperHub\Domain\Developer\UserName;
use Jcv\DeveloperHub\Domain\Service\Developer\DeveloperFinder;
use Jcv\DeveloperHub\Domain\Service\Developer\DeveloperQueryResponse;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class FindDeveloperByUserNameUseCaseTest extends TestCase
{
    use ProphecyTrait;

    private $developerFinder;
    private ?string $searchUserName = null;
    private ?DeveloperQueryResponse $expectedResponse = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->developerFinder = $this->prophesize(DeveloperFinder::class);

        $this->searchUserName = 'colvin';
    }

    /** @test */
    public function should_return_developer_response_when_developer_is_found_by_user_name()
    {
        $this->developerWillBeFoundByUserName();

        $response = $this->executeUseCase();

        $this->assertEquals($this->expectedResponse->payload(), $response->payload());
    }

    /** @test */
    public function should_return_null_when_developer_is_not_found_by_user_name()
    {
        $this->developerWillBeNotFoundByUserName();

        $response = $this->executeUseCase();

        $this->assertNull($response);
    }

    //todo: tests wrong scenarios

    private function developerWillBeFoundByUserName(): void
    {
        $this->expectedResponse = DeveloperQueryResponse::fromArray([
            'userName' => $this->searchUserName,
            'followersUserNames' => [
                'jhon',
                'mike',
                'ane',
            ],
        ]);

        $this->developerFinder
            ->findByUserName(UserName::fromString($this->searchUserName))
            ->willReturn($this->expectedResponse);
    }

    private function developerWillBeNotFoundByUserName()
    {
        $this->developerFinder
            ->findByUserName(UserName::fromString($this->searchUserName))
            ->willReturn($this->expectedResponse);
    }

    private function executeUseCase()
    {
        $useCase = new FindDeveloperByUserNameUseCase($this->developerFinder->reveal());

        return $useCase->__invoke(
            new FindDeveloperByUserNameQuery($this->searchUserName)
        );
    }
}
