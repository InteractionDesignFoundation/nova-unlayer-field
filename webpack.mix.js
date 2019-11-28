let mix = require('laravel-mix')

mix
  .setPublicPath('dist')
  .js('resources/js/field.js', 'js')

if (mix.inProduction()) {
    mix.version();
}
