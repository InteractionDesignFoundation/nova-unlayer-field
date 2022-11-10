<?php declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Unlayer Project ID
    |--------------------------------------------------------------------------
    | This is the project ID from Unlayer. You can get it from the project settings page:
    | 1. Log in to the Unlayer dashboard and open https://dashboard.unlayer.com/projects
    | 2. Select or create a Project from, URL will be like https://dashboard.unlayer.com/projects/42/design/templates, where 42 is a project ID.
    */
    'project_id' => env('UNLAYER_PROJECT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Unlayer Project ID
    |--------------------------------------------------------------------------
    | Templates are pre-built designs that can be loaded into the editor
    | so your users don't have to start creating everything from scratch in a blank editor.
    | @see https://docs.unlayer.com/docs/templates
    |
    | To find your project ID:
    | 1. Open Unlayer dashboard and open https://dashboard.unlayer.com/projects
    | 2. Select a project and template, URL will be like https://dashboard.unlayer.com/projects/42/design/templates/43, where 42 is a project ID, 43 is a temple ID.
    */
    'template_id' => env('UNLAYER_DEFAULT_TEMPLATE_ID'),
];
