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

    /** @var \samsonframework\orm\QueryInterface */
    protected $query;

    /** @var string Entity class name */
    protected $entity = '\samson\activerecord\material';

    /** @var string Collection class name for rendering entities collection */
    protected $collectionClass;

    /** @var string Form class name for rendering entities form */
    protected $formClassName = '\samsoncms\form\Form';

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

        // If now collection class is set
        if (!isset($this->collectionClass{0})) {
            // Temporary build collection class name manually
            $className = get_class($this);
            $namespace = substr($className, 0, strrpos($className, '\\'));
            $this->collectionClass = $namespace . '\\Collection';
        }

        // Check form class configuration
        if (!class_exists($this->formClassName)) {
            e(
                '## application form class(##) is not found',
                E_CORE_ERROR,
                array($this->id, $this->formClassName)
            );
        }

        // Create database object
        $this->query = new dbQuery('material');

        parent::__construct($path, $vid, $resources);
    }

    /** Module initialization */
    public function init(array $params = array())
    {
        \samsonphp\event\Event::subscribe('help.content.rendered', array($this, 'help'));
        \samsonphp\event\Event::subscribe('help.submenu.rendered', array($this, 'helpMenu'));
    }

    /**
     * Universal controller action.
     * Entity collection rendering
     */
    public function __handler()
    {
        $description = t($this->description, true);
        $name = t($this->description, true);

        // Prepare view
        $this->title($description)
            ->view('collection/index')
            ->set('name', $name)
            ->set('icon', $this->icon)
            ->set('description', $description)
            ->set(call_user_func_array(array($this, '__async_collection'), func_get_args()))
        ;
    }

    /**
     * Render entities collection
     * @return array Asynchronous response array
     */
    public function __async_collection($page = 1)
    {
        // Create entities collection from defined parameters
        $entitiesCollection = new $this->collectionClass(
            $this,
            $this->query->className($this->entity),
            new Pager($page, $this->pageSize, $this->id . '/collection')
        );

        // Generate Asynchronous response array
        return array_merge(
            array('status' => 1),
            $entitiesCollection->toView('collection_')
        );
    }

    /**
     * Generic entity delete controller action
     * @param int $identifier Entity identifier
     * @return array Asynchronous response array
     */
    public function __async_remove2($identifier)
    {
        /** @var \samsonframework\orm\Record $entity Find database record by identifier */
        $entity = null;
        if ($this->findEntityByID($identifier, $entity)) {
            $entity->delete();
            return array('status' => 1);
        }

        // Deletion failed
        return array('status' => 0, 'error' => $this->entity.'#'.$id.' entity not found');
    }

    /**
     * Clone entity
     * @param int $identifier Entity identifier
     * @return array Asynchronous response array
     */
    public function __async_clone2($identifier)
    {

    }

    /**
     * Edit entity
     * @param int $identifier Entity identifier
     * @return array Asynchronous response array
     */
    public function __async_edit2($identifier)
    {

    }

    /**
     * Generic form rendering controller action
     * @param int $identifier Entity identifier, if 0 is passed or nothing a new entity creation
     *                        form should be shown
     */
    public function __form($identifier = 0)
    {
        // If identifier is passed and entity is not found by this identifier
        $entity = null;
        if (func_num_args() == 1 && !$this->findEntityByID($identifier, $entity)) {
            // Create new entity
            $entity = new $this->entity();
            $entity->save();
        }
        // Otherwise we have found entity and its stored at $entity
        // TODO: what to render if entity is not found

        // Create form object
        $form = new $this->formClassName($this, $this->query->className($this->entity), $entity);
        $formView = $form->render();

        // Render view
        $this->view('form/index2')
            ->set('entityId', $entity->id)
            ->set($entity, 'entity')
            ->set('formContent', $formView)
        ;
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
     * Generic handler for rendering SamsonCMS application "Help" content.
     * @param string $html Content HTML
     */
    public function help(&$html, $category, $subCategory, $subSubCategory)
    {
        if ($this->findView('help/index')) {
            $html .= $this->view('help/index')->output();
        }
    }

    /**
     * Generic handler for rendering SamsonCMS application "Help" menu.
     * @param string $html Sub-menu HTML
     */
    public function helpMenu(&$html, $renderer, $category, $subCategory, $subSubCategory)
    {
        if ($this->findView('help/index')) {
            $html .= $renderer->view('submenu-item')
                ->set('module', $this->id)
                ->set('modulename', t($this->name, true))
            ->output();
        }
    }

    /** Deserialization handler */
    public function __wakeup()
    {
        parent::__wakeup();

        // Save CMSApplication instance
        if (!in_array(get_class($this), array(__CLASS__, 'samson\\cms\\App'))) {
            // Add instance to static collection
            self::$loaded[ $this->id ] = & $this;
        }
    }

    /**
     * Get entity from database by identifier
     * @param int $identifier Entity identifier
     * @param \samsonframework\orm\Record Found entity
     * @return boolean
     */
    protected function findEntityByID($identifier, & $entity)
    {
        return $this->query->className($this->entity)->id($identifier)->first($entity);
    }
}
