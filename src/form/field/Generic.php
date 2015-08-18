<?php
/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 26.05.2015
 * Time: 17:56
 */
namespace samsoncms\form\field;


class Generic extends \samsoncms\field\Generic
{
    /** @var string Path to field view file */
    protected $innerView = 'www/form/field/generic';

    /** @var string Path to field view file */
    protected $headerView = 'www/form/field/header';
}
