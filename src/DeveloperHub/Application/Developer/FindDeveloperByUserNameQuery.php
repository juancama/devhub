<?php
declare(strict_types=1);

namespace Jcv\DeveloperHub\Application\Developer;

use Jcv\Shared\Bus\Query\Query;

class FindDeveloperByUserNameQuery implements Query
{
    private string $userName;

    public function __construct(string $userName)
    {
        $this->userName = $userName;
    }

    public function userName(): string
    {
        return $this->userName;
    }
}
