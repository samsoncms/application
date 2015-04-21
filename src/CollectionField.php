<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 21.04.2015
 * Time: 14:04
 */
namespace samsoncms;

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
}
