<?php declare(strict_types=1);

namespace Idf\NovaUnlayerField;

use Illuminate\Support\ServiceProvider as BasicServiceProvider;
use Laravel\Nova\Nova;

final class ServiceProvider extends BasicServiceProvider
{
    /** @inheritDoc */
    public function boot(): void
    {
        Nova::serving(function () {
            Nova::script('nova-unlayer-field', __DIR__.'/../dist/js/field.js');
        });
    }
}
