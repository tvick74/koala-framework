Vpc.Paragraphs.DataView = Ext.extend(Ext.DataView, {
    autoHeight: true,
    multiSelect: true,
    overClass: 'x-view-over',
    itemSelector: 'div.paragraph-wrap',
    width: 600,
    initComponent: function()
    {
        this.componentConfigs = {};

        this.addEvents('delete', 'edit', 'changeVisible', 'changePos', 'addParagraphMenuShow', 'addParagraph');
        this.tpl = new Ext.XTemplate(
            '<tpl for=".">',
                '<div class="paragraph-wrap<tpl if="!visible"> vpc-paragraph-invisible</tpl>" id="vpc-paragraphs-{id}" style="width:'+this.width+'px">',
                    '<div class="vpc-paragraphs-toolbar"></div>',
                    '<div class="webStandard vpc-paragraphs-preview">{preview}</div>',
                '</div>',
            '</tpl>',
            '<div class="x-clear"></div>'
        );

        Vpc.Paragraphs.DataView.superclass.initComponent.call(this);
    },

    onUpdate: function() {
        var ret = Vpc.Paragraphs.DataView.superclass.onUpdate.apply(this, arguments);
        this.updateToolbars();
        return ret;
    },
    onAdd: function() {
        var ret = Vpc.Paragraphs.DataView.superclass.onAdd.apply(this, arguments);
        this.updateToolbars();
        return ret;
    },
    refresh: function() {
        var ret = Vpc.Paragraphs.DataView.superclass.refresh.apply(this, arguments);
        this.updateToolbars();
        return ret;
    },
    updateToolbars: function()
    {
        Ext.each(Vps.contentReadyHandlers, function(i) {
            i.fn.call(i.scope | window);
        }, this);

        var nodes = this.getNodes();
        for (var i=0; i< nodes.length; i++) {
            var node = nodes[i];
            var tbCt = Ext.get(node).down('.vpc-paragraphs-toolbar');
            if (tbCt.down('.x-toolbar')) {
                continue;
            }
            var tb = new Ext.Toolbar({
                renderTo: tbCt
            });
            var record = this.getRecord(node);

            tb.add({
                //text: record.get('visible') ? trlVps('visible') : trlVps('invisible'),
                tooltip: trlVps('visibility'),
                scope: this,
                record: record,
                handler: function(btn) {
                    this.fireEvent('changeVisible', btn.record);
                },
                icon : '/assets/silkicons/'+(record.get('visible') ? 'tick' : 'cross') + '.png',
                cls  : 'x-btn-icon'
            });

            var posCombo = new Vps.Form.ComboBox({
                listClass: 'vpc-paragraphs-pos-list',
                tpl: '<tpl for=".">' +
                    '<div class="x-combo-list-item<tpl if="visible"> visible</tpl><tpl if="!visible"> invisible</tpl>">'+
                        '{pos} - {component_name}'+
                    '</div>'+
                    '</tpl>',
                displayField: 'pos',
                valueField: 'pos',
                store: this.store,
                editable: false,
                width: 50,
                triggerAction: 'all',
                mode: 'local',
                record: record,
                listWidth: 100,
                listeners: {
                    scope: this,
                    changevalue: function(v, combo) {
                        if (v && combo.record.get('pos') != v) {
                            this.fireEvent('changePos', combo.record, v);
                            combo.blur();
                            combo.hasFocus = false; //ansonsten wird die list angezeigt nachdem daten geladen wurden
                        }
                    }
                }
            });
            posCombo.setValue(record.get('pos'));

            tb.add(posCombo);
            tb.add({
                tooltip: trlVps('delete'),
                scope: this,
                record: record,
                handler: function(btn) {
                    this.fireEvent('delete', btn.record);
                },
                icon : '/assets/silkicons/bin.png',
                cls  : 'x-btn-icon'
            });
            tb.add('-');
            if (record.get('edit_components').length == 1) {
                tb.add({
                    text: trlVps('edit'),
                    scope: this,
                    record: record,
                    handler: function(btn) {
                        this.fireEvent('edit', btn.record, Vps.clone(record.get('edit_components')[0]));
                    },
                    icon : '/assets/silkicons/application_edit.png',
                    cls  : 'x-btn-text-icon'
                });
            } else if (record.get('edit_components').length > 1) {
                var menu = [];
                record.get('edit_components').forEach(function(ec) {
                    var cfg = this.componentConfigs[ec.componentClass+'-'+ec.type];
                    menu.push({
                        text: cfg.title,
                        icon: cfg.icon,
                        scope: this,
                        record: record,
                        editComponent: ec,
                        handler: function(menu) {
                            this.fireEvent('edit', menu.record, Vps.clone(menu.editComponent));
                        }
                    });
                }, this);
                tb.add({
                    text: trlVps('edit'),
                    menu: menu,
                    icon : '/assets/silkicons/application_edit.png',
                    cls  : 'x-btn-text-icon'
                });
                tb.add('-');
            }
            tb.add(new Vpc.Paragraphs.AddParagraphButton({
                record: record,
                components: this.components,
                componentIcons: this.componentIcons,
                listeners: {
                    scope: this,
                    menushow: function(btn) {
                        this.fireEvent('addParagraphMenuShow', btn.record);
                    },
                    addParagraph: function(component) {
                        this.fireEvent('addParagraph', component);
                    }
                }
            }));
            tb.add('->');
            tb.add(record.get('component_name'));
            tb.add('<img src="'+record.get('component_icon')+'">');
        }
    }
});