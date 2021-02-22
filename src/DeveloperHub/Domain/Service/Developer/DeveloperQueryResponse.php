<?php
declare(strict_types=1);

namespace Jcv\DeveloperHub\Domain\Service\Developer;

use Jcv\Shared\Bus\Query\QueryResponse;

final class DeveloperQueryResponse implements QueryResponse
{
    private string $userName;
    private array $followersUserNames;
    private int $totalFollowers;

    public function __construct(string $userName, array $followersUserNames, int $totalFollowers)
    {
        $this->userName = $userName;
        $this->followersUserNames = $followersUserNames;
        $this->totalFollowers = $totalFollowers;
    }

    public static function fromArray(array $data)
    {
        return new static(
            $data['userName'] ?? null,
            $data['followersUserNames'] ?? [],
            $data['totalFollowers'] ?? 0,
        );
    }

    public function payload(): array
    {
        return [
            'userName' => $this->userName,
            'followBacks' => [
                'count' => $this->totalFollowers,
                'userNames' => $this->followersUserNames,
            ],
        ];
    }
}
