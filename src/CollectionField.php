<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 21.04.2015
 * Time: 14:04
 */
namespace samsoncms;

use samsonframework\core\RenderInterface;
use samsonframework\orm\QueryInterface;

/**
 * Collection view field descriptor class
 * @package samsoncms
 * @author Vitaly Iegorov <egorov@samsonos.com>
 */
class CollectionField
{
    /** @var string Coolection field real name */
    public $name = true;

    /** @var bool Flag if collection field can be edited */
    public $editable = true;

    /** @var string Collection field title */
    public $title;

    /** @var string Collection field CSS class */
    public $css;

    /** @var string Collection field additional field type */
    public $type = 0;

    /** @var string Path to field view file */
    protected $view = 'www/collection/body/col';

    /**
     * @param string $name Collection field real name
     * @param string $title Collection field title
     * @param int $type  Collection field additional field type
     * @param string $css Collection field CSS class
     * @param bool $editable Collection field title
     */
    public function __construct($name, $title = null, $type = 0, $css = '', $editable = true)
    {
        $this->name = $name;
        $this->title = isset($title{0}) ? $title : $name;
        $this->type = $type;
        $this->css = isset($css{0}) ? $css : $name;
        $this->editable = $editable;
    }

    /**
     * Render collection entity field
     * @param RenderInterface $renderer
     * @param QueryInterface $query
     * @param mixed $object Entity object instance
     * @return string Rendered entity field
     */
    public function render(RenderInterface $renderer, QueryInterface $query, $object)
    {
        // Create input element for field
        $input = m('samsoncms_input_application')->createFieldByType($query, $this->type, $object, $this->name);

        // Render input field view
        return $renderer
            ->view($this->view)
            ->set('class', $this->name)
            ->set($input, 'field')
            ->output();
    }
}
