<?php
class Kwc_Form_Dynamic_Form_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['paragraphs'] = $this->getData()->parent->getChildComponent('-paragraphs');
        return $ret;
    }

    protected function _initForm()
    {
        $this->_form = new Kwf_Form('form');
        $referenceMap = array();
        $dependentModels = array();
        foreach ($this->getData()->parent->getChildComponent('-paragraphs')->getRecursiveChildComponents(array('flags'=>array('formField'=>true))) as $c) {
            $f = $c->getComponent()->getFormField();
            $this->_form->fields->add($f);
            if ($f instanceof Kwf_Form_Field_File) {
                $referenceMap[$f->getName()] = array(
                    'refModelClass' => 'Kwf_Uploads_Model',
                    'column' => $f->getName()
                );
            } else if ($f instanceof Kwf_Form_Field_MultiCheckbox) {
                $dependentModels[$f->getName()] = 'Kwc_Form_Field_MultiCheckbox_DataToValuesModel';
            }
        }
        $this->_form->setModel($this->_createModel(array('referenceMap'=>$referenceMap,
                                                         'dependentModels'=>$dependentModels)));
    }

    protected function _createModel(array $config)
    {
        $config['componentClass'] = get_class($this);
        $config['mailerClass'] = 'Kwf_Mail';
        return new Kwc_Form_Dynamic_Form_MailModel($config);
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->component_id = $this->getData()->parent->dbId;
    }

    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_afterInsert($row);

        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = Kwf_Registry::get('config')->server->domain;
        }
        $row->setFrom("noreply@$host");
        $settings = $this->getData()->parent->getComponent()->getRow(); //TODO interface dafür machen, nicht auf row direkt zugreifen
        $row->addTo($settings->recipient);
        $row->setSubject($settings->subject);

        $msg = '';
        foreach ($this->getData()->parent->getChildComponent('-paragraphs')->getRecursiveChildComponents(array('flags'=>array('formField'=>true))) as $c) {
            $f = $c->getComponent()->getFormField();
            if ($f->getName() && $f->getFieldLabel()) {
                if ($f instanceof Kwf_Form_Field_File) {
                    $uploadRow = $row->getParentRow($f->getName());
                    if ($uploadRow) {
                        $row->addAttachment($uploadRow);
                        $msg .= $f->getFieldLabel().": {$uploadRow->filename}.{$uploadRow->extension} ".trlKwf('attached')."\n";
                    }
                } else if ($f instanceof Kwf_Form_Field_Checkbox) {
                    if ($row->{$f->getName()}) {
                        $msg .= $f->getFieldLabel().': '.$this->getData()->trlKwf('on')."\n";
                    } else {
                        $msg .= $f->getFieldLabel().': '.$this->getData()->trlKwf('off')."\n";
                    }
                } else if ($f instanceof Kwf_Form_Field_MultiCheckbox) {
                    $values = array();
                    foreach ($row->getChildRows($f->getName()) as $r) {
                        if (substr($r->value_id, 0, strlen($f->getName())) == $f->getName()) {
                            $values[] = $r->value_id;
                        }
                    }
                    $valuesText = array();
                    foreach ($f->getValues() as $k=>$i) {
                        if (in_array($k, $values)) {
                            $valuesText[] = $i;
                        }
                    }
                    $msg .= $f->getFieldLabel().': '.implode(', ', $valuesText)."\n";

                } else {
                    $msg .= $f->getFieldLabel().': '.$row->{$f->getName()}."\n";
                }
            }
        }

        $row->sent_mail_content_text = $msg;

        $row->sendMail(); //manuell aufrufen weils beim speichern nicht automatisch gemacht wird (da da der content nocht nicht vorhanden ist)
    }
}
