<?php declare(strict_types=1);

namespace InteractionDesignFoundation\NovaUnlayerField\Tests;

use InteractionDesignFoundation\NovaUnlayerField\ServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * {@inheritDoc}
     * @param \Illuminate\Foundation\Application $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }
}
