<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 21.04.2015
 * Time: 9:59
 */
namespace samsoncms;

use samson\activerecord\materialfield;
use samson\core\SamsonLocale;
use samsoncms\field\Generic;
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
class MetaCollection extends \samsoncms\api\Collection
{
    /** @var string Index collection view */
    protected $indexView = 'collection/collection';

    /** @var string Entity fields row view */
    protected $rowView = 'collection/row/body';

    /** @var string Empty row view */
    protected $emptyView = 'collection/row/empty';

    /** @var string No found row view */
    protected $notfoundView = 'collection/row/notfound';

    /** @var \samsoncms\field\Generic[] Collection of entity fields to manipulate */
    protected $fields;

    /** @var string Current collection locale */
    protected $locale;

    /** @var array $pagerSizes Collection of possible page sizes */
    protected $pagerSizes = array(10, 15, 20, 50, 100);

    /**
     * Generic collection constructor
     * @param RenderInterface $renderer View render object
     * @param QueryInterface $query Query object
     */
    public function __construct(RenderInterface $renderer, QueryInterface $query, PagerInterface $pager, $locale = SamsonLocale::DEF)
    {
        // Call parent initialization
        parent::__construct($renderer, $query, $pager);

        $this->locale = $locale;

        // Check sorting GET parameters
        if (sizeof($_GET)) {
            foreach ($_GET as $name => $dest) {
                // If get parameter defines sorting
                if (sizeof(str_replace('sort', '', $name))) {
                    // Add collection sorter by current parameter
                    $this->sorter(str_replace('sort', '', $name), $dest);
                }
            }
        }

        // If we have not configured fields before
        if (!sizeof($this->fields)) {
            // TODO: This must be incapsulated into QueryInterface ancestor
            // Get current entity name
            $entity = $query->className();
            // Store its attributes
            foreach ($entity::$_attributes as $field) {
                $this->fields[] = new Generic($field);
            }
        }
    }

    /**
     * Overload renderer
     * @param string $prefix Prefix for view variables
     * @param array $restricted Collection of ignored keys
     * @return array Collection key => value
     */
    public function toView($prefix = null, array $restricted = array())
    {
        // Render pager and collection
        return array(
            $prefix.'html' => $this->render(),
            $prefix.'pager' => $this->pager->total > 1 ? $this->pager->toHTML() : ' ',
            $prefix.'sizeBlock' => $this->renderSizeBlock()
        );
    }

    /**
     * Render pagination size block
     * @return string pagination size block
     */
    public function renderSizeBlock()
    {
        /** @var string $url Address of async collection renderer with page size GET parameter */
        $url = url()->build($this->renderer->id.'/collection').'?pagerSize=';

        /** @var string $options HTML content of options */
        $options = '';

        /** @var int $total Collection items count */
        $total = $this->pager->page_size * $this->pager->total;

        foreach ($this->pagerSizes as $optValue) {
            // Show default option as selected
            $optSelected = $this->pager->page_size == $optValue ? 'selected' : '';

            // Is necessary to show current option
            $options .= $total >= $optValue
                ? $this->renderer->view('collection/sizeblock_option')
                    ->optVal($optValue)
                    ->optSelected($optSelected)
                    ->output()
                : '';
        }

        // Show block only if we have some options
        return $options != ''
            ? $this->renderer->view('collection/sizeblock')->url($url)->options($options)->output()
            : '';
    }

    /**
     * Overload default, render SamsonCMS collection index
     * @param string $items Rendered items
     * @return string Rendered collection block
     */
    public function renderIndex($items)
    {
        $headerHTML = '';
        foreach ($this->fields as $field) {
            $headerHTML .= $field->renderHeader($this->renderer);
        }

        return $this->renderer
            ->view($this->indexView)
            ->set('header', $headerHTML)
            ->set('items', $items)
            ->output();
    }

    /**
     * Overload default, render fields as SamsonCMS input fields
     * @param mixed $item Item to render
     * @return string Rendered collection item block
     */
    public function renderItem($item)
    {
        // Iterate all entity fields
        $fieldsHTML = '';
        foreach ($this->fields as $inputField) {
            // TODO: Maybe we can optimize this requests
            // Find additional field by Name
            $field = null;
            if (dbQuery('field')->cond('Name', $inputField->name)->first($field)) {

                // Create material field query to get additional field record
                $query = dbQuery('materialfield')
                    ->cond('MaterialID', $item->id)
                    ->cond('FieldID', $field->id)
                    ;

                // If additional field is localizable add locale condition
                if ($field->local == 1) {
                    $query->cond('locale', $this->locale);
                }

                // Try to find materialfield record for this item and its field
                $materialfield = null;
                if (!$query->first($materialfield)) {
                    // Create record if it does not exists
                    $materialfield = new materialfield(false);
                    $materialfield->MaterialID = $item->id;
                    $materialfield->locale = $this->locale;
                    $materialfield->FieldID = $field->id;
                    $materialfield->Active = 1;
                    $materialfield->save();
                }

                // Render input field view
                $fieldsHTML .= $inputField->render($this->renderer, $this->query, $materialfield);
            } else {
                // Render input field view
                $fieldsHTML .= $inputField->render($this->renderer, $this->query, $item);
            }
        }

        // Render fields row view
        return $this->renderer
            ->view($this->rowView)
            ->set('cols', $fieldsHTML)
            ->output();
    }

    /**
     * Empty collection block render function
     * @return string Rendered empty collection block
     */
    public function renderEmpty()
    {
        // Render and define which empty collection view to render
        return $this->renderer
            ->view(sizeof($this->search) ? $this->notfoundView : $this->emptyView)
            ->output();
    }
}
