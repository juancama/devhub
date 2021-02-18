<?php
declare(strict_types=1);

namespace Colvin\DeveloperHub\Application\Developer;

use Colvin\DeveloperHub\Domain\Developer\UserName;
use Colvin\DeveloperHub\Domain\Service\Developer\DeveloperFinder;
use Colvin\Share\Bus\Query\QueryResponse;

class FindDeveloperByUserNameUseCase
{
    private DeveloperFinder $developerFinder;

    public function __construct(DeveloperFinder $developerFinder)
    {
        $this->developerFinder = $developerFinder;
    }

    public function __invoke(FindDeveloperByUserNameQuery $query): ?QueryResponse
    {
        return $this->developerFinder->findByUserName(
            UserName::fromString($query->userName())
        );
    }
}
