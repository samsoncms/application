<?php
//[PHPCOMPRESSOR(remove,start)]
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 23.03.16 at 14:51
 */
namespace samsoncms\application\generator;

/**
 * SamsonCMS application classes generator.
 *
 * @package samsoncms\application\generator
 */
class Application extends \samsoncms\api\generator\Generic
{
    /** Custom css selector in generic constructor */
    const DEFAULT_CUSTOM_TYPE_CSS = '';

    /** User can edit field in list of application */
    const DEFAULT_CUSTOM_TYPE_EDITABLE = 'false';

    /** Field can be sortable in list of application */
    const DEFAULT_CUSTOM_TYPE_SORTABLE = 'false';

    /** Default namespace of custom types */
    const DEFAULT_CUSTOM_TYPE_NAMESPACE = '\\samsonphp\\cms\\types\\';

    /** Default generic class name */
    const DEFAULT_GENERIC_TYPE = 'Generic';

    /** Default control class name */
    const DEFAULT_GENERIC_CONTROL_TYPE = 'Control';

    /** Path to index view of main page */
    const MAIN_INDEX_VIEW = 'www/main_application/index';

    /** Path to item view of main page */
    const MAIN_ITEM_VIEW = 'www/main_application/row';

    /**
     * Class definition generation part.
     *
     * @param \samsoncms\api\generator\metadata\Generic $metadata Entity metadata
     */
    protected function createDefinition($metadata)
    {
        // TODO: Implement createDefinition() method.
    }

    /**
     * Class constructor generation part.
     *
     * @param \samsoncms\api\generator\metadata\Virtual $metadata Entity metadata
     */
    protected function createConstructor($metadata)
    {
        $constructorCode = <<<'EOD'
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

        $this->generator->text($constructorCode);
    }

    public function createSubMenuView(Metadata $metadata, $namespace = __NAMESPACE__)
    {

        $code = <<<'EOD'

    <li>
        <a class="sub_menu_a <?php if(isv('all_materials')):?>active<?php endif?>" href="<?php module_url()?>">
            <i class="icon2 icon2-list"></i>
            <?=$appName?>
        </a>
    </li>
    <li>
        <a class="sub_menu_a <?php if(isv('new_material')):?>active<?php endif?>" href="<?php module_url('new');?>">
            <i class="icon2 icon2-plus"></i> <?php t('Add'); ?> <?=lcfirst($appName);?> <?php t('item')?>
        </a>
    </li>

EOD;

        return str_replace('{{fields}}', implode(',', array_merge(array(), array("\n\t\t\t" . 'new Control()'. "\n\t\t"))), $code);
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
        /*return (new \samsoncms\api\generated\{{collection_name}}Collection($this))
            // Render index
            ->indexView(function($html, $renderer) use ($navName, $mainIndexView) {
                return $renderer->view($mainIndexView)
                    ->set($html, \samsoncms\api\renderable\Collection::ITEMS_VIEW_VARIABLE)
                    ->navName($navName)
                    ->output();
            })
            // Render item
            ->itemView(function($item, $renderer) use ($moduleId, $mainItemView) {
                return $renderer->view($mainItemView)
                    ->set($item, \samsoncms\api\renderable\Collection::ITEM_VIEW_VARIABLE)
                    //->user(m('social')->user())
                    ->moduleId($moduleId)
                    ->output();
            })
            ->output();*/
    }
EOD;

        return str_replace(array('{{collection_name}}'), array($entityName), $code);
    }
}
//[PHPCOMPRESSOR(remove,end)]
