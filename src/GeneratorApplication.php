<?php
//[PHPCOMPRESSOR(remove,start)]
/**
 * Created by PhpStorm.
 * User: VITALYIEGOROV
 * Date: 09.12.15
 * Time: 14:34
 */
namespace samsoncms\application;

use samson\activerecord\Structure;
use samsoncms\api\Field;
use samsoncms\api\generator\Metadata;
use samsoncms\api\generator\Generator;
use samsoncms\api\generator\exception\ParentEntityNotFound;
use samsonframework\orm\DatabaseInterface;

/**
 * Entity classes generator.
 * @package samsoncms\api
 */
class GeneratorApplication extends Generator
{
    /** Path to index view of main page */
    const MAIN_INDEX_VIEW = 'www/main_application/index';

    /** Path to item view of main page */
    const MAIN_ITEM_VIEW = 'www/main_application/row';

    /**
     * Generator constructor.
     * @param DatabaseInterface $database Database instance
     * @throws ParentEntityNotFound
     * @throws \samsoncms\api\exception\AdditionalFieldTypeNotFound
     */
    public function __construct(DatabaseInterface $database)
    {
        parent::__construct($database);

        /**
         * Fill metadata only with structures which have to be generated
         */
        $this->fillMetadata(function($structureRow) {

            // Skip all wrong structures
            if ($structureRow['applicationGenerate'] != 1) {
                return false;
            }
        });
    }

    /**
     * Get metadata
     * @return \samsoncms\api\generator\Metadata[]
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Generate constructor for application class.
     */
    protected function generateConstructorApplicationClass()
    {

$class = <<<'EOD'

    /**
     * Render materials list with pager
     *
     * @param string $navigationId Structure identifier
     * @param string $search Keywords to filter table
     * @param int $page Current table page
     * @return array Asynchronous response containing status and materials list with pager on success
     * or just status on asynchronous controller failure
     */
    public function __async_collection($navigationId = '0', $search = '', $page = 1)
    {
        return parent::__async_collection(self::$navigation, $search, $page);
    }

EOD;

        return $class;
    }

    /**
     * Generate constructor for application class.
     */
    protected function generateConstructorApplicationCollectionClass($genericField)
    {

$class = <<<'EOD'

    /**
     * Generic collection constructor
     *
     * @param RenderInterface $renderer View render object
     * @param QueryInterface $query Query object
     */
    public function __construct($renderer, $query = null, $pager = null)
    {
        parent::__construct($renderer, $query, $pager);

        // Generic of fields
        $this->fields = array({{fields}});
    }
EOD;

        return str_replace('{{fields}}', implode(',', array_merge($genericField, array("\n\t\t\t" . 'new Control()'. "\n\t\t"))), $class);
    }

    /**
     * @param $name
     * @param $description
     * @param int $type
     * @param string $css
     * @param string $editable
     * @param string $sortable
     * @return string
     */
    public function genericConstructor($name, $description, $type = 0, $css = '', $editable = 'true', $sortable = 'false')
    {
        return "\n\t\t\tnew Generic('$name', t('$description', true), $type, '$css', $editable, $sortable)";
    }

    /**
     * Render method for rendering view of entity on main page
     * @param $entityName
     * @return string
     */
    public function renderViewsOnMainPage($entityName)
    {

$code = <<<'EOD'

    /** Output for main page1 */
    public function main()
    {
        // Get module id
        $moduleId = $this->id;

        // Get module description
        $navName = $this->description;

        // Get path to views
        $mainIndexView = $this->mainIndexView;
        $mainItemView = $this->mainItemView;

        // Return material block HTML on main page
        return (new \samsoncms\api\{{collection_name}}Collection($this))
            // Render index
            ->indexView(function($renderer, $html) use ($navName, $mainIndexView) {
                return $renderer->view($mainIndexView)
                    ->set(\samsoncms\api\renderable\Collection::ITEMS_VIEW_VARIABLE, $html)
                    ->navName($navName)
                    ->output();
            })
            // Render item
            ->itemView(function($renderer, $item) use ($moduleId, $mainItemView) {
                return $renderer->view($mainItemView)
                    ->set(\samsoncms\api\renderable\Collection::ITEM_VIEW_VARIABLE, $item)
                    ->user(m('social')->user())
                    ->moduleId($moduleId)
                    ->output();
            })
            ->output();
    }
EOD;

        return str_replace(array('{{collection_name}}'), array($entityName), $code);
    }

    /**
     * Create entity query PHP class code.
     *
     * @param Metadata $metadata Entity metadata
     * @param string $namespace Namespace of generated application class
     *
     * @return string Generated entity query PHP class code
     */
    public function createApplicationClass(Metadata $metadata, $namespace = __NAMESPACE__)
    {
        $class = "\n" . 'namespace ' . $namespace . ';';
        $class .= "\n";

        $this->generator
            ->text($class)
            ->multiComment(array('Class application for "'.$metadata->entityRealName.'"'))
            ->defClass($this->entityName($metadata->entityRealName) . 'Application', '\samsoncms\app\material\Application')
            ->commentVar('string', 'Application name')
            ->defVar('public $name', ucfirst($this->fieldName($metadata->entityRealName)))
            ->commentVar('string', 'Application description')
            ->defVar('public $description', ucfirst($this->fieldName($metadata->entityRealName)))
            ->commentVar('string', 'Identifier')
            ->defVar('protected $id', 'cms_' . $this->fieldName($metadata->entityRealName))
            ->commentVar('string', 'Icon class')
            ->defVar('public $icon', $metadata->iconApplication)
            ->commentVar('bool', 'Flag for hiding Application icon in main menu')
            ->defClassVar('$hide', 'public', !$metadata->showApplication ? 1 : 0)
            ->commentVar('string', 'Path to rendering index view in main page')
            ->defClassVar('$mainIndexView', 'public', self::MAIN_INDEX_VIEW)
            ->commentVar('string', 'Path to rendering item view in main page')
            ->defClassVar('$mainItemView', 'public', self::MAIN_ITEM_VIEW)
            ->commentVar('array', 'Collection of structures related to entity')
            ->defClassVar('$navigation', 'public static', (int)$metadata->entityID)
            ->commentVar('array', 'All structures which have to have material at creation')
            ->defClassVar('$structures', 'public static', array_merge(array($metadata->entityID), $metadata->childNavigationIDs))
            ->commentVar('string', 'Collection class name for rendering entities collection')
            ->defClassVar('$collectionClass', 'protected', $namespace . '\\' . $this->entityName($metadata->entityRealName). 'ApplicationCollection')
            ->text($this->generateConstructorApplicationClass())
            ->text($metadata->renderMainApplication ? $this->renderViewsOnMainPage($metadata->entityRealName) : '')
            ->endClass()
        ;

        return $this->formatTab($this->generator->flush());
    }

    /**
     * Create entity query PHP class code.
     *
     * @param Metadata $metadata Entity metadata
     * @param string $namespace Namespace of generated application class
     *
     * @return string Generated entity query PHP class code
     */
    public function createApplicationCollectionClass(Metadata $metadata, $namespace = __NAMESPACE__)
    {
        $class = "\n" . 'namespace ' . $namespace . ';';
        $class .= "\n\n" . 'use samsoncms\field\Generic;';
        $class .= "\n" . 'use samsoncms\field\Control;';
        $class .= "\n\n";

        $this->generator
            ->text($class)
            ->multiComment(array('Collection for application "'.$metadata->entityRealName.'"'))
            ->defClass($this->entityName($metadata->entityRealName) . 'ApplicationCollection', '\samsoncms\app\material\Collection');

        $genericFields = array();

        // Iterate all field and create generic constructor for them
        foreach ($metadata->showFieldsInList as $fieldID) {
            $genericFields[] = $this->genericConstructor(
                $metadata->allFieldIDs[$fieldID],
                $metadata->fieldRawDescriptions[$fieldID] ?: $metadata->allFieldNames[$fieldID],
                $metadata->allFieldCmsTypes[$fieldID]
            );
        }

        $this->generator
            ->text($this->generateConstructorApplicationCollectionClass($genericFields))
            ->endClass()
        ;

        return $this->formatTab($this->generator->flush());
    }
}

//[PHPCOMPRESSOR(remove,end)]
