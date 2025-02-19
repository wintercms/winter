<?php namespace System\Console;

use System\Console\BaseScaffoldCommand;

class CreateJob extends BaseScaffoldCommand
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'create:job';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'create:job
        {plugin : The name of the plugin. <info>(eg: Winter.Blog)</info>}
        {name : The name of the job class to generate. <info>(eg: ImportPosts)</info>}
        {--b|batchable : Generates a batchable queue job.}
        {--s|sync : Generates a non-queueable job.}
        {--f|force : Overwrite existing files with generated files.}
        {--uninspiring : Disable inspirational quotes}
    ';

    /**
     * @var string The console command description.
     */
    protected $description = 'Creates a new job class.';

    /**
     * @var array List of commands that this command replaces (aliases)
     */
    protected $replaces = [
        'make:job',
    ];

    /**
     * @var string The type of class being generated.
     */
    protected $type = 'Job';

    /**
     * @var array A mapping of stubs to generated files.
     */
    protected $jobStubs = [
        'sync' => [
            'scaffold/job/job.stub' => 'jobs/{{studly_name}}.php',
        ],
        'batched' => [
            'scaffold/job/job.batched.stub' => 'jobs/{{studly_name}}.php',
        ],
        'queued' => [
            'scaffold/job/job.queued.stub' => 'jobs/{{studly_name}}.php',
        ],
    ];

    /**
     * @inheritDoc
     */
    public function prepareVars(): array
    {
        if ($this->option('sync')) {
            $this->stubs = $this->jobStubs['sync'];
        } elseif ($this->option('batchable')) {
            $this->stubs = $this->jobStubs['batched'];
        } else {
            $this->stubs = $this->jobStubs['queued'];
        }

        return parent::prepareVars();
    }
}
