<?php

return [

    /*
     * Laravel aliases
     */
    'App'          => Illuminate\Support\Facades\App::class,
    // 'Arr'       => Illuminate\Support\Arr::class,                  // Replaced by Winter
    'Artisan'      => Illuminate\Support\Facades\Artisan::class,
    // 'Auth'      => Illuminate\Support\Facades\Auth::class,         // Only BackendAuth provided by default
    // 'Blade'     => Illuminate\Support\Facades\Blade::class,        // Not encouraged
    'Broadcast'    => Illuminate\Support\Facades\Broadcast::class,
    'Bus'          => Illuminate\Support\Facades\Bus::class,
    'Cache'        => Illuminate\Support\Facades\Cache::class,
    // 'Config'    => Illuminate\Support\Facades\Config::class,       // Replaced by Winter
    'Cookie'       => Illuminate\Support\Facades\Cookie::class,
    'Crypt'        => Illuminate\Support\Facades\Crypt::class,
    'Date'         => Illuminate\Support\Facades\Date::class,
    'DB'           => Illuminate\Support\Facades\DB::class,
    'Eloquent'     => Illuminate\Database\Eloquent\Model::class,
    'Event'        => Illuminate\Support\Facades\Event::class,
    // 'File'      => Illuminate\Support\Facades\File::class,         // Replaced by Winter
    // 'Gate'      => Illuminate\Support\Facades\Gate::class,         // Currently unsupported in Winter
    'Hash'         => Illuminate\Support\Facades\Hash::class,
    'Http'         => Illuminate\Support\Facades\Http::class,
    // 'Js'        => Illuminate\Support\Js::class,                   // Currently unsupported in Winter
    'Lang'         => Illuminate\Support\Facades\Lang::class,
    'Log'          => Illuminate\Support\Facades\Log::class,
    // 'Mail'      => Illuminate\Support\Facades\Mail::class,         // Replaced by Winter
    'Notification' => Illuminate\Support\Facades\Notification::class,
    // 'Password'  => Illuminate\Support\Facades\Password::class,     // Currently unsupported in Winter
    'Queue'        => Illuminate\Support\Facades\Queue::class,
    'RateLimiter'  => Illuminate\Support\Facades\RateLimiter::class,
    'Redirect'     => Illuminate\Support\Facades\Redirect::class,
    // 'Redis'     => Illuminate\Support\Facades\Redis::class,        // Conflicts with default redis driver, see https://github.com/laravel/laravel/commit/612d16600419265566d01a19c852ddb13b5e9f4b
    'Request'      => Illuminate\Support\Facades\Request::class,
    'Response'     => Illuminate\Support\Facades\Response::class,
    'Route'        => Illuminate\Support\Facades\Route::class,
    // 'Schema'    => Illuminate\Support\Facades\Schema::class,       // Replaced by Winter
    'Session'      => Illuminate\Support\Facades\Session::class,
    'Storage'      => Illuminate\Support\Facades\Storage::class,
    // 'Str'       => Illuminate\Support\Str::class,                  // Replaced by Winter
    'URL'          => Illuminate\Support\Facades\URL::class,
    // 'Validator' => Illuminate\Support\Facades\Validator::class,    // Replaced by Winter
    'View'         => Illuminate\Support\Facades\View::class,

    /*
     * Winter aliases
     */
    'AjaxException'        => Winter\Storm\Exception\AjaxException::class,
    'ApplicationException' => Winter\Storm\Exception\ApplicationException::class,
    'Arr'                  => Winter\Storm\Support\Arr::class,
    'BackendAuth'          => Backend\Facades\BackendAuth::class,
    'Backend'              => Backend\Facades\Backend::class,
    'BackendMenu'          => Backend\Facades\BackendMenu::class,
    'Block'                => Winter\Storm\Support\Facades\Block::class,
    'Cms'                  => Cms\Facades\Cms::class,
    'Config'               => Winter\Storm\Support\Facades\Config::class,
    'DbDongle'             => Winter\Storm\Support\Facades\DbDongle::class,
    'File'                 => Winter\Storm\Support\Facades\File::class,
    'Flash'                => Winter\Storm\Support\Facades\Flash::class,
    'Form'                 => Winter\Storm\Support\Facades\Form::class,
    'Html'                 => Winter\Storm\Support\Facades\Html::class,
    'Http'                 => Winter\Storm\Support\Facades\Http::class,
    'Ini'                  => Winter\Storm\Support\Facades\Ini::class,
    'Input'                => Winter\Storm\Support\Facades\Input::class,
    'Mail'                 => Winter\Storm\Support\Facades\Mail::class,
    'Markdown'             => Winter\Storm\Support\Facades\Markdown::class,
    'Model'                => Winter\Storm\Database\Model::class,
    'Schema'               => Winter\Storm\Support\Facades\Schema::class,
    'Seeder'               => Winter\Storm\Database\Updates\Seeder::class,
    'Str'                  => Winter\Storm\Support\Str::class,
    'SystemException'      => Winter\Storm\Exception\SystemException::class,
    'Twig'                 => Winter\Storm\Support\Facades\Twig::class,
    'ValidationException'  => Winter\Storm\Exception\ValidationException::class,
    'Validator'            => Winter\Storm\Support\Facades\Validator::class,
    'Yaml'                 => Winter\Storm\Support\Facades\Yaml::class,

    /*
     * Backwards compatibility aliases
     */
    'Db'             => Illuminate\Support\Facades\DB::class,
    'Url'            => Illuminate\Support\Facades\URL::class,
    'TestCase'       => System\Tests\Bootstrap\TestCase::class,
    'PluginTestCase' => System\Tests\Bootstrap\PluginTestCase::class,

    // Input facade was removed in Laravel 6 - we are keeping it in the Storm library for backwards compatibility.
    'Illuminate\Support\Facades\Input' => Winter\Storm\Support\Facades\Input::class,

    // Illuminate's HtmlDumper was "dumped" in Laravel 6 - we'll route this to Symfony's HtmlDumper as Laravel have done.
    'Illuminate\Support\Debug\HtmlDumper' => Symfony\Component\VarDumper\Dumper\HtmlDumper::class,

    // Scaffolds were moved from the Storm library into their corresponding modules.
    'Winter\Storm\Scaffold\Console\CreateCommand'        => System\Console\CreateCommand::class,
    'Winter\Storm\Scaffold\Console\CreateModel'          => System\Console\CreateModel::class,
    'Winter\Storm\Scaffold\Console\CreatePlugin'         => System\Console\CreatePlugin::class,
    'Winter\Storm\Scaffold\Console\CreateSettings'       => System\Console\CreateSettings::class,
    'Winter\Storm\Scaffold\Console\CreateController'     => Backend\Console\CreateController::class,
    'Winter\Storm\Scaffold\Console\CreateFormWidget'     => Backend\Console\CreateFormWidget::class,
    'Winter\Storm\Scaffold\Console\CreateReportWidget'   => Backend\Console\CreateReportWidget::class,
    'Winter\Storm\Scaffold\Console\CreateTheme'          => Cms\Console\CreateTheme::class,
    'Winter\Storm\Scaffold\Console\CreateComponent'      => Cms\Console\CreateComponent::class,

    'October\Rain\Scaffold\Console\CreateCommand'        => System\Console\CreateCommand::class,
    'October\Rain\Scaffold\Console\CreateModel'          => System\Console\CreateModel::class,
    'October\Rain\Scaffold\Console\CreatePlugin'         => System\Console\CreatePlugin::class,
    'October\Rain\Scaffold\Console\CreateSettings'       => System\Console\CreateSettings::class,
    'October\Rain\Scaffold\Console\CreateController'     => Backend\Console\CreateController::class,
    'October\Rain\Scaffold\Console\CreateFormWidget'     => Backend\Console\CreateFormWidget::class,
    'October\Rain\Scaffold\Console\CreateReportWidget'   => Backend\Console\CreateReportWidget::class,
    'October\Rain\Scaffold\Console\CreateTheme'          => Cms\Console\CreateTheme::class,
    'October\Rain\Scaffold\Console\CreateComponent'      => Cms\Console\CreateComponent::class,
];
