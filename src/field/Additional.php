<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 21.04.2015
 * Time: 14:04
 */
namespace samsoncms\field;

use samsonframework\core\RenderInterface;
use samsonframework\orm\QueryInterface;

/**
 * Collection view additional field class
 * @package samsoncms
 * @author Vitaly Iegorov <egorov@samsonos.com>
 */
class Additional extends Generic
{
    /**
     * @param \samson\activerecord\field Database field record
     */
    public function __construct(\samson\activerecord\field $field, $title = '', $css = '', $editable = true)
    {
        $this->name = $field->Name;
        $this->title = isset($title) ? $title : $this->name;
        $this->type = $field->type;
        $this->css = isset($css{0}) ? $css : $this->name;
        $this->editable = $editable;
    }

    /**
     * Render collection entity field inner block
     * @param RenderInterface $renderer
     * @param QueryInterface $query
     * @param mixed $object Entity object instance
     * @return string Rendered entity field
     */
    public function render(RenderInterface $renderer, QueryInterface $query, $object)
    {
        // Set view
        $renderer = $renderer->view($this->innerView);

        // If we need to render input field
        if ($this->editable) {
            // Create input element for field
            $renderer->set(
                m('samsoncms_input_application')->createFieldByType($query, $this->type, $object, $this->name),
                'field'
            );
        } else if (isset($object[$this->name])){ // Or just show a value of entity object field
            $renderer->set($object->value(), 'field_html');
        }

        // Render input field view
        return $renderer
            ->set($this->css, 'class')
            ->set($object, 'item')
            ->output();
    }
}
