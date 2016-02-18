<?php
/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 26.05.2015
 * Time: 17:08
 */

namespace samsoncms\form\tab;


use samsonframework\core\RenderInterface;
use samsonframework\orm\QueryInterface;
use samsonframework\orm\Record;

abstract class Generic
{
    /** @var string Default path to main view */
    public $indexView = 'form/tab/index';

    /** @var string Default path to content view */
    public $contentView = 'form/tab/main/content';

    /** @var string Default path to tab header view */
    public $headerIndexView = 'form/tab/header/index';

    /** @var string Default path to tab content view */
    public $headerContentView = 'form/tab/header/content';

    /** @var string Tab name */
    protected $name = '';

    /** @var string Tab identifier */
    protected $id = '';

    /** @var self[] Collection of sub tabs */
    protected $subTabs = array();

    /** @var \samson\core\IViewable module for html views rendering */
    protected $renderer;

    /** @var QueryInterface Query */
    protected $query;

    /** @var Record Tab for each form is created */
    protected $entity;

    /** @var bool Tab visibility status */
    public $show = true;

    /**
     * @param RenderInterface $renderer
     * @param QueryInterface $query
     * @param Record $entity
     */
    public function __construct(RenderInterface $renderer, QueryInterface $query, Record $entity)
    {
        // Set module renderer for this tab
        $this->renderer = $renderer;

        // Set query object for this tab
        $this->query = $query;

        // Set db entity of this tab
        $this->entity = $entity;

        // If identifier is not specified
        if (!isset($this->id{0})) {
            $this->id = str_ireplace('\\', '_', __CLASS__);
        }
    }

    /**
     * Tab content rendering method
     * @return string Tab content html view
     */
    public function render()
    {
        if ($this->show) {
            return $this->renderer->view($this->indexView)
                ->set($this->content(), 'content')
                ->set($this->header(), 'header')
                ->set($this->id, 'tabUrl')
                ->output();
        } else {
            return '';
        }
    }

    /**
     * Tab content rendering method
     * @return mixed Tab content view
     */
    public abstract function content();

    /**
     * Tab header rendering method
     * @return mixed Tab header view
     */
    public function header()
    {
        /** @var string $tabHeader Header html view */
        $tabSubHeader = '';

        // Set content of header view
        $tabHeader = $this->renderer->view($this->headerContentView)
            ->set(t($this->name, true), 'headName')
            ->set('#'.$this->id, 'headUrl')
            ->output();

        // If tab has sub tabs
        if (sizeof($this->subTabs) > 1) {
            // Render header of each sub tab inside parent tab
            foreach ($this->subTabs as $tab) {
                $tabSubHeader .= $tab->header();
            }
        }

        return $this->renderer->view($this->headerIndexView)
            ->set($tabHeader, 'tabHeader')
            ->set($tabSubHeader, 'tabSubHeader')
            ->output();
    }
}
