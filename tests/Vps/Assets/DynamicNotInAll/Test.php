<?php
/**
 * @group Assets
 */
class Vps_Assets_DynamicNotInAll_Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Vps_Assets_DynamicNotInAll_Asset::$file = tempnam('/tmp', 'asset');
        file_put_contents(Vps_Assets_DynamicNotInAll_Asset::$file, 'a { color: red; }');
    }

    public function tearDown()
    {
        unlink(Vps_Assets_DynamicNotInAll_Asset::$file);
    }

    public function testMTimeFiles()
    {
        Vps_Assets_Cache::getInstance()->clean();
        Vps_Benchmark::enable();
        Vps_Benchmark::reset();

        $config = clone Zend_Registry::get('config');
        $config->debug->menu = false;
        $config->debug->assets->css = false;
        $loader = new Vps_Assets_Loader($config);
        $dep = $loader->getDependencies();

        $type = 'Vps_Assets_DynamicNotInAll:Test';
        $files = $dep->getAssetUrls($type, 'css', 'web', false);
        $this->assertEquals(2, count($files));
        $f = 'all/web/'.Vps_Registry::get('trl')->getTargetLanguage().'/Vps_Assets_DynamicNotInAll:Test.css';
        $this->assertContains('/assets/'.$f, $files[0]);
        $this->assertContains('/assets/dynamic/Vps_Assets_DynamicNotInAll:Test/Vps_Assets_DynamicNotInAll_Asset', $files[1]);

        $c = $loader->getFileContents($f);
        $this->assertEquals('', trim($c['contents']));
    }
}