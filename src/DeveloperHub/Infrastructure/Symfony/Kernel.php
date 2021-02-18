<?php
declare(strict_types=1);

namespace Jcv\DeveloperHub\Infrastructure\Symfony;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getProjectDir(): string
    {
        return \dirname(__DIR__, 4) . '/applications/developer-hub';
    }

    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import($this->getProjectDir() . '/config/{packages}/*.yaml');
        $container->import($this->getProjectDir() . '/config/{packages}/' . $this->environment . '/*.yaml');

        if (is_file($this->getProjectDir() . '/config/services.yaml')) {
            $container->import($this->getProjectDir() . '/config/services.yaml');
            $container->import($this->getProjectDir() . '/config/{services}_' . $this->environment . '.yaml');
        } elseif (is_file($path = $this->getProjectDir() . '/config/services.php')) {
            (require $path)($container->withPath($path), $this);
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import($this->getProjectDir() . '/config/{routes}/' . $this->environment . '/*.yaml');
        $routes->import($this->getProjectDir() . '/config/{routes}/*.yaml');

        if (is_file($this->getProjectDir() . '/config/routes.yaml')) {
            $routes->import($this->getProjectDir() . '/config/routes.yaml');
        } elseif (is_file($path = $this->getProjectDir() . '/config/routes.php')) {
            (require $path)($routes->withPath($path), $this);
        }
    }
}
