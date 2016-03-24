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

    /** Path to www folder */
    const PATH_TO_RESOURCE = 'www';

    /** File sub menu name without extensions */
    const SUB_MENU_FILE_NAME = 'sub_menu';

    /** Extensions of file view */
    const EXTENSION_VIEW_FILE = '.vphp';

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

            // Folder to module
            $folder = $cachePath . $metadata->entityRealName;

            // Recreate folder
            $dir = $this->createFolder($folder);
            $applicationFile = $dir . DIRECTORY_SEPARATOR . 'Application.php';
            $collectionFile = $dir . DIRECTORY_SEPARATOR . 'Collection.php';

            // Create classes
            file_put_contents($applicationFile, "<?php\n" . $generatorApplication->createApplicationClass($metadata));
            file_put_contents($collectionFile, "<?php\n" . $generatorApplication->createApplicationCollectionClass($metadata));

            // Require classes
            require($applicationFile);
            require_once($collectionFile);

            // Create folder for resource of module
            $dirResource = $this->createFolder($folder . DIRECTORY_SEPARATOR . self::PATH_TO_RESOURCE);
            $subMenuViewFile = $dirResource . DIRECTORY_SEPARATOR . self::SUB_MENU_FILE_NAME . self::EXTENSION_VIEW_FILE;

            // Create sub-menu
            file_put_contents($subMenuViewFile, $generatorApplication->createSubMenuView($metadata));

            // Load created module
            $module->load($dir);
        }
    }

    /**
     * Create folder
     * @param $dirName
     * @return mixed
     * @throws \Exception
     */
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