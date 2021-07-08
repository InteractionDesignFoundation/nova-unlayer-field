<?php declare(strict_types=1);

namespace InteractionDesignFoundation\NovaUnlayerField;

use Laravel\Nova\Fields\Code;
use Laravel\Nova\Http\Requests\NovaRequest;

class Unlayer extends Code
{
    /**
     * The field's component.
     * @var string
     */
    public $component = 'nova-unlayer-field';

    /**
     * A function to call on filling Model attributes from Request
     * @var null|callable(\Laravel\Nova\Http\Requests\NovaRequest, string, \Illuminate\Database\Eloquent\Model, string):void $callback
     */
    public $savingCallback;

    /** @var string Height of the editor (with units) */
    public $height = '800px';

    /**
     * Specify Unlayer config
     * @see https://docs.unlayer.com/docs/getting-started#section-configuration-options
     * @param array|callable():array $config
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
     * @param null|callable(\Laravel\Nova\Http\Requests\NovaRequest, string, \Illuminate\Database\Eloquent\Model, string):void $callback
     */
    final public function savingCallback(?callable $callback): static
    {
        $this->savingCallback = $callback;

        return $this;
    }

    /**
     * Set generated HTML code that can be used on details page.
     * @param string|callable():string $html
     */
    final public function html(array | callable $html): static
    {
        $html = is_callable($html)
            ? $html()
            : $html;

        return $this->withMeta(['html' => $html]);
    }

    /**
     * Specify javascript modules to process Unlayer’s design on every design change.
     * @param string[] $plugins
     */
    final public function plugins(array $plugins): static
    {
        return $this->withMeta(['plugins' => $plugins]);
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
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if (is_callable($this->savingCallback)) {
            call_user_func($this->savingCallback, $request, $requestAttribute, $model, "{$requestAttribute}_html");
        }

        if ($request->exists($requestAttribute)) {
            $attributeValue = json_decode($request->get($requestAttribute), true);
            $model->setAttribute($attribute, $attributeValue);
        }
    }

    private function defaultUnlayerConfig(): array
    {
        return [
            'displayMode' => 'email',
            'locale' => app()->getLocale(),
        ];
    }
}
