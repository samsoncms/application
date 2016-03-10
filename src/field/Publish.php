<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 21.04.2015
 * Time: 14:52
 */
namespace samsoncms\field;

use samsonframework\core\RenderInterface;
use samsonframework\orm\QueryInterface;

/**
 * Overridden control field
 * @package samsoncms\app\user
 */
class Publish extends Generic
{
    /** @var string Path to field view file */
    protected $innerView = 'www/collection/field/publish';

    /**  Overload parent constructor and pass needed params there */
    public function __construct()
    {
        parent::__construct('Published', t('Показывать', true), 11, 'publish');
    }
}
