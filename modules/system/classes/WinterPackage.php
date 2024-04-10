<?php

namespace System\Classes;

trait WinterPackage
{
    public function isWinterPlugin()
    {
        if (!$this->getType() === 'winter-plugin') {
            return false;
        }

        $extras = $this->getExtras();

        if (!isset($extras['winter']['name'])) {
            return false;
        }

        return true;
    }

    public function getPluginName()
    {
        return $this->getExtras()['winter']['name'];
    }

    public function getDisplayVersion()
    {
        $version = $this->getVersionNormalized();

        if (preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$/', $version)) {
            $parts = explode('.', $version);
            array_pop($parts);
            return implode('.', $parts);
        }

        return $version;
    }

    public function getAuthorList()
    {
        $authors = $this->getAuthors();

        $list = [];

        foreach ($authors as $author) {
            $list[] = $author['name'];
        }

        return implode(', ', $list);
    }
}
