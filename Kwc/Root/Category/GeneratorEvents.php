<?php
class Vpc_Root_Category_GeneratorEvents extends Vps_Component_Generator_Page_Events_Table
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Vps_Component_Event_Row_Updated',
            'callback' => 'onPageRowUpdate'
        );
        return $ret;
    }

    public function onPageFilenameChanged(Vps_Component_Event_Page_FilenameChanged $event)
    {
        if (is_numeric($event->dbId)) {
            foreach ($this->_getRecursiveChildIds($event->dbId, $this->_getGenerator()->getModel()) as $id) {
                $this->fireEvent(
                    new Vps_Component_Event_Page_RecursiveFilenameChanged($this->_class, $id)
                );
            }
        }
    }

    public function onPageRowUpdate(Vps_Component_Event_Row_Updated $event)
    {
        if (in_array('parent_id', $event->row->getDirtyColumns())) {
            foreach ($this->_getRecursiveChildIds($event->row->id, $event->row->getModel()) as $id) {
                $this->fireEvent(
                    new Vps_Component_Event_Page_ParentChanged($this->_class, $id)
                );
            }
        }
    }

    private function _getRecursiveChildIds($id, $model)
    {
        $ids = array();
        foreach ($model->getRows() as $row) {
            $ids[$row->parent_id][] = $row->id;
        }
        return $this->_rekGetRecursiveChildIds($id, $ids);
    }

    private function _rekGetRecursiveChildIds($id, $ids)
    {
        $ret = array($id);
        if (isset($ids[$id])) {
            foreach ($ids[$id] as $id) {
                $ret = array_merge($ret, $this->_rekGetRecursiveChildIds($id, $ids));
            }
        }
        return $ret;
    }
}