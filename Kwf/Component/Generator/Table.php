<?php
class Kwf_Component_Generator_Table extends Kwf_Component_Generator_Abstract
{
    protected $_loadTableFromComponent = true;

    protected $_idSeparator = '-'; //um in StaticTable _ verwenden zu können
    protected $_idColumn = 'id';
    protected $_hasNumericIds = true;
    protected $_eventsClass = 'Kwf_Component_Generator_Events_Table';

    final public function getFormattedSelect($parentData)
    {
        $ret = $this->select($parentData);
        $ret = $this->_formatSelect($parentData, $ret);
        return $ret;
    }

    public function joinWithChildGenerator($select, $childGenerator)
    {
        $table = $this->_getModel()->getTable()->info('name');
        $childTable = $childGenerator->_getModel()->getTable()->info('name');
        $select->setIntegrityCheck(false);
        $select->join($childTable, "{$table}.cache_child_component_id={$childTable}.component_id", array());
        return $select;
    }

    public function joinWithParentGenerator($select, $parentGenerator, $grandParentData = null)
    {
        $table = $this->_getModel()->getTable()->info('name');
        $parentTable = $parentGenerator->_getModel()->getTable()->info('name');
        $select->setIntegrityCheck(false);
        $select->join($parentTable, "{$parentTable}.cache_child_component_id={$table}.component_id", array());
        if ($grandParentData) {
            $parentSelect = $parentGenerator->select($grandParentData);
            $parentSelect = $parentGenerator->_formatSelect($grandParentData, $parentSelect);
            $where = $parentSelect->getPart(Kwf_Component_Select::WHERE_EQUALS);
            if ($where) {
                foreach ($parentSelect->getPart(Kwf_Component_Select::WHERE_EQUALS) as $key => $value) {
                    if (!strpos($key, '.')) { $key = $parentTable . '.' . $key; }
                    $select->where("$key=?", $value);
                }
            }
            $where = $parentSelect->getPart(Kwf_Component_Select::WHERE);
            if ($where) {
                foreach ($where as $key => $value) {
                    if (!strpos($key, '.')) { $key = $parentTable . '.' . $key; }
                    $select->where($key, $value);
                }
            }
        }
        return $select;
    }

    public final function getChildIds($parentData, $select = array())
    {
        $select = $this->_formatSelect($parentData, $select);
        return $this->_fetchIds($parentData, $select);
    }

    protected function _fetchIds($parentData, $select)
    {
        return $this->_getModel()->getIds($select);
    }

    protected function _fetchRows($parentData, $select)
    {
        return $this->_getModel()->getRows($select);
    }

    protected function _fetchCountChildData($parentData, $select)
    {
        if ($select) {
            return $this->_getModel()->countRows($select);
        } else {
            return 0;
        }
    }

    public function getChildData($parentData, $select = array())
    {
        Kwf_Benchmark::count('GenTable::getChildData');
        if (is_array($select)) $select = new Kwf_Component_Select($select);
        $ret = array();
        if (!$parentData && ($p = $select->getPart(Kwf_Component_Select::WHERE_CHILD_OF_SAME_PAGE))
                && !$this->_getModel()->hasColumn('component_id')) {
            $parentDatas = $p->getRecursiveChildComponents(array(
                'componentClass' => $this->_class
            ));
        } else {
            $parentDatas = array($parentData /* kann auch null sein*/);
        }

        foreach ($parentDatas as $parentData) {
            $s = $this->_formatSelect($parentData, clone $select);
            $rows = array();
            if ($s) {
                $rows = $this->_fetchRows($parentData, $s);
            }

            foreach ($rows as $row) {
                $currentPd = $parentData;
                if (!$currentPd) {
                    $currentPd = $this->_getParentDataByRow($row, $s);
                }
                if (!is_array($currentPd)) {
                    if ($currentPd) {
                        $currentPds = array($currentPd);
                    } else {
                        $currentPds = array();
                    }
                } else {
                    $currentPds = $currentPd;
                }
                foreach ($currentPds as $currentPd) {
                    $data = $this->_createData($currentPd, $row, $s);
                    if ($data) {
                        $ret[] = $data;
                    }
                }
            }
        }
        return $ret;
    }

    public final function countChildData($parentData, $select = array())
    {
        $select = $this->_formatSelect($parentData, $select);
        return $this->_fetchCountChildData($parentData, $select);
    }

    protected function _getParentDataByRow($row, $select)
    {
        if (isset($row->component_id) && $row->component_id) {
            $constraints = array('componentClass'=>$this->_class);
            if ($select->hasPart(Kwf_Component_Select::WHERE_SUBROOT)) {
                $constraints['subroot'] = $select->getPart(Kwf_Component_Select::WHERE_SUBROOT);
            }
            if ($select->hasPart(Kwf_Component_Select::IGNORE_VISIBLE)) {
                $constraints['ignoreVisible'] = $select->getPart(Kwf_Component_Select::IGNORE_VISIBLE);
            }
            $ret = Kwf_Component_Data_Root::getInstance()
                ->getComponentsByDbId($row->component_id, $constraints);

            //streng genommen nicht on same page sondern children of und auf same page
            //siehe Kwf_Component_Generator_RecursiveTable2_RecursiveTest
            if ($p = $select->getPart(Kwf_Component_Select::WHERE_CHILD_OF_SAME_PAGE)) {
                foreach ($ret as $k=>$i) {
                    $found = false;
                    while ($i) {
                        if ($p->componentId == $i->componentId) {
                            $found = true;
                        }
                        if ($i->isPage) break; //bei page aufhoeren
                        $i = $i->parent;
                    }
                    if (!$found) {
                        unset($ret[$k]); //kein gemeinsamer parent vorhanden
                    }
                }
            }
        } else {
            $components = Kwf_Component_Data_Root::getInstance()->getComponentsBySameClass($this->_class, array('ignoreVisible' => true));
            if (count($components) == 1) {
                return $components[0];
            } else if (count($components) == 0) {
                return null;
            }
            throw new Kwf_Exception("Can't find parentData for row, implement _getParentDataByRow for the '{$this->_class}' Generator");
        }
        return $ret;
    }

    protected function _formatSelectId(Kwf_Component_Select $select)
    {
        if ($select->hasPart(Kwf_Model_Select::WHERE_ID)) {
            $id = $select->getPart(Kwf_Model_Select::WHERE_ID);
            $separator = substr($id, 0, 1);
            if (in_array($separator, array('_', '-'))) {
                $id = substr($id, 1);
                if ($separator != $this->_idSeparator || ($this->_hasNumericIds && !is_numeric($id))) {
                    return null;
                }
                $select->whereEquals($this->_idColumn, $id);
                $select->unsetPart(Kwf_Model_Select::WHERE_ID);
            }
        }
        return $select;
    }

    protected function _formatSelect($parentData, $select)
    {
        $select = parent::_formatSelect($parentData, $select);
        if (is_null($select)) return null;

        if ($this->_getModel()->hasColumn('component_id') && $this->_getModel()->getPrimaryKey() != 'component_id') {
            if ($parentData) {
                $select->whereEquals('component_id', $parentData->dbId);
            } else if ($p = $select->getPart(Kwf_Component_Select::WHERE_CHILD_OF_SAME_PAGE)) {
                $p = $p->getPageOrRoot();
                $select->where(new Kwf_Model_Select_Expr_Or(array(
                    new Kwf_Model_Select_Expr_StartsWith('component_id', $p->dbId.'-'),
                    new Kwf_Model_Select_Expr_Equal('component_id', $p->dbId),
                )));
            }
        }

        $select = $this->_formatSelectId($select);
        if (is_null($select)) return null;

        if (in_array('pos', $this->_getModel()->getOwnColumns()) && !$select->hasPart(Kwf_Component_Select::ORDER)) {
            $select->order("pos");
        }

        static $showInvisible;
        if (is_null($showInvisible)) {
            $showInvisible = Kwf_Config::getValue('showInvisible');
        }
        if (!$select->getPart(Kwf_Component_Select::IGNORE_VISIBLE)
            && $this->_getModel()->hasColumn('visible') && !$showInvisible) {
            $select->whereEquals("visible", 1);
        }

        if ($select->hasPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES)) {
            $selectClasses = $select->getPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES);
            if (!$selectClasses) return null;
            $childClasses = $this->_settings['component'];
            $keys = array();
            foreach ($selectClasses as $selectClass) {
                $keys = array_merge($keys, array_keys($childClasses, $selectClass));
            }

            if (!$keys) return null;

            if (count($childClasses)==1) {
                if (!in_array(key($childClasses), $keys)) {
                    return null;
                }
            } else {
                if (!$this->_getModel()->hasColumn('component')) {
                    throw new Kwf_Exception("Column component does not exist for a generator in '$this->_class'");
                }
                $select->whereEquals('component', $keys);
            }
        }
        return $select;
    }

    protected function _getComponentIdFromRow($parentData, $row)
    {
        return $parentData->componentId . $this->_idSeparator . $this->_getIdFromRow($row);
    }

    protected function _formatConfig($parentData, $row)
    {
        $componentId = $this->_getComponentIdFromRow($parentData, $row);
        $dbId = $this->_getIdFromRow($row);
        if (isset($this->_settings['dbIdShortcut'])) {
            $dbId = $this->_settings['dbIdShortcut'] . $dbId;
        } else {
            $dbId = $parentData->dbId . $this->_idSeparator . $dbId;
        }

        if (count($this->_settings['component']) > 1 && isset($row->component)) {
            $componentKey = $row->component;
        } else {
            $componentKey = null;
        }
        $componentClass = $this->_getChildComponentClass($componentKey, $parentData);

        $data = array(
            'componentId' => $componentId,
            'dbId' => $dbId,
            'componentClass' => $componentClass,
            'parent' => $parentData,
            'row' => $row,
            'isPage' => false,
            'isPseudoPage' => false
        );
        if ($this->_getModel()->hasColumn('visible') && !$row->visible) {
            $data['invisible'] = true;
        }
        return $data;
    }

    /**
     * wird in Link-Generator überschrieben
     **/
    protected function _getIdFromRow($row)
    {
        return $row->{$this->_idColumn};
    }

    public function hasMultipleComponents()
    {
        return (count($this->_settings['component']) > 1);
    }

    public function getDuplicateProgressSteps($source)
    {
        return 1; //fixed, as we don't go any deeper (would be too expensive)
    }

    public function duplicateChild($source, $parentTarget, Zend_ProgressBar $progressBar = null)
    {
        if ($progressBar) $progressBar->next();
        $progressBar = null; //stop here, as getDuplicateProgressSteps doesn't go any deeper

        if ($source->generator !== $this) {
            throw new Kwf_Exception("you must call this only with the correct source");
        }

        $data = array();
        if ($this->_getModel()->hasColumn('component_id')) { //only duplicate rows that are scoped to source component (using component_id)
            $data['component_id'] = $parentTarget->dbId;
            $newRow = $source->row->duplicate($data);
        } else {
            $newRow = $source->row;
        }

        $id = $this->_idSeparator . $newRow->{$this->_getModel()->getPrimaryKey()};
        $target = $parentTarget->getChildComponent(array('id'=>$id, 'ignoreVisible'=>true));
        if (!$target) {
            return null;
        }
        Kwc_Admin::getInstance($source->componentClass)->duplicate($source, $target, $progressBar);
        return $target;
    }

    public function makeChildrenVisible($source)
    {
        if ($source->generator !== $this) {
            throw new Kwf_Exception("you must call this only with the correct source");
        }

        $data = array();
        if ($this->_getModel()->hasColumn('visible')) {
            if (!$source->row->visible) {
                $source->row->visible = 1;
                $source->row->save();
            }
        }
        Kwc_Admin::getInstance($source->componentClass)->makeVisible($source);
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['table'] = true;
        return $ret;
    }

    // TODO Cache
    public function getStaticCacheVarsForMenu()
    {
        $ret = array();
        $ret[] = array(
            'model' => $this->getModel()
        );
        return $ret;
    }
}
