<?php

return [

    /*
     * Laravel aliases
     */
    'App'       => Illuminate\Support\Facades\App::class,
    'Artisan'   => Illuminate\Support\Facades\Artisan::class,
    'Bus'       => Illuminate\Support\Facades\Bus::class,
    'Cache'     => Illuminate\Support\Facades\Cache::class,
    'Cookie'    => Illuminate\Support\Facades\Cookie::class,
    'Crypt'     => Illuminate\Support\Facades\Crypt::class,
    'Db'        => Illuminate\Support\Facades\DB::class, // Preferred
    'DB'        => Illuminate\Support\Facades\DB::class,
    'Eloquent'  => Illuminate\Database\Eloquent\Model::class,
    'Event'     => Illuminate\Support\Facades\Event::class,
    'Hash'      => Illuminate\Support\Facades\Hash::class,
    'Lang'      => Illuminate\Support\Facades\Lang::class,
    'Log'       => Illuminate\Support\Facades\Log::class,
    'Queue'     => Illuminate\Support\Facades\Queue::class,
    'Redirect'  => Illuminate\Support\Facades\Redirect::class,
    'Redis'     => Illuminate\Support\Facades\Redis::class,
    'Request'   => Illuminate\Support\Facades\Request::class,
    'Response'  => Illuminate\Support\Facades\Response::class,
    'Route'     => Illuminate\Support\Facades\Route::class,
    'Session'   => Illuminate\Support\Facades\Session::class,
    'Storage'   => Illuminate\Support\Facades\Storage::class,
    'Url'       => Illuminate\Support\Facades\URL::class, // Preferred
    'URL'       => Illuminate\Support\Facades\URL::class,
    'View'      => Illuminate\Support\Facades\View::class,

    /*
     * Winter aliases
     */
    'AjaxException'        => Winter\Storm\Exception\AjaxException::class,
    'ApplicationException' => Winter\Storm\Exception\ApplicationException::class,
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
    'Str'                  => Winter\Storm\Support\Facades\Str::class,
    'SystemException'      => Winter\Storm\Exception\SystemException::class,
    'Twig'                 => Winter\Storm\Support\Facades\Twig::class,
    'ValidationException'  => Winter\Storm\Exception\ValidationException::class,
    'Validator'            => Winter\Storm\Support\Facades\Validator::class,
    'Yaml'                 => Winter\Storm\Support\Facades\Yaml::class,

    /*
     * Fallback aliases
     */
    // Input facade was removed in Laravel 6 - we are keeping it in the Storm library for backwards compatibility.
    'Illuminate\Support\Facades\Input' => Winter\Storm\Support\Facades\Input::class,

    // Illuminate's HtmlDumper was "dumped" in Laravel 6 - we'll route this to Symfony's HtmlDumper as Laravel have done.
    'Illuminate\Support\Debug\HtmlDumper' => Symfony\Component\VarDumper\Dumper\HtmlDumper::class,
];
