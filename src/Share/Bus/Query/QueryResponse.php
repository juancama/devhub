<?php
declare(strict_types=1);

namespace Jcv\Share\Bus\Query;

interface QueryResponse
{
    public function payload(): array;
}
