<?php 
namespace samsoncms;

use samson\activerecord\dbQuery;
use samson\core\CompressableExternalModule;
use samson\pager\Pager;
use samsonframework\core\ResourcesInterface;
use samsonframework\core\SystemInterface;
use samsonframework\orm\QueryInterface;

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

    /**
     * @var \samsonframework\orm\QueryInterface
     */
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
     * @var self[]
     */
    protected static $loaded = array();

    /**
     * Get all loaded SamsonCMS applications
     * @return Application[] Collection of loaded applications
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

    /**
     * Application constructor.
     *
     * @param string $path
     * @param ResourcesInterface $resources
     * @param SystemInterface $system
     * @param QueryInterface $query
     * @throws ApplicationFormClassNotFound
     *
     * @\samsonframework\containerannotation\InjectArgument(resources="\samsonframework\core\ResourcesInterface")
     * @\samsonframework\containerannotation\InjectArgument(system="\samsonframework\core\SystemInterface")
     * @\samsonframework\containerannotation\InjectArgument(query="\samsonframework\orm\QueryInterface")
     */
    public function  __construct($path, ResourcesInterface $resources, SystemInterface $system, QueryInterface $query)
    {
        $this->query = $query;

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
            throw new ApplicationFormClassNotFound(array($this->id) . ' application form class ' . array($this->formClassName) . ' is not found');
        }

        parent::__construct($path, $resources, $system);
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
    public function __handler($page = 1)
    {
        $description = t($this->description, true);
        $name = t($this->description, true);

        $collection = call_user_func_array(array($this, '__async_collection'), func_get_args());

        // Prepare view
        $this->title($description)
            ->view('collection/index')
            ->set($name, 'name')
            ->set($this->icon, 'icon')
            ->set($description, 'description')
            ->set($collection)
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
            $this->query->entity($this->entity),
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
        return array('status' => 0, 'error' => $this->entity.'#'.$identifier.' entity not found');
    }
    
    /**
     * Generic entity delete controller action
     * @param int $identifier Entity identifier
     * @return array Asynchronous response array
     */
    public function __async_removeentity($identifier)
    {
        /** @var \samsonframework\orm\Record $entity Find database record by identifier */
        $entity = null;
        if ($this->findEntityByID($identifier, $entity)) {
            $entity->delete();
            return $this->__async_collection();
        }

        // Deletion failed
        return array('status' => 0, 'error' => $this->entity.'#'.$identifier.' entity not found');
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
     * New entity creation generic controller action
     * @param int $parentID Parent identifier
     */
    public function __new($parentID = null)
    {
        // Create new entity
        $entity = new $this->entity();

        // Persist
        $entity->save();

        // Go to correct form URL
        url()->redirect($this->system->module('cms')->id.'/'.$this->id . '/form/' . $entity->id);
    }

    /**
     * Generic form rendering controller action
     * @param int $identifier Entity identifier, if 0 is passed or nothing a new entity creation
     *                        form should be shown
     * @return bool Controller action result
     */
    public function __form($identifier)
    {
        // If identifier is passed and entity is not found by this identifier
        $entity = null;
        if ($this->findEntityByID($identifier, $entity)) {

            // Create form object
            $form = new $this->formClassName($this, $this->query->className($this->entity), $entity);
            $formView = $form->render();

            // Set title for all of applications
            $this->title(t('Редактирование', true).' #'.$identifier.' - '.$this->description);

            // Render view
            return $this->view('form/index2')
                ->set($entity->id, 'entityId')
                ->set($entity, 'entity')
                ->set($formView, 'formContent');
        }

        return A_FAILED;
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
                ->set($this->id, 'module')
                ->set(t($this->name, true), 'modulename')
                ->output();
        }
    }

    /** De-serialization handler */
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
     * This method is a shortcut for asynchronous controller actions to avoided repeated
     * code when we search for an entity by identifier and form asynchronous $result array.
     *
     * @see findEntityByID()
     * @param $identifier
     * @param $entity
     * @param array $result
     * @param null $entityName
     * @return boolean|string
     */
    protected function findAsyncEntityByID($identifier, & $entity, array & $result = array(), $entityName = null)
    {
        if ($this->findEntityByID($identifier, $entity, $entityName)) {
            $result['status'] = true;
        } else { // Entity not found
            $result['status'] = false;
            $result['error'] = 'Material entity #' . $identifier . ' not found';
        }

        return $result['status'];
    }

    /**
     * Get entity from database by identifier
     * @param int $identifier Entity identifier
     * @param \samsonframework\orm\Record Found entity
     * @return boolean
     */
    protected function findEntityByID($identifier, & $entity, $entityName = null)
    {
        // If no specific entity name is passed use application default entity name
        $entityName = !isset($entityName) ? $this->entity : $entityName;

        return $this->query->className($entityName)->id($identifier)->first($entity);
    }
}
