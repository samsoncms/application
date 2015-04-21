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
 * Collection view field descriptor class
 * @package samsoncms
 * @author Vitaly Iegorov <egorov@samsonos.com>
 */
class Generic
{
    /** @var string Coolection field real name */
    protected $name;

    /** @var bool Flag if collection field can be edited */
    protected $editable = true;

    /** @var string Collection field title */
    protected $title;

    /** @var string Collection field CSS class */
    protected $css;

    /** @var string Collection field additional field type */
    protected $type = 0;

    /** @var string Path to field view file */
    protected $innerView = 'www/collection/field/generic';

    /** @var string Path to field view file */
    protected $headerView = 'www/collection/header/col';


    /**
     * @param string $name Collection field real name
     * @param string $title Collection field title
     * @param int $type  Collection field additional field type
     * @param string $css Collection field CSS class
     * @param bool $editable Collection field title
     */
    public function __construct($name, $title = null, $type = 0, $css = '', $editable = true)
    {
        $this->name = isset($this->name{0}) ? $this->name : $name;
        $this->title = isset($this->title{0}) ? $this->title : isset($title) ? $title : $name;
        $this->type = isset($this->type) ? $this->type : isset($type) ? $type : 0;
        $this->css = isset($this->css{0}) ? $this->css : isset($css{0}) ? $css : $name;
        $this->editable = isset($this->editable) ? $this->editable : isset($editable) ? $editable : true;
    }

    /**
     * Render collection entity field header block
     * @param RenderInterface $renderer
     * @param QueryInterface $query
     * @param mixed $object Entity object instance
     * @return string Rendered entity field
     */
    public function renderHeader(RenderInterface $renderer)
    {
        // Render input field view
        return $renderer
            ->view($this->headerView)
            ->set('class', $this->css)
            ->set('field', $this->title)
            ->output();
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
        } else { // Or just show a value of entity object field
            $renderer->set('field_html', $object[$this->name]);
        }

        // Render input field view
        return $renderer
            ->set('class', $this->css)
            ->output();
    }
}
