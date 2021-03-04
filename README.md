# Nova Unlayer Field

⚠️ This package is under active development

[![Latest Stable Version](https://poser.pugx.org/idf/nova-unlayer-field/v/stable)](https://packagist.org/packages/idf/nova-unlayer-field)
[![Total Downloads](https://poser.pugx.org/idf/nova-unlayer-field/downloads)](https://packagist.org/packages/idf/nova-unlayer-field)

Adds a Laravel Nova field for Unlayer to compose emails and landing pages.

![image](https://s5.gifyu.com/images/unlayer.gif)

## Installation

You can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require idf/nova-unlayer-field
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
            'templateId' => config('unlayer.newsletter_template_id'), // Used only if bound attribute is empty (e.g. $newsletter->design)
            'displayMode' => 'email', // "email"|"web". Default value: "email"
            'locale' => app()->getLocale(), // Locale for Unlayer UI
        ]),
     ];
}
```

### Options
 - `->config(array|callable $config)`: Specify [Unlayer config](https://docs.unlayer.com/docs/getting-started#section-configuration-options).
 - `->savingCallback(?callable $callback)`: Specify callback on saving a Model. Useful to store HTML result HTML code.
 - `->height(string $height)`: Set height of the editor (with units). E.g. '1000px' (800px by default).


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