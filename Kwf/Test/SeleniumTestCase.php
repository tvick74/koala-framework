<?php
class Vps_Test_SeleniumTestCase extends PHPUnit_Extensions_SeleniumTestCase
{
    protected $backupStaticAttributes = false;
    protected $autoStop = false;
    protected $_unitTestCookie;
    protected $_domain = null;

    protected function initTestDb($bootstrapFile)
    {
        Vps_Test_SeparateDb::createSeparateTestDb($bootstrapFile);
        $dbName = Vps_Test_SeparateDb::getDbName();
        $this->createCookie('test_special_db='.$dbName, 'path=/, max_age=60*5');
        Vps_Registry::set('db', Vps_Test::getTestDb($dbName));
        Vps_Model_Abstract::clearInstances();
    }

    public static function suite($className)
    {
        self::$browsers = array();
        foreach (Vps_Registry::get('config')->server->testBrowser as $b) {
            if (!$b->browser) continue; //deaktiviert
            $b = $b->toArray();
            if (isset($b['port'])) $b['port'] = (int)$b['port'];
            if (isset($b['timeout'])) $b['timeout'] = (int)$b['timeout'];
            self::$browsers[] = $b;
        }
        if (!self::$browsers) {
            throw new Vps_Exception("No test-Browser avaliable");
        }
        return parent::suite($className);
    }

    public function tearDown()
    {
        Vps_Test_SeparateDb::restoreTestDb(); // macht das nur wenns eine gibt
        parent::tearDown();
    }

    protected function setUp()
    {
        if (!$cfg = Vps_Registry::get('testServerConfig')) {
            throw new Vps_Exception("testServerConfig not set");
        }
        $d = $this->_domain;
        if (!$d) {
            $domain = $cfg->server->domain;
        } else {
            if (!isset($cfg->vpc->domains->$d)) {
                throw new Vps_Exception("Domain '$d' not found in config");
            }
            $domain = $cfg->vpc->domains->$d->domain;
        }
        $this->setBrowserUrl('http://'.$domain.'/');

        $this->_unitTestCookie = md5(uniqid('testId', true));

        $this->captureScreenshotOnFailure = Vps_Setup::getConfigSection()=='vivid-test-server';
        $this->screenshotPath = '/mnt/screenshots';
        $this->screenshotUrl = 'http://screenshots.vivid';
        parent::setUp();

    }

    protected function runTest()
    {
        parent::runTest();
        try {
            $this->stop();
        } catch (RuntimeException $e) { }
    }

    protected function onNotSuccessfulTest(Exception $e)
    {
        parent::onNotSuccessfulTest($e);
        if (Zend_Registry::get('config')->server->autoStopTest) {
            try {
                $this->stop();
            }catch (RuntimeException $x) { }
        }
    }

    public function clickAndWait($link)
    {
        $this->click($link);
        $this->waitForPageToLoad();
    }
    public function sessionRestart()
    {
        $this->open("/vps/debug/session-restart");
    }
    public function assertContainsText($locator, $text)
    {
        if (is_array($text)) {
            foreach ($text as $k=>$i) {
                $this->assertContainsText($locator.'['.$k.']', $i);
            }
        } else {
            $this->assertTrue((bool)preg_match('#'.preg_quote($text).'#', $this->getText($locator)));
        }
    }

    public function start()
    {
        parent::start();
        $this->open('/vps/test/vps_start');
        $this->deleteAllVisibleCookies();
        $this->createCookie('unitTest='.$this->_unitTestCookie, 'path=/, max_age=60*5');
    }

    public function __call($command, $arguments)
    {
        $ret = parent::__call($command, $arguments);
        return $ret;
    }

    protected function defaultAssertions($command)
    {
        if ($command == 'waitForPageToLoad' || $command == 'open' || $command == 'waitForConnections') {
            if ($this->isElementPresent('css=#exception')) {
                $this->runScript('document.getElementById("exception").style.display="block";');
                $exception = $this->getText('css=#exception');
                $exception = unserialize(base64_decode($exception));
                if ($exception instanceof Exception) {
                    throw $exception;
                } else {
                    throw new Vps_Exception($exception);
                }
            }
            $this->assertTextNotPresent('regexp:File not found|Seite wurde nicht gefunden|konnte nicht gefunden werden|was not found on this server|Exception|Fatal error|Parse error');
            $this->assertTitleNotContains('Internal Server Error');
        }
    }

    public function openVpc($url)
    {
        return $this->open('/vps/vpctest/'.Vps_Component_Data_Root::getComponentClass().$url);
    }

    public function openVpcEdit($componentClass, $componentId)
    {
        $url = '/vps/componentedittest/'.
                Vps_Component_Data_Root::getComponentClass().'/'.
                $componentClass.'/Index'.
                '?componentId='.$componentId;
        return $this->open($url);
    }

    protected function waitForConnections()
    {
        $this->waitForCondition('selenium.browserbot.getCurrentWindow().Vps.Connection.runningRequests==0');
        $this->defaultAssertions('waitForConnections');
    }

    //kopiert von PhpUnit, nur um eigenen Driver verwenden zu können
    protected function getDriver(array $browser)
    {
        if (isset($browser['name'])) {
            if (!is_string($browser['name'])) {
                throw new InvalidArgumentException;
            }
        } else {
            $browser['name'] = '';
        }

        if (isset($browser['browser'])) {
            if (!is_string($browser['browser'])) {
                throw new InvalidArgumentException;
            }
        } else {
            $browser['browser'] = '';
        }

        if (isset($browser['host'])) {
            if (!is_string($browser['host'])) {
                throw new InvalidArgumentException;
            }
        } else {
            $browser['host'] = 'localhost';
        }

        if (isset($browser['port'])) {
            if (!is_int($browser['port'])) {
                throw new InvalidArgumentException;
            }
        } else {
            $browser['port'] = 4444;
        }

        if (isset($browser['timeout'])) {
            if (!is_int($browser['timeout'])) {
                throw new InvalidArgumentException;
            }
        } else {
            $browser['timeout'] = 30;
        }

        $driver = new Vps_Test_SeleniumTestCase_Driver;
        $driver->setName($browser['name']);
        $driver->setBrowser($browser['browser']);
        $driver->setHost($browser['host']);
        $driver->setPort($browser['port']);
        $driver->setTimeout($browser['timeout']);
        $driver->setTestCase($this);
        $driver->setTestId($this->testId);

        $this->drivers[] = $driver;

        return $driver;
    }

    protected function _getLatestMail($select = null)
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Util_Model_MailLog');
        if (!$select) $select = $m->select();
        $select->order('id', 'DESC');
        $select->limit(1);
        return $this->_getMails($select)->current();
    }

    protected function _getMails($select = null)
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Util_Model_MailLog');
        if (!$select) $select = $m->select();
        return $m->getRows($select
                    ->whereEquals('identifier', $this->_unitTestCookie));
    }

    public function assertBodyText($search)
    {
        $this->assertEquals($search, $this->getText('//body'));
    }

    public function assertTitleContains($title, $message = '')
    {
        $this->assertContains($title, $this->getTitle(), $message);
    }

    public function assertTitleNotContains($title, $message = '')
    {
        $this->assertNotContains($title, $this->getTitle(), $message);
    }

    public function assertValidHtml($uri = null)
    {
        if (is_null($uri)) {
            $uri = $this->getLocation();
        }
        Vps_Test_TestCase::assertValidHtml($uri);
    }
}