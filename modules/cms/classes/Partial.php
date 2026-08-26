<?php namespace Cms\Classes;

/**
 * The CMS partial class.
 *
 * @package winter\wn-cms-module
 * @author Alexey Bobkov, Samuel Georges
 */
class Partial extends CmsCompoundObject
{
    /**
     * @var string The container name associated with the model, eg: pages.
     */
    protected $dirName = 'partials';

    protected $allowedExtensions = ['htm', 'blade'];

    /**
     * Returns name of a PHP class to us a parent for the PHP class created for the object's PHP section.
     * @return string Returns the class name.
     */
    public function getCodeClassParent()
    {
        return PartialCode::class;
    }

    /**
     * Returns the base file name and extension. Applies a default extension, if none found.
     */
    public function getFileNameParts($fileName = null)
    {
        if ($fileName === null) {
            $fileName = $this->fileName;
        }

        if (!strlen($extension = pathinfo($fileName, PATHINFO_EXTENSION))) {
            $extension = $this->defaultExtension;
            $baseFile = $fileName;
        }
        elseif (($extension = pathinfo($fileName, PATHINFO_EXTENSION)) === 'blade') {
            $extension = 'php';
            $baseFile = $fileName;
        }
        else {
            $pos = strrpos($fileName, '.');
            $baseFile = substr($fileName, 0, $pos);
        }

        return [$baseFile, $extension];
    }
}
