<?php
/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 26.05.2015
 * Time: 17:08
 */

namespace samsoncms\form;


use samsoncms\form\tab\Entity;
use samsoncms\form\tab\Generic;
use samsonframework\core\RenderInterface;
use samsonframework\orm\QueryInterface;
use samsonframework\orm\Record;
use samsonphp\event\Event;

class Form
{
    public $indexView = 'form/form2';

    /** @var Generic[]  */
    protected $tabs = array();

    /** @var Record Entity for each form is created */
    protected $entity;

    /** @var \samson\core\IViewable Module for html views rendering */
    protected $renderer;

    /** @var QueryInterface Query */
    protected $query;

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

        // If form tabs are not configured
        if (!sizeof($this->tabs)) {
            // Add MainTab to form tabs
            $this->tabs = array(
                new Entity($renderer, $query, $entity)
            );
        }

        // Fire new event after creating form tabs
        Event::fire('samsoncms.form.created', array(& $this));
    }

    /**
     * Render form view
     * @return string Form content html view
     */
    public function render()
    {
        // Tab contents view
        $tabs = '';

        // Render header and content for each tab
        foreach ($this->tabs as $tab) {
            $tabs .= $tab->render();
        }

        return $this->renderer->view($this->indexView)->tabs($tabs)->output();
    }
}
