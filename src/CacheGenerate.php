<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 14.02.2016
 * Time: 14:48
 */

namespace samsoncms\application;

/**
 * Generate files and folder of new application modules
 *
 * @package samsoncms\application
 */
class CacheGenerate
{
    /** @var \samsonframework\orm\DatabaseInterface */
    protected $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    /**
     * Load generated modules
     *
     * @param $module
     * @param $cachePath
     */
    public function loadModules($module, $cachePath)
    {
        // Get generator
        $generatorApplication = new GeneratorApplication($this->database);

        // Create cache path
        $this->createFolder($cachePath);

        // Iterate all structures which should be generated as application
        foreach ($generatorApplication->getMetadata() as $metadata) {

            // Recreate folder
            $dir = $this->createFolder($cachePath. $metadata->entityRealName);
            $applicationFile = $dir . DIRECTORY_SEPARATOR . 'Application.php';
            $collectionFile = $dir . DIRECTORY_SEPARATOR . 'Collection.php';

            // Create classes
            file_put_contents($applicationFile, "<?php\n" . $generatorApplication->createApplicationClass($metadata));
            file_put_contents($collectionFile, "<?php\n" . $generatorApplication->createApplicationCollectionClass($metadata));

            // Require classes
            require($applicationFile);
            require($collectionFile);

            // Load created module
            $module->load($dir);
        }
    }

    public function createFolder($dirName)
    {
        // Check if folder is exists then remove it
        if (is_dir($dirName)) {
            $this->deleteDir($dirName);
        }
        // Create new
        mkdir($dirName);

        return $dirName;
    }


    /**
     * Delete dir recursively
     * @param $dirPath
     */
    public static function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new \Exception("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
}