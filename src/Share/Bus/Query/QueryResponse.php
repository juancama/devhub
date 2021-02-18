<?php
declare(strict_types=1);

namespace Colvin\Share\Bus\Query;

interface QueryResponse
{
    public function payload(): array;
}
