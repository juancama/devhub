<?php
declare(strict_types=1);

namespace Colvin\DeveloperHub\Domain\Service\Developer;

use Colvin\Share\Bus\Query\QueryResponse;

final class DeveloperQueryResponse implements QueryResponse
{
    private string $userName;
    private array $followersUserNames;

    public function __construct(string $userName, array $followersUserNames)
    {
        $this->userName = $userName;
        $this->followersUserNames = $followersUserNames;
    }

    public static function fromArray(array $data)
    {
        return new static(
            $data['userName'] ?? null,
            $data['followersUserNames'] ?? [],
        );
    }

    public function payload(): array
    {
        return [
            'userName' => $this->userName,
            'followBacks' => [
                'count' => count($this->followersUserNames),
                'userNames' => $this->followersUserNames,
            ],
        ];
    }
}
