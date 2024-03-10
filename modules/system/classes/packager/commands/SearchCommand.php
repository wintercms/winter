<?php

namespace System\Classes\Packager\Commands;

use Cache;
use Winter\Packager\Commands\Search;
use Winter\Packager\Exceptions\CommandException;

class SearchCommand extends Search
{
    public function execute()
    {
        $output = $this->runComposerCommand();

        if ($output['code'] !== 0) {
            throw new CommandException(implode(PHP_EOL, $output['output']));
        }

        $this->results = json_decode(implode(PHP_EOL, $output['output']), true) ?? [];

        return $this;
    }
}
