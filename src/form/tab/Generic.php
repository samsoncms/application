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
    public $indexView = 'form/tab/index';
    public $headerIndexView = 'form/tab/header/index';
    public $headerContentView = 'form/tab/header/content';
    public $contentView = 'form/tab/main/content';

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
    }

    /**
     * Tab content rendering method
     * @return string Tab content html view
     */
    public function render()
    {
        return $this->renderer->view($this->indexView)
            ->content($this->content())
            ->header($this->header())
            ->tabUrl($this->id)
            ->output();
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
            ->headName(t($this->name, true))
            ->headUrl('#'.$this->id)
            ->output();

        // If tab has sub tabs
        if (sizeof($this->subTabs) > 1) {
            // Render header of each sub tab inside parent tab
            foreach ($this->subTabs as $tab) {
                $tabSubHeader .= $tab->header();
            }
        }

        return $this->renderer->view($this->headerIndexView)
            ->tabHeader($tabHeader)
            ->tabSubHeader($tabSubHeader)
            ->output();
    }
}
