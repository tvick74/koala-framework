<?php
class Vps_Media_Cache extends Vps_Cache_Core
{
    public function __construct(array $options = array())
    {
        if (!isset($options['checkComponentSettings'])) $options['checkComponentSettings'] = false;
        if (!isset($options['lifetime'])) $options['lifetime'] = null;
        if (!isset($options['automatic_serialization'])) $options['automatic_serialization'] = true;
        parent::__construct($options);

        $backend = new Zend_Cache_Backend_File(array(
            'cache_dir' => 'cache/media',
            'cache_file_umask' => 0666,
            'hashed_directory_umask' => 0777,
        ));
        $this->setBackend($backend);
    }

    public function remove($id)
    {
        Vps_Benchmark::cacheInfo("Media-Cache '$id' gelöscht.");
        return parent::remove($id);
    }
}