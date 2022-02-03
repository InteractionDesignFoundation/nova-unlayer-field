let mix = require('laravel-mix')

mix
  .setPublicPath('dist')
  .js('resources/js/field.js', 'js')
  .vue();

if (mix.inProduction()) {
    mix.version();
}
