<?php
/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 26.05.2015
 * Time: 19:02
 */
namespace samsoncms\form\tab;

use samsonframework\core\RenderInterface;
use samsonframework\orm\QueryInterface;
use samsonframework\orm\Record;

/**
 *
 * @package samsoncms\form\tab
 */
class Entity extends Generic
{
    /** @var string Tab name or identifier */
    protected $name = 'Entity Tab';

    protected $id = 'entity_tab';

    /** @var Generic[] Collection of entity fields that must be rendered in tab content */
    protected $fields = array();

    /** @inheritdoc */
    public function __construct(RenderInterface $renderer, QueryInterface $query, Record $entity)
    {
        // If fields are not configured
        if (!sizeof($this->fields)) {
            // Add all entity attributes to fields array
            foreach ($entity::$_attributes as $field) {
                $this->fields[] = new \samsoncms\form\field\Generic($field);
            }
        }

        // Call parent constructor to define all class fields
        parent::__construct($renderer, $query, $entity);
    }

    /** @inheritdoc */
    public function content()
    {
        $view = '';

        foreach ($this->fields as $field) {
            $view .= $field->renderHeader($this->renderer);
            $view .= $field->render($this->renderer, $this->query, $this->entity);
        }

        return $this->renderer->view($this->contentView)->content($view)->output();
    }
}