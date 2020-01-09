<?php declare(strict_types=1);

namespace IDF\NovaUnlayerField;

use Illuminate\Support\ServiceProvider as BasicServiceProvider;
use Laravel\Nova\Nova;

class ServiceProvider extends BasicServiceProvider
{
    /** @inheritDoc */
    public function boot(): void
    {
        Nova::serving(function () {
            Nova::script('nova-unlayer-field', __DIR__.'/../dist/js/field.js');
        });

        $this->registerTranslations();
    }

    protected function registerTranslations(): void
    {
        $currentLocale = app()->getLocale();

        Nova::translations(__DIR__."/../resources/lang/$currentLocale.json");
    }
}
