<?php
class Vps_Util_Model_Feed_Entries extends Vps_Model_Abstract
    implements Vps_Model_RowsSubModel_Interface
{
    protected $_rowsetClass = 'Vps_Model_Rowset_ParentRow';
    protected $_rowClass = 'Vps_Util_Model_Feed_Row_Entry';

    public function getOwnColumns()
    {
        return array('title', 'link', 'description', 'date');
    }

    public function getPrimaryKey()
    {
        return false;
    }
    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        throw new Vps_Exception_NotYetImplemented();
    }

    public function getRowsByParentRow(Vps_Model_Row_Interface $parentRow, $select = array())
    {
        $select = $this->select($select);
        if ($select->getParts()) {
            throw new Vps_Exception_NotYetImplemented('Custom select is not yet implemented');
        }
        if (!($parentRow instanceof Vps_Util_Model_Feed_Row_Feed)) {
            throw new Vps_Exception('Only possible with feed row');
        }
        return $parentRow->getEntries();
    }

    //"darf" nur von Vps_Util_Model_Feed_Row_Feed aufgerufen werden!
    public function _getFeedEntries($parentRow, $xml)
    {
        $pId = $parentRow->getInternalId();
        $this->_data[$pId] = array();

        if ($parentRow->format == Vps_Util_Model_Feed_Row_Feed::FORMAT_RSS) {
            if (in_array('http://purl.org/rss/1.0/', $xml->getNamespaces(true))) {
                $xml->registerXPathNamespace('rss', 'http://purl.org/rss/1.0/');
                foreach ($xml->xpath('//rss:item') as $item) {
                    $this->_data[$pId][] = $item;
                }
            } else {
                foreach ($xml->channel->item as $item) {
                    $this->_data[$pId][] = $item;
                }
            }
        } else {
            foreach ($xml->entry as $item) {
                $this->_data[$pId][] = $item;
            }
        }

        return new $this->_rowsetClass(array(
            'model' => $this,
            'dataKeys' => array_keys($this->_data[$pId]),
            'parentRow' => $parentRow
        ));
    }
    public function getRowByDataKey($key, $parentRow)
    {
        $pId = $parentRow->getInternalId();
        if (!isset($this->_rows[$pId][$key])) {
            $this->_rows[$pId][$key] = new $this->_rowClass(array(
                'xml' => $this->_data[$pId][$key],
                'feed' => $parentRow,
                'model' => $this
            ));
        }
        return $this->_rows[$pId][$key];
    }
}