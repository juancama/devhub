<?php
declare(strict_types=1);

namespace Jcv\Shared\Bus\Query;

interface QueryBus
{
    public function ask(Query $query): ?QueryResponse;
}
