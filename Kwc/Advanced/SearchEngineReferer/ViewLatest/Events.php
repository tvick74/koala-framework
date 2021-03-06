<?php
class Kwc_Advanced_SearchEngineReferer_ViewLatest_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => 'Kwc_Advanced_SearchEngineReferer_Model',
            'event' => 'Kwf_Component_Event_Row_Inserted',
            'callback' => 'onRowInsert'
        );
        return $ret;
    }

    public function onRowInsert(Kwf_Component_Event_Row_Inserted $event)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
            $this->_class, $event->row->component_id
        ));
        $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
            $this->_class, $event->row->component_id
        ));
    }
}
