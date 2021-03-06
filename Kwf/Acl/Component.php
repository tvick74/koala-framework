<?php
class Kwf_Acl_Component extends Kwf_Acl
{
    public function __construct()
    {
        parent::__construct();

        $this->addRole(new Kwf_Acl_Role('superuser', trlKwf('Superuser')));
        $this->add(new Kwf_Acl_Resource_EditRole('edit_role_superuser', 'superuser'), 'edit_role');

        $this->add(new Zend_Acl_Resource('kwf_debug_class-tree'), 'kwf_debug');
        $this->add(new Zend_Acl_Resource('kwf_component_web'));
        $this->add(new Zend_Acl_Resource('kwf_component_media'));
        $this->add(new Zend_Acl_Resource('kwf_component_index'));
        $this->add(new Zend_Acl_Resource('kwf_component_benchmark'));
        $this->add(new Zend_Acl_Resource('kwf_component_show-component'));
        $this->add(new Kwf_Acl_Resource_MenuUrl('kwf_component_pages',
            array('text'=>trlKwf('Page tree'), 'icon'=>'application_side_tree.png'),
            '/admin/component/pages'));
            $this->add(new Zend_Acl_Resource('kwf_component_page'), 'kwf_component_pages');
            $this->add(new Zend_Acl_Resource('kwf_component_components'),
                                'kwf_component_pages'); // für /component/show
            $this->add(new Zend_Acl_Resource('kwf_component'),
                                'kwf_component_pages'); // für /component/edit

        $this->add(new Zend_Acl_Resource('kwf_component_root')); //Komponenten können hier resourcen anhängen

        $this->allow(null, 'kwf_component_web');
        $this->allow(null, 'kwf_component_media');
        $this->allow(null, 'kwf_component_index');
        $this->allow('admin', 'kwf_component');
        $this->allow('superuser', 'kwf_component');
        $this->allow('superuser', 'edit_role_superuser');

        $this->allow('admin', 'kwf_component_show-component');
        $this->allow('admin', 'kwf_component_pages');
        $this->allow('admin', 'kwf_component_benchmark');
        $this->allow('superuser', 'kwf_component_show-component');
        $this->allow('superuser', 'kwf_component_pages');

        $this->allow(null, 'kwf_component_root');

        $this->deny('guest', 'kwf_component_index');
        $this->deny('guest', 'kwf_component_pages');

        // Kwf_Component_Acl nicht vergessen für Komponentenrechte!
    }
}
