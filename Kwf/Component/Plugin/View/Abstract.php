<?php
abstract class Kwf_Component_Plugin_View_Abstract extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_View
{
    protected $_componentId;
    static private $_instances = array();

    public function __construct($componentId)
    {
        $this->_componentId = $componentId;
        parent::__construct($componentId);
    }

    public function processMailOutput($output, Kwc_Mail_Recipient_Interface $recipient = null)
    {
        return $this->processOutput($output);
    }

    public function getExecutionPoint()
    {
        return Kwf_Component_Plugin_Interface_View::EXECUTE_BEFORE;
    }

    public function getInstance($pluginClass, $componentId)
    {
        if (!isset(self::$_instances[$pluginClass][$componentId])) {
            self::$_instances[$pluginClass][$componentId] = new $pluginClass($componentId);
        }
        return self::$_instances[$pluginClass][$componentId];
    }
}
