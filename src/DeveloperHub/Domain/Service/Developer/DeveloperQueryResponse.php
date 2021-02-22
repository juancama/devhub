<?php
declare(strict_types=1);

namespace Jcv\DeveloperHub\Domain\Service\Developer;

use Jcv\Shared\Bus\Query\QueryResponse;

final class DeveloperQueryResponse implements QueryResponse
{
    private string $userName;
    private array $followersUserNames;
    private int $followBacksCount;

    public function __construct(string $userName, array $followBacksUserNames, int $followBacksCount)
    {
        $this->userName = $userName;
        $this->followersUserNames = $followBacksUserNames;
        $this->followBacksCount = $followBacksCount;
    }

    public static function fromArray(array $data)
    {
        return new static(
            $data['userName'] ?? null,
            $data['followBacks']['userNames'] ?? [],
            $data['followBacks']['count'] ?? 0,
        );
    }

    public function payload(): array
    {
        return [
            'userName' => $this->userName,
            'followBacks' => [
                'count' => $this->followBacksCount,
                'userNames' => $this->followersUserNames,
            ],
        ];
    }
}
