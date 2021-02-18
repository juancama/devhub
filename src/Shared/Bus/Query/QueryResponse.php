<?php
declare(strict_types=1);

namespace Jcv\Shared\Bus\Query;

interface QueryResponse
{
    public function payload(): array;
}
