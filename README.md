# Nova Unlayer Field

[![Latest Stable Version](https://poser.pugx.org/interaction-design-foundation/nova-unlayer-field/v/stable)](https://packagist.org/packages/idf/nova-unlayer-field)
[![Total Downloads](https://poser.pugx.org/interaction-design-foundation/nova-unlayer-field/downloads)](https://packagist.org/packages/idf/nova-unlayer-field)

Adds a Laravel Nova field for Unlayer to compose emails and landing pages.

![image](https://github.com/InteractionDesignFoundation/nova-unlayer-field/blob/master/resources/img/demo-800x592@8.gif)



## Installation

You can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require interaction-design-foundation/nova-unlayer-field
```


## Usage

This package assumes that your Model has an attribute to store design config
(it's better to use `json` or `longtext` SQL type to store it).

On submit, the package sends two fields:
 - design (stringified json object)
 - html code. If you want to store HTML to your model, please use `savingCallback()`

```php
public function fields()
{
    return [ 
        Unlayer::make('Content', 'design')->config([
            'projectId' => config('unlayer.project_id'),

            // optional
            'templateId' => config('unlayer.newsletter_template_id'), // Used only if bound attribute ('design' in this case) is empty.
            'displayMode' => 'web', // "email" or "web". Default value: "email"
            'locale' => 'es', // Locale for Unlayer UI. Default value: application’s locale.
        ]),
     ];
}
```


### Options
 - `->config(array|callable $config)`: Specify [Unlayer config](https://docs.unlayer.com/docs/getting-started#section-configuration-options).
 - `->height(string $height)`: Set height of the editor (with units). E.g. '1000px' (800px by default).
 - `->savingCallback(?callable $callback)`: Specify a callback to call on before Model saving. Useful to store generated HTML code (to a Model or as a file).

Example of using `savingCallback`:
```php
Unlayer::make('Design')->config([
        'projectId' => config('unlayer.project_id'),
    ])
    ->savingCallback(function (NovaRequest $request, $attribute, Newsletter $newsletterModel, $htmlFieldName) {
        $newsletterModel->html = $request->input($htmlFieldName);
    }),
````


### Changelog

Please see [Releases](https://github.com/InteractionDesignFoundation/nova-unlayer-field/releases) for more information on what has changed recently.


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


### Compiling Assets

```bash
# Compile and minify your assets:
npm run prod

# Compile your assets for local development:
npm run dev

# Run the NPM "watch" command to auto-compile your assets when they are changed:
npm run watch
```


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
