Ext.namespace('Vpc.Directories.Item.Directory');
Vpc.Directories.Item.Directory.Panel = Ext.extend(Vps.Auto.GridPanel,
{
    initComponent: function() {
        if (this.componentPlugins) {
            this.plugins = [ ];
            this.componentPlugins.each(function(v) {
                var pluginClass = eval(v);
                this.plugins.push(new pluginClass());
            }, this);
        }

        this.columnsConfig = {
            edit: {
                clickHandler: function(grid, rowIndex) {
                    var row = grid.getStore().getAt(rowIndex);
                    this.fireEditComponent(row);
                },
                scope: this
            }
        };

        Vpc.Directories.Item.Directory.Panel.superclass.initComponent.call(this);
    },

    fireEditComponent : function(row)
    {
        this.fireEvent('editcomponent', {
            componentClass: this.contentClass,
            componentId: this.idTemplate.replace('{0}', row.data.id),
            text: trlVps('Details')
        });
    }
});

Ext.reg('vpc.directories.item.directory', Vpc.Directories.Item.Directory.Panel);