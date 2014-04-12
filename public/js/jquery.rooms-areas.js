;(function($){
    $.roomsAreas = $.roomsAreas || {};
    $.extend($.roomsAreas,{
        version: '0.1 alfa',
        getAccessor : function(obj, expr) {
            var ret = [];
            if(typeof expr === 'function') {
                return expr(obj);
            }
            ret = obj[expr];
            return ret;
	},
        _createRoomsDialog: function() {
            var self = this,
                options,
                buttons = new Object();
            buttons[self.roomsAreas.popupPhrases.btnApply] = function() {
                var roomsDialog = $(this);
                $.roomsAreas._applyRoomsDialog.apply(self, [roomsDialog]);
                roomsDialog.roomsdialog('close');
            };
            buttons[self.roomsAreas.popupPhrases.btnCancel] = function() {
                $(this).roomsdialog('close');
            };
            options = {
                modal: true,
                buttons: buttons,
                title: self.roomsAreas.popupPhrases.title,
                popupPhrases: self.roomsAreas.popupPhrases.dialog
            }
            self.roomsDialog = $('<div />');
            self.roomsDialog.roomsdialog(options);
        },
        _openRoomsDialog: function(roomsAreas) {
            if(!this.roomsDialog) {
                $.roomsAreas._createRoomsDialog.call(this);
            } else {
                this.roomsDialog.roomsdialog('open');
            }
            this.roomsDialog.roomsdialog('option', 'areas', roomsAreas);
        },
        _applyRoomsDialog: function(dialog) {
           var self = this,
               roomsAreas = dialog.roomsdialog('option', 'areas'),
               roomsCount = roomsAreas.length,
               roomsAreasStr = roomsAreas.join('/');
            self.val(roomsAreasStr);
            if(self.roomsAreas.accept && $.isFunction(self.roomsAreas.accept)){
                self.roomsAreas.accept(roomsAreas);
            }
        },
        _clearRoomsDialog: function() {
            var self = this;
            self.val('');
            if(self.roomsDialog) {
                self.roomsDialog.roomsdialog('option', 'areas', []);
            }
            if(self.roomsAreas.onclean && $.isFunction(self.roomsAreas.onclean)){
                self.roomsAreas.onclean();
            }
        }
    });

    $.widget('ui.roomsdialog', $.extend({}, $.ui.dialog.prototype, {
        options: $.extend({}, $.ui.dialog.prototype.options, {
            popupPhrases: {
                area: 'Area'
            }
        }),
        _create: function() {
            var self = this,
                options = self.options,
                gridTable,
                gridPager;
            $.ui.dialog.prototype._create.call(self);
            self.element.append(
                (gridTable = $('<table />')).attr({'id': 'rooms-grid'}).addClass('scroll'),
                (gridPager = $('<div />')).attr({'id': 'rooms-grid-pager'}).addClass('scroll')
            );
            self.grid = gridTable.jqGrid({
                colNames: [
                    'id',
                    options.popupPhrases.area
                ],
                colModel: [
                    {name: 'id', index: 'id', key: true, hidden: true},
                    {name: 'Area', index: 'Area', width: 150, sortable: false, editable: true,
                     editoptions: {dataInit: function(elem){$(elem).mask('99 кв.м', {placeholder: "0"});}}},

                ],
                datatype: 'local',
                viewrecords: true,
                pager: gridPager,
                width: 290,
                height: 200,
                pgbuttons: false,
                pginput: false,
                rowNum: 1000,
                dataProxy: function (ajaxOptions, oper_grid){
                    var status='success';
                    if($.isFunction(ajaxOptions.complete)){
                        ajaxOptions.complete(ajaxOptions.data, status);
                    }
                }
            });
            self.grid.navGrid('#' + gridPager.attr('id'),
                {add: true, edit: true, del: true, refresh: true, search: false},
                {useDataProxy: true, closeAfterEdit: true, reloadAfterSubmit: false},
                {useDataProxy: true, closeAfterAdd :true, reloadAfterSubmit: false, addedrow:'last'},
                {useDataProxy: true},
                {},
                {}
            );
        },
        option: function (key, value) {
            var self = this;
            if(key === 'areas') {
                if(value === undefined){
                    return self._getAreas();
                } else {
                    self._setAreas(value);
                    return this;
                }
            } else {
                return $.widget.prototype.option.call(self, key, value);
            }
	},
        _setAreas: function(areas) {
            var self = this;
            if(self.grid){
                self.grid.jqGrid('clearGridData');
                for(var i = 0, j = areas.length; i < j; i++) {
                    self.grid.addRowData(i, {Area: areas[i]}, 'last');
                }
            }
        },
        _getAreas: function() {
            var self = this,
                roomsAreas = [];
            if(self.grid) {
                var dataIds = self.grid.getDataIDs(),
                    rowData;
                for(var i = 0, j = dataIds.length; i < j; i++) {
                    rowData = self.grid.getRowData(dataIds[i]);
                    roomsAreas[i] = rowData.Area;
                }
            }
            return roomsAreas;
        },
        close: function() {
            var self = this;
            $.ui.dialog.prototype.close.call(self);
            if(self.options.afterDialogClose && $.isFunction(self.options.afterDialogClose)){
                self.options.afterDialogClose(self.element);
            }
            return self;
        }
    }));

    $.extend($.ui.roomsdialog, {
        version: '0.1 beta'
    });

    $.fn.attachRoomsDialog = function(pin) {
        if(this.roomsAreas) return;
        if(typeof pin == 'string') {
            var fn = $.addressattach.getAccessor($.fn.addressdialog,pin);
            if (!fn) {
                    throw ("addressdialog - No such method: " + pin);
            }
            var args = $.makeArray(arguments).slice(1);
	    return fn.apply(this,args);
        }
        return this.each(function(){
            var self = $(this),
                roomsAreas = $.extend({}, {
                    popupPhrases: {
                        title     : 'RoomsAreas',
                        btnApply  : 'ApplyCommand',
                        btnCancel : 'CancelCommand',
                        dialog: {
                            area: 'Area'
                        }
                    }
                }, pin),
                wrapper = $('<div />').css('position', 'relative'),
                btnEdit = $('<div />').addClass('ui-state-default ui-corner-all').append(
                            $('<span />').addClass('ui-icon ui-icon-pencil')
                       ),
                btnRemove = $('<div />').addClass('ui-state-default ui-corner-all').append(
                            $('<span />').addClass('ui-icon ui-icon-trash')
                       ),
                openDialog = function() {
                    var controlValue = self.val(),
                        args = [],
                        roomsAreas;
                    if(controlValue != '') {
                         roomsAreas = self.val().split('/');
                         args = [roomsAreas];
                    }
                    $.roomsAreas._openRoomsDialog.apply(self,args);
                };
            self.wrap(wrapper).css({'width': '96%','padding': '.4em'});
            var container = self.parent();
            container.append(btnEdit.hide(), btnRemove.hide())
                .bind('mouseover', function(){
                    btnEdit.show();
                    btnRemove.show();
                })
                .bind('mouseout', function() {
                    btnEdit.hide();
                    btnRemove.hide();
                });
            $(this).bind('focus',openDialog);

            btnRemove.css({'cursor': 'pointer', 'position': 'absolute', 'top':'3px','right':'1px'})
                .bind('mouseover', function() {
                    $(this).addClass('ui-state-hover');
                    $(this).addClass('ui-state-focus');
                })
                .bind('mousedown', function() {
                    $(this).addClass('ui-state-active');
                })
                .bind('mouseup', function() {
                    $(this).removeClass('ui-state-active');
                    $.roomsAreas._clearRoomsDialog.call(self);
                })
                .bind('mouseout', function() {
                        $(this).removeClass('ui-state-hover ui-state-focus ui-state-active');
                });
            btnEdit.css({'cursor': 'pointer', 'position': 'absolute', 'top':'3px','right':'20px'})
                .bind('mouseover', function() {
                    $(this).addClass('ui-state-hover');
                    $(this).addClass('ui-state-focus');
                })
                .bind('mousedown', function() {
                    $(this).addClass('ui-state-active');
                })
                .bind('mouseup', function() {
                    $(this).removeClass('ui-state-active');
                    openDialog();
                })
                .bind('mouseout', function() {
                     $(this).removeClass('ui-state-hover ui-state-focus ui-state-active');
                });
            self.roomsAreas = roomsAreas;
        });
    }
})(jQuery);