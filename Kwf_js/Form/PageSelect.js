Kwf.Form.PageSelect = Ext.extend(Kwf.Form.AbstractSelect, {
    initComponent: function() {
        Kwf.Form.PageSelect.superclass.initComponent.call(this);
    },
    _getWindowItem: function()
    {
        if (!this._windowItem) {
            this._windowItem = new Kwf.Auto.TreePanel({
                controllerUrl: this.controllerUrl,
                listeners: {
                    click: function(node) {
                        var n = node;
                        var name = '';
                        while (n.parentNode.parentNode) {
                            if (name) name += ' - ';
                            name += n.attributes.text;
                            n = n.parentNode;
                        }
                        this._selectWin.value = {
                            id: node.id,
                            name: name
                        };
                    },
                    scope: this
                }
            });
        }
        return this._windowItem;
    }
});

Ext.reg('pageselect', Kwf.Form.PageSelect);
