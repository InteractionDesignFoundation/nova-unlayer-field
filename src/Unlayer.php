<?php declare(strict_types=1);

namespace InteractionDesignFoundation\NovaUnlayerField;

use Illuminate\Support\Facades\App;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\SupportsDependentFields;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.UselessAnnotation
 * @phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.UselessAnnotation
 * @phpcs:disable SlevomatCodingStandard.Classes.RequireAbstractOrFinal
 * @noRector \Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector
 * @api
 */
class Unlayer extends Field
{
    use SupportsDependentFields;

    public const string MODE_EMAIL = 'email';
    public const string MODE_WEB = 'web';

    /**
     * The field's component.
     * @var string
     */
    public $component = 'nova-unlayer-field';

    /**
     * Indicates if the element should be shown on the index view.
     * @var bool|(callable(\Laravel\Nova\Http\Requests\NovaRequest, array<array-key, mixed>|object): bool)
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    public $showOnIndex = false;

    /**
     * A function to call on filling Model attributes from Request
     * @var (callable(\Laravel\Nova\Http\Requests\NovaRequest, string, \Illuminate\Database\Eloquent\Model, string): void)|null $callback
     */
    public $savingCallback;

    /** @var string Height of the editor (with units) */
    public string $height = '800px';

    /**
     * Specify Unlayer config
     * @see https://docs.unlayer.com/docs/getting-started#section-configuration-options
     * @param array<string, mixed>|callable():array<string, mixed> $config
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    final public function config(array | callable $config): static
    {
        $customUnlayerConfig = is_callable($config)
            ? $config()
            : $config;

        return $this->withMeta([
            'config' => array_merge($this->defaultUnlayerConfig(), $customUnlayerConfig),
            'html' => '',
            'plugins' => [],
        ]);
    }

    /**
     * @param (callable(\Laravel\Nova\Http\Requests\NovaRequest, string, \Illuminate\Database\Eloquent\Model, string): void)|null $callback
     * @psalm-external-mutation-free
     */
    final public function savingCallback(?callable $callback): static
    {
        $this->savingCallback = $callback;

        return $this;
    }

    /**
     * Set generated HTML-code that can be used on details page.
     * @param string|callable():string $html
     */
    final public function html(string | callable $html): static
    {
        /** @var string $html */
        $html = is_callable($html)
            ? $html()
            : $html;

        return $this->withMeta(['html' => $html]);
    }

    /**
     * Specify javascript modules to process Unlayer’s design on every design change.
     * @param array<string> $plugins
     */
    final public function plugins(array $plugins): static
    {
        return $this->withMeta(['plugins' => $plugins]);
    }

    /**
     * Set the Code editor to display all of its contents.
     * @psalm-external-mutation-free
     */
    public function fullHeight(): static
    {
        $this->height = '100%';

        return $this;
    }

    /**
     * Set the visual height of the Code editor to automatic.
     * @psalm-external-mutation-free
     */
    public function autoHeight(): static
    {
        $this->height = 'auto';

        return $this;
    }

    /**
     * Set the visual height of the Unlayer editor (with units).
     * @psalm-external-mutation-free
     */
    public function height(string $height): static
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Prepare the field for JSON serialization.
     * @return array<string, mixed>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    #[\Override]
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'height' => $this->height,
        ]);
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     * @see \Laravel\Nova\Fields\Field::fillAttributeFromRequest
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param string $requestAttribute
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $attribute
     * @return void
     */
    #[\Override]
    protected function fillAttributeFromRequest(
        NovaRequest $request,
        string $requestAttribute,
        object $model,
        string $attribute
    ): void {
        if (is_callable($this->savingCallback)) {
            call_user_func($this->savingCallback, $request, $requestAttribute, $model, "{$requestAttribute}_html");
        }

        if ($request->exists($requestAttribute)) {
            $model->setAttribute($attribute, json_decode((string) $request->string($requestAttribute), true));
        }
    }

    /** @return array<string, string> */
    private function defaultUnlayerConfig(): array
    {
        return [
            'displayMode' => self::MODE_EMAIL,
            'locale' => App::getLocale(),
        ];
    }
}
