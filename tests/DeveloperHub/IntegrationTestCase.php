<?php
declare(strict_types=1);

namespace Jcv\Tests\DeveloperHub;

use Jcv\Shared\Bus\Query\QueryBus;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class IntegrationTestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel(['environment' => 'test']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    protected function getService($id)
    {
        return self::$container->get($id);
    }

    protected function getQueryBus(): QueryBus
    {
        return $this->getService(QueryBus::class);
    }

}
