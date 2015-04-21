<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 21.04.2015
 * Time: 9:59
 */
namespace samsoncms;

use samsonframework\core\RenderInterface;
use samsonframework\pager\PagerInterface;
use samsonframework\orm\QueryInterface;

/**
 * Generic SamsonCMS application entities collection.
 * Class provide all basic UI interactions with database entities.
 *
 * @package samsoncms
 * @author Vitaly Iegorov <egorov@samsonos.com>
 */
class Collection extends \samsonframework\collection\Paged
{
    /** @var string Entity fields row view */
    protected $rowView = 'collection/body/row';

    /** @var string Entity field view */
    protected $colView = 'collection/body/col';

    /**
     * Generic collection constructor
     * @param RenderInterface $renderer View render object
     * @param QueryInterface $query Query object
     */
    public function __construct(RenderInterface $renderer, QueryInterface $query, PagerInterface $pager)
    {
        trace($renderer, true);
        // Call parent initialization
        parent::__construct($renderer, $query, $pager);
    }

    /**
     * Overload default, render fields as SamsonCMS input fields
     * @param mixed $item Item to render
     * @return string Rendered collection item block
     */
    public function renderItem($item)
    {
        // TODO: This must be incapsulated into QueryInterface ancestor
        $attributes = $item::$_attributes;

        // Iterate all entity fields
        $fieldsHTML = '';
        foreach ($attributes as $field => $value) {
            // Create input element for field
            $input = m('samsoncms_input_application')->createFieldByType($this->query, 1, $item, $field);

            // Render input field view
            $fieldsHTML = $this->renderer
                ->view($this->colView)
                ->set('class', $field)
                ->set($input, 'field')
                ->output();
        }

        trace($fieldsHTML, true);

        // Render fields row view
        return $this->renderer
            ->view($this->rowView)
            ->set('cols', $fieldsHTML)
            ->output();
    }
}
