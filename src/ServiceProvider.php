<?php declare(strict_types=1);

namespace InteractionDesignFoundation\NovaUnlayerField;

use Illuminate\Support\ServiceProvider as BasicServiceProvider;
use Laravel\Nova\Nova;

/**
 * @phpcs:disable SlevomatCodingStandard.Classes.RequireAbstractOrFinal
 * @noRector \Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector
 */
class ServiceProvider extends BasicServiceProvider
{
    /** Bootstrap any application services. */
    public function boot(): void
    {
        Nova::serving(static function () {
            Nova::script('nova-unlayer-field', __DIR__.'/../dist/js/field.js');
        });

        $this->publishes([
            __DIR__.'/../resources/lang/' => resource_path('lang/vendor/nova-unlayer-field'),
        ]);

        $this->registerTranslations();

        if ($this->app->runningInConsole()) {
            $this->registerResources();
        }
    }

    protected function registerTranslations(): void
    {
        Nova::translations(__DIR__.'/../lang/en.json');
        Nova::translations($this->app->resourcePath("lang/vendor/nova-unlayer-field/{$this->app->getLocale()}.json"));
    }

    private function registerResources(): void
    {
        $this->publishes([
            __DIR__.'/../config/unlayer.php' => $this->app->configPath('unlayer.php'),
        ], 'config');
    }
}
