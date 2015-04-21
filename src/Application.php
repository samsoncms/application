<?php 
namespace samsoncms;

use samson\activerecord\dbQuery;
use samson\core\CompressableExternalModule;
use samson\pager\Pager;

/**
 * SamsonCMS external compressible application for integrating
 * @author Vitaly Iegorov <egorov@samsonos.com>
 */
class Application extends CompressableExternalModule
{
    /** Application name */
    public $name;

    /** Application description */
    public $description;

    /** Flag for hiding Application icon in main menu */
    public $hide = false;

    /** @var string Application main menu icon */
    public $icon = 'book';

    /** @var string Entity class name */
    protected $entity = 'material';

    /** @var string Collection class name for rendering entities collection */
    protected $collectionClassName = 'Collection';

    /** @var int Collection page size */
    protected $pageSize = 10;

    /**
     * Collection of loaded SamsonCMS applications
     * @var App[]
     */
    protected static $loaded = array();

    /**
     * Get all loaded SamsonCMS applications
     * @return App[] Collection of loaded applications
     * @deprecated
     */
    public static function loaded()
    {
        return self::$loaded;
    }

    /**
     * Find SamsonCMS application by identifier
     * @param string $id SamsonCMS application identifier
     * @param mixed $app Variable to return found SamsonCMS application
     * @return boolean True if SamsonCMS application has been found
     */
    public static function find($id, & $app = null)
    {
        // Clear var as someone can pass anything in it
        $app = isset(self::$loaded[$id]) ? self::$loaded[$id] : null;

        // Return if module exists
        return isset($app);
    }

    /** Constructor */
    public function __construct($path = null, $vid = null, $resources = null)
    {
        // Save CMSApplication instance
        if (!in_array(get_class($this), array(__CLASS__, 'samson\\cms\\App'))) {
            self::$loaded[$this->id] = & $this;
        }

        // TODO: WTF? Why it does not use $this namespace?
        // Temporary build collection class name manually
        $className = get_class($this);
        $namespace = substr($className, 0, strrpos($className, '\\'));
        $this->collectionClassName = $namespace.'\\Collection';

        parent::__construct($path, $vid, $resources);
    }

    /**
     * Universal controller action.
     * Entity collection rendering
     */
    public function __handler()
    {
        // Prepare view
        $this->title(t($this->description, true))
            ->view('collection/index')
            ->set('name', $this->name)
            ->set('icon', $this->icon)
            ->set('description', $this->description)
            ->set($this->__async_collection())
        ;
    }

    /**
     * Render entities collection
     * @return array Asynchronous response array
     */
    public function __async_collection($page = 1)
    {
        // Create entities collection from defined parameters
        $entitiesCollection = new $this->collectionClassName(
            $this,
            new dbQuery($this->entity),
            new Pager($page, $this->pageSize, 'user/collection')
        );

        // Generate Asynchronous response array
        return array_merge(
            array('status' => 1),
            $entitiesCollection->toView('collection_')
        );
    }

    /**
     * Delete entity
     * @return array Asynchronous response array
     */
    public function __async_remove2($identifier)
    {

    }

    /**
     * Clone sentity
     * @return array Asynchronous response array
     */
    public function __async_clone2($identifier)
    {

    }

    /**
     * Edit entity
     * @return array Asynchronous response array
     */
    public function __async_edit2($identifier)
    {

    }

    /**
     * Generic handler for rendering SamsonCMS application "Main page"
     * @deprecated Subscribe to samsoncms/template event
     */
    public function main()
    {
        return false;
    }

    /**
     * Generic handler for rendering SamsonCMS application "Sub-menu"
     * @deprecated Subscribe to samsoncms/template event
     */
    public function submenu()
    {
        return false;
    }

    /**
     * Generic handler for rendering SamsonCMS application "Help"
     * @deprecated
     */
    public function help($category = null)
    {
        if ($this->findView('help/index')) {
            return $this->view('help/index')->output();
        } else {
            return false;
        }
    }

    /** Deserialization handler */
    public function __wakeup()
    {
        parent::__wakeup();

        // Add instance to static collection
        self::$loaded[ $this->id ] = & $this;
    }
}
