<?php
/**
 * @package Model
 * @internal
 */
class Kwf_Model_Mongo_MapReduce_Row extends Kwf_Model_Mongo_Row
{
    public function __construct(array $config)
    {
        if (is_array($config['data']['value'])) {
            $data = (array)$config['data'];
            $data = array_merge(
                $data,
                $data['value']
            );
            unset($data['value']);
            $config['data'] = $data;
        }
        parent::__construct($config);
    }
}
