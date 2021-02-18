<?php
declare(strict_types=1);

namespace Jcv\DeveloperHub\Application\Developer;

use Jcv\DeveloperHub\Domain\Developer\UserName;
use Jcv\DeveloperHub\Domain\Service\Developer\DeveloperFinder;
use Jcv\Shared\Bus\Query\QueryResponse;

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
