<?php
declare(strict_types=1);

namespace Jcv\Shared\Infrastructure\Bus;

use Jcv\Shared\Bus\Query\Query;
use Jcv\Shared\Bus\Query\QueryBus;
use Jcv\Shared\Bus\Query\QueryResponse;

use League\Tactician\CommandBus as TacticianBus;

class TacticianQueryBus implements QueryBus
{
    private TacticianBus $bus;

    public function __construct(TacticianBus $bus)
    {
        $this->bus = $bus;
    }

    public function ask(Query $query): ?QueryResponse
    {
        return $this->bus->handle($query);
    }
}
