<?php
declare(strict_types=1);

namespace Jcv\DeveloperHub\Domain\Developer;

final class UserName
{
    private string $userName;

    private function __construct(string $userName)
    {
        $this->guard($userName);

        $this->userName = $userName;
    }

    public static function fromString(string $userName): self
    {
        return new self($userName);
    }

    private function guard(string $userName)
    {
        // todo: add username domain restrictions here
    }

    public function userName(): string
    {
        return $this->userName;
    }
}
