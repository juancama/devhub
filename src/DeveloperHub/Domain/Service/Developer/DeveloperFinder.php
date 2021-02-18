<?php
declare(strict_types=1);

namespace Jcv\DeveloperHub\Domain\Service\Developer;

use Jcv\DeveloperHub\Domain\Developer\UserName;

interface DeveloperFinder
{
    public function findByUserName(UserName $userName): ?DeveloperQueryResponse;
}
