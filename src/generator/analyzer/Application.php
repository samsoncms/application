<?php
//[PHPCOMPRESSOR(remove,start)]
namespace samsoncms\application\generator\analyzer;

/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>.
 * on 23.03.16 at 16:21
 */
class Application extends \samsoncms\api\generator\analyzer\Virtual
{
    /**
     * Analyze virtual entities and gather their metadata.
     *
     * @return \samsoncms\application\generator\metadata\Application[]
     */
    public function analyze()
    {
        $metadataCollection = [];
        foreach (parent::analyze() as $metadata) {
            $metadata = new \samsoncms\application\generator\metadata\Application();

        }
    }
}
//[PHPCOMPRESSOR(remove,end)]
