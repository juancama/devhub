<?php
declare(strict_types=1);

namespace Jcv\DeveloperHub\Application\Developer;

class FindDeveloperByUserNameQuery
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
