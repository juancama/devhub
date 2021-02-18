<?php
declare(strict_types=1);

namespace Jcv\Shared\Infrastructure\Symfony\DependencyInjection\Bus;

use Jcv\Shared\Bus\Query\QueryBus;
use Jcv\Shared\Infrastructure\Bus\TacticianQueryBus;
use League\Tactician\CommandBus as Tactician;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\InvokeInflector;
use Symfony\Component\DependencyInjection\ContainerInterface;

class QueryBusFactory
{
    public static function make(ContainerInterface $container): QueryBus
    {
        return new TacticianQueryBus(new Tactician([
            new CommandHandlerMiddleware(
                new ClassNameExtractor(),
                new ContainerHandlerLocator(
                    $container,
                    'Query',
                    'UseCase'
                ),
                new InvokeInflector()
            ),
        ]));
    }
}
