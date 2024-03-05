# Plugins

Plugins use internal undocumented API and features of Unlayer.


## Events

- `content:added`
- `content:removed`
- `content:modified`
- `row:added`
- `row:removed`


## Example

```php
Unlayer::make('Content', 'design')->config(function (): array {
        /** @see https://docs.unlayer.com/docs/email-builder */
        return $this->generateUnlayerConfig();
    })->plugins([
        asset(mix('js/unlayer-editor-plugins/utm.js')->toHtml()),
        asset(mix('js/unlayer-editor-plugins/fontsize.js')->toHtml()),
    ]);
```
