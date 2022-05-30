<?php declare(strict_types=1);

namespace InteractionDesignFoundation\NovaUnlayerField\Tests;

use InteractionDesignFoundation\NovaUnlayerField\ServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }
}
