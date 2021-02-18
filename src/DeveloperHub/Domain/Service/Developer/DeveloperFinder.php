<?php
declare(strict_types=1);

namespace Colvin\DeveloperHub\Domain\Service\Developer;

use Colvin\DeveloperHub\Domain\Developer\UserName;

interface DeveloperFinder
{
    public function findByUserName(UserName $userName): ?DeveloperQueryResponse;
}
