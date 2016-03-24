<?php
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 24.03.16 at 13:34
 */
namespace samsoncms\application\generator;

/**
 * SamsonCMS application collection class generator.
 *
 * @package samsoncms\application\generator
 */
class Collection extends \samsoncms\api\generator\Generic
{
    /** Default control class name */
    const DEFAULT_GENERIC_CONTROL_TYPE = 'Control';

    /**
     * Class definition generation part.
     *
     * @param \samsoncms\application\generator\metadata\Application $metadata Entity metadata
     */
    protected function createDefinition($metadata)
    {
        // TODO: Implement createDefinition() method.
    }

    /**
     * Class constructor generation part.
     *
     * @param \samsoncms\api\generator\metadata\Virtual $metadata Entity metadata
     */
    protected function createConstructor($metadata)
    {
        $constructorCode = <<<'EOD'
    /**
     * Generic SamsonCMS application collection constructor
     *
     * @param RenderInterface $renderer View render object
     * @param QueryInterface $query Query object
     */
    public function __construct($renderer, $query = null, $pager = null)
    {
        parent::__construct($renderer, $query, $pager);

        // Generic of fields
        $this->fields = array({{fields}});
    }
EOD;
        // Iterate all field and create generic constructor for them
        foreach ($metadata->showFieldsInList as $fieldID) {
            // Create constructor for custom type or if it not exists then use cms defined type
            $genericFields[] = $this->genericCustomTypeConstructor(
                $metadata->customTypeFields[$fieldID],
                $metadata->allFieldIDs[$fieldID],
                strlen($metadata->fieldRawDescriptions[$fieldID]) === 0 ? $metadata->allFieldIDs[$fieldID] : $metadata->fieldRawDescriptions[$fieldID],
                $metadata->allFieldCmsTypes[$fieldID],
                self::DEFAULT_CUSTOM_TYPE_CSS,
                self::DEFAULT_CUSTOM_TYPE_EDITABLE,
                self::DEFAULT_CUSTOM_TYPE_SORTABLE
            );
        }

        $constructorCode = str_replace(
            '{{fields}}',
            implode(',', array_merge(
                    $genericField,
                    array("\n\t\t\t" . 'new ' . self::DEFAULT_GENERIC_CONTROL_TYPE . '()'. "\n\t\t"))
            ),
            $constructorCode);

        $this->generator->text($constructorCode);
    }
}
