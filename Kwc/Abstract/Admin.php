<?php
class Kwc_Abstract_Admin extends Kwf_Component_Abstract_Admin
{
    protected function _getRow($componentId)
    {
        if (!Kwc_Abstract::hasSetting($this->_class, 'tablename')) return null;
        $tablename = Kwc_Abstract::getSetting($this->_class, 'tablename');
        if ($tablename) {
            $table = new $tablename(array('componentClass'=>$this->_class));
            return $table->find($componentId)->current();
        }
        return null;
    }

    protected function _getRows($componentId)
    {
        $tablename = Kwc_Abstract::getSetting($this->_class, 'tablename');
        if ($tablename) {
            $table = new $tablename(array('componentClass' => $this->_class));
            $where = array(
                'component_id = ?' => $componentId
            );
            return $table->fetchAll($where);
        }
        return array();
    }

    public function delete($componentId)
    {
        $row = $this->_getRow($componentId);
        if ($row) {
            $row->delete();
        }
    }

    public function getDuplicateProgressSteps($source)
    {
        $ret = 0;
        $s = array('inherit' => false, 'ignoreVisible'=>true);
        foreach ($source->getChildComponents($s) as $c) {
            $ret += $c->generator->getDuplicateProgressSteps($c);
        }
        return $ret;
    }

    public function duplicate($source, $target, Zend_ProgressBar $progressBar = null)
    {
        if ($model = $source->getComponent()->getOwnModel()) {
            $row = $model->getRow($source->dbId);
            if ($row) {
                $newRow = $row->duplicate(array(
                    'component_id' => $target->dbId
                ));
            }
        }

        $s = array('inherit' => false, 'ignoreVisible'=>true);
        foreach ($source->getChildComponents($s) as $c) {
            $c->generator->duplicateChild($c, $target, $progressBar);
        }
    }

    public function makeVisible($source)
    {
        foreach ($source->getChildComponents(array('inherit' => false, 'ignoreVisible'=>true)) as $c) {
            $c->generator->makeChildrenVisible($c);
        }
    }

    public function getCardForms()
    {
        $ret = array();
        $title = Kwc_Abstract::getSetting($this->_class, 'componentName');
        $title = str_replace('.', ' ', $title);
        $ret[] = array(
            'form' => Kwc_Abstract_Form::createComponentForm($this->_class, 'child'),
            'title' => $title,
        );
        return $ret;
    }

    public function getPagePropertiesForm()
    {
        return null;
    }
}
