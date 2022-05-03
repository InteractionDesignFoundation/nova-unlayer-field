let mix = require('laravel-mix')

require('./nova.mix')

mix
  .setPublicPath('dist')
  .js('resources/js/field.js', 'js')
  .vue({ version: 3 })
  .nova('interaction-design-foundation/nova-unlayer-field')

if (mix.inProduction()) {
    mix.version();
}
