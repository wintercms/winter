<?php

namespace System\Console;

use Illuminate\Foundation\Console\VendorPublishCommand;
use Illuminate\Foundation\Events\VendorTagPublished;

class VendorPublish extends VendorPublishCommand
{
    use Traits\HasPluginArgument;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'vendor:publish
        {plugin? : The plugin to publish assets to. <info>(eg: Winter.Blog)</info>}
        {--existing : Publish and overwrite only the files that have already been published}
        {--force : Overwrite any existing files}
        {--all : Publish assets for all service providers without prompt}
        {--provider= : The service provider that has assets you want to publish}
        {--tag=* : One or many tags that have assets you want to publish}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish any publishable assets from vendor packages to the specified plugin.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->validateProvidedPlugin();

        parent::handle();
    }

    /**
     * Publishes the assets for a tag.
     *
     * @param  string  $tag
     * @return mixed
     */
    protected function publishTag($tag)
    {
        /**
         * @TODO: Continue here:
         * - Temporarily override the internal paths for:
         * - config_path() -> plugin/config
         * - resource_path() -> plugin/resources (May want to do some magic rewriting of resources/views to plugin/views or just add support for the resources folder in a plugin)
         * - database_path() -> plugin/updates/$tag/ (May want to add support for integrating the migrations in order to the version.yaml file, same as create:migration)
         */
        $pathsToPublish = $this->pathsToPublish($tag);

        if ($publishing = count($pathsToPublish) > 0) {
            $this->components->info(sprintf(
                'Publishing %sassets',
                $tag ? "[$tag] " : '',
            ));
        }

        foreach ($pathsToPublish as $from => $to) {
            $this->publishItem($from, $to);
        }

        if ($publishing === false) {
            $this->components->info('No publishable resources for tag ['.$tag.'].');
        } else {
            $this->laravel['events']->dispatch(new VendorTagPublished($tag, $pathsToPublish));

            $this->newLine();
        }
    }
}
