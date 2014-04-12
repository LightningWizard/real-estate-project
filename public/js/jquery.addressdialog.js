(function($){
    $.addressattach = $.addressattach || {};
    $.extend($.addressattach,{
        version: '0.1 alfa',
        getAccessor : function(obj, expr) {
            var ret = [];
            if(typeof expr === 'function') {
                return expr(obj);
            }
            ret = obj[expr];
            return ret;
	},
        _openAddressDialog: function() {
            var self = this;
            $.addressattach._setAddressDialogValues.call(self);
            self.addressDialog.addressdialog('openDialog');
        },
        _setAddressDialogValues: function() {
            var self = this,
                dialog = self.addressDialog,
                controlFields = self.addressAttachment.controlFields,
                values = {};
            for(var field in controlFields) {
                values[field] = controlFields[field].val();
            }
            dialog.addressdialog('option', 'values', values);
        },
        _createAddressDialog: function() {
            var self = this,
                buttons = new Object();
            buttons[self.addressAttachment.ApplyCommandText] = function(){
                $.addressattach._applyAddressDialog.call(self);
                self.addressDialog.addressdialog('close');
            };
            buttons[self.addressAttachment.CancelCommandText] = function(){
                $(this).addressdialog('close');
            };
            self.addressDialog = $('<div />').addressdialog(
                $.extend({},{
                    modal    : true,
                    autoOpen : false,
                    title    : self.addressAttachment.TitleText,
                    width    : self.addressAttachment.width || 400,
                    height   : self.addressAttachment.height || 'auto',
                    buttons  : buttons
                },{
                    url                      : self.addressAttachment.url,
                    SettlementText           : self.addressAttachment.SettlementText,
                    StreetText               : self.addressAttachment.StreetText,
                    StreetTypeText           : self.addressAttachment.StreetTypeText,
                    BuildingText             : self.addressAttachment.BuildingText,
                    FlatText                 : self.addressAttachment.FlatText,
                    TitleText                : self.addressAttachment.TitleText
                })
            );
        },
        _applyAddressDialog: function() {
            var self = this,
                dialog = self.addressDialog,
                values = dialog.addressdialog('option', 'values');
            for(var field in self.addressAttachment.controlFields) {
                var controlField = self.addressAttachment.controlFields[field];
                controlField.val(values[field] != undefined ? values[field] : '');
            }
        },
        _clear: function(){
            var self = this,
                controlFields = self.addressAttachment.controlFields;
            for(var i in controlFields) {
                var itemControlField = controlFields[i];
                itemControlField.val('');
            }
            self.val('');
        }
    });

    $.widget('ui.addressdialog', $.extend({}, $.ui.dialog.prototype, {
        options: $.extend({}, $.ui.dialog.prototype.options, {
            initialValues: {
                settlement : 1,
                street: null,
                building: null,
                flat: null
            }
        }),
        _create: function() {
            var self = this,
                options = self.options,
                autoOpen = options.autoOpen;
            options.autoOpen = false;
            $.ui.dialog.prototype._create.call(self);
            options.autoOpen = autoOpen;

            self.components = {};
            self.controls = {};
            self.initialValues = options.initialValues;
            self.values = {
                settlement: null,
                street: null,
                building: null,
                flat: null
            };

            self.element.append(
                $('<form/>').addClass('ui-address-form').append(
                    (self.components.settlement = $('<div />')).append(
                        $('<label />').attr({'for': 'af-settlement'}).text(options.SettlementText),
                        (self.controls.settlement = $('<select />')).attr({type: 'text', id: 'af-settlement'})
                            .addClass('text ui-widget-content ui-corner-all')
                    ),
                    (self.components.street = $('<div />')).append(
                        $('<label />').attr({'for': 'af-street'}).text(options.StreetText),
                        $('<div />').append(
                            (self.controls.streettype = $('<select />')).attr({type: 'text', id: 'af-streettype'})
                                .addClass('text ui-widget-content ui-corner-all'),
                            (self.controls.street = $('<select />')).attr({type: 'text', id: 'af-street'})
                                .addClass('text ui-widget-content ui-corner-all')
                        )
                    ),
                    (self.components.building = $('<div />')).append(
                        $('<label />').attr('for', 'af-building').text(options.BuildingText),
                        (self.controls.building = $('<input />')).attr({type: 'text', id: 'af-bulding'})
                            .addClass('text ui-widget-content ui-corner-all')
                    ),
                    (self.components.flat = $('<div />')).append(
                        $('<label />').attr('for', 'af-flat').text(options.FlatText),
                        (self.controls.flat = $('<input />')).attr({'type': 'text', 'id': 'af-flat'})
                            .addClass('text ui-widget-content ui-corner-all')
                    )
                )
            );
            for (var i in self.controls) {
                switch (i) {
                    case 'streettype':
                        self.controls[i].css({'width':'20%'});
                        break;
                    case 'street':
                        self.controls[i].css({'width':'79%'});
                        break;
                    default:
                        self.controls[i].css({'width':'99%'});
                }
            }
            for (var j in self.components) {
                var itemComponent = self.components[j];
                itemComponent.css({'marginBottom':'5px'});
                if (j != 'settlement' && j != 'street') {
                    itemComponent.hide();
                }
            }

            self.controls.address = $('<textarea >').css({'height':'2.4em','width':'99%'});
            self.element.append($('<hr>').css({'color': '#a6c9e2'}), self.controls.address);

            self.loadIndicator = $('<div />')
                .addClass('ui-dialog-loader');
            self.controls.settlement.change(function(){
                self.onSettlementChange.call(self);
            });
            self.controls.street.change(function(){
                self.onStreetChange.call(self);
            });
            self.controls.building.change(function(){
                self.onBuildingChange.call(self);
            });
            self.controls.building.keyup(function() {
                if (self.controls.building.timer) {
                    clearTimeout(self.controls.building.timer);
                    delete self.controls.building.timer;
                }
                self.controls.building.timer = setTimeout(function() {self.onBuildingChange.call(self);}, 500);
            });
            self.controls.flat.change(function(){
                self.onFlatChange.call(self);
            });
            self.controls.flat.keyup(function() {
                if (self.controls.flat.timer) {
                    clearTimeout(self.controls.flat.timer);
                    delete self.controls.flat.timer;
                }
                self.controls.building.timer = setTimeout(function() {self.onFlatChange.call(self);}, 500);
            });
            self.loadStreetTypes();
        },
        loadSettlements: function() {
            var self = this;
            if(self.settlements){
                self.onLoadSettlementsSuccess(self.settlements);
                return;
            }
            $.ajax({
                url: self.options.url + '/settlements',
                type: 'POST',
                dataType: 'json',
                async: false,
                beforeSend: function() {
                    self.showLoaderIcon.call(self);
                },
                complete: function() {
                    self.hideLoaderIcon.call(self);
                },
                success: function(data)  {
                    self.onLoadSettlementsSuccess.call(self, data);
                }
            });
        },
        onLoadSettlementsSuccess: function(data){
            var self = this,
                firstChar = null,
                optgroup = null,
                dataItem = null;
            self.settlements = new Object();
            self.controls.settlement.children().remove();
            for(var i in data) {
                dataItem = data[i];
                var itemFirstChar = dataItem.title.substr(0,1);
                if(firstChar == null || firstChar != itemFirstChar) {
                    firstChar = itemFirstChar;
                    optgroup = $('<optgroup />').attr('label', firstChar);
                    self.controls.settlement.append(optgroup);
                }
                var option = $('<option/>');
                option.val(dataItem.id).text(dataItem.title);
                if(dataItem.id == self.initialValues.settlement) {
                    option.attr('selected', 'selected');
                }
                self.settlements['adrs' + dataItem.id] = {
                    id: dataItem.id,
                    title: dataItem.title
                };
                optgroup.append(option);
            }
            self.initialValues.settlement = null;
            self.onSettlementChange();
        },
        onSettlementChange: function() {
            var self = this,
                settlement = self.controls.settlement.val()
            self.values.settlement = settlement;
            with(self.values) {
                building = null;
                flat = null;
            }
            with(self.components) {
                building.add(flat).hide();
            }
            with(self.controls) {
                building.add(flat).val('');
            }
            self.createFormatedAddress();
            self.loadStreetsForSettlement();
        },
        loadStreetsForSettlement: function() {
            var self = this;
            if(!self.values.settlement){
                return;
            }
            if(self.streets && self.streets.settlement == self.values.settlement) {
                self.onLoadStreetsForSettlementSuccess(self.streets.data);
                return;
            }
            self.controls.street.children().remove();
            $.ajax({
                url: self.options.url + '/settlement-streets',
                type: 'POST',
                dataType: 'json',
                data: {
                    settlement: self.values.settlement
                },
                async: true,
                beforeSend: function() {
                    self.showLoaderIcon.call(self);
                },
                complete: function() {
                    self.hideLoaderIcon.call(self);
                },
                success: function(data)  {
                    self.onLoadStreetsForSettlementSuccess.call(self, data);
                }
            });
        },
        onLoadStreetsForSettlementSuccess: function(data){
            var self = this,
                firstChar = null,
                optgroup = null;
                self.controls.street.append(
                    $('<optgroup />').attr('label','').append(
                        $('<option/>').val('').text('')
                    )
                );
                self.streets = {
                    settlement: self.values.settlement,
                    data: {}
                };
            for(var i in data) {
                var dataItem = data[i],
                    itemFirstChar = dataItem.title.substr(0,1);
                if(firstChar != itemFirstChar) {
                    firstChar = itemFirstChar;
                    optgroup = $('<optgroup />').attr('label', firstChar);
                    self.controls.street.append(optgroup);
                }
                var option = $('<option/>');
                option.val(dataItem.id).text(dataItem.title);
                if(dataItem.id == self.initialValues.street) {
                    option.attr('selected', 'selected');
                }
                optgroup.append(option);
                self.streets.data['adrs' + dataItem.id] = {
                    id:    dataItem.id,
                    title: dataItem.title,
                    type:  dataItem.type
                };
            }
            self.initialValues.street = null;
            self.onStreetChange();
        },
        onStreetChange: function() {
            var self = this,
                street = $.trim(self.controls.street.val());

            self.values.street = street;
            with(self.values) {
                building = null;
                flat = null;
            }
            with (self.controls) {
                building.add(flat).val('');
            }

            if(self.values.street) {
                self.components.building.show();
                self.components.flat.show();
                self.controls.building.focus();
                if(self.initialValues.building) {
                    self.controls.building.val(self.initialValues.building);
                    self.initialValues.building = null;
                    self.onBuildingChange.call(self);
                }
                if(self.initialValues.flat) {
                    self.controls.flat.val(self.initialValues.flat);
                    self.initialValues.flat = null;
                    self.onFlatChange.call(self);
                }
                self.controls.streettype.val(self.streets.data['adrs' +self.values.street].type);
                self._position('center');
            }  else {
                self.components.building.hide();
                self.components.flat.hide();
            }
            self.createFormatedAddress();
        },
        loadStreetTypes: function() {
            var self = this;
            if(self.streettypes)
                return;
            $.ajax({
                url: self.options.url + '/street-types',
                type: 'POST',
                dataType: 'json',
                async: true,
                beforeSend: function() {
                    self.showLoaderIcon.call(self);
                },
                complete: function() {
                    self.hideLoaderIcon.call(self);
                },
                success: function(data)  {
                    self.onLoadStreetTypesSuccess.call(self, data);
                }
            });
        },
        onLoadStreetTypesSuccess: function(data) {
            var self = this,
                firstChar = null,
                optgroup = null;
            self.controls.streettype.children().remove();
            self.controls.streettype.append(
                $('<optgroup/>').attr('label','').append(
                    $('<option/>').val('').text('')
                )
            );
            self.streettypes = new Object();
            for(var i in data) {
                var dataItem = data[i],
                    itemFirstChar = dataItem.title.substr(0,1);
                if(firstChar != itemFirstChar) {
                    firstChar = itemFirstChar;
                    optgroup = $('<optgroup />').attr('label', firstChar);
                    self.controls.streettype.append(optgroup);
                }
                var option = $('<option/>');
                option.val(dataItem.id).text(dataItem.titleShort);
                self.streettypes['adrs' + dataItem.id] = {
                    id: dataItem.id,
                    title: dataItem.title,
                    titleShort: dataItem.titleShort
                };
                optgroup.append(option);
            }

        },
        onBuildingChange: function() {
            var self = this;
            if (self.controls.building.timer) {
                clearTimeout(self.controls.building.timer);
                delete self.controls.building.timer;
            }
            self.values.building = self.controls.building.val();
            self.createFormatedAddress();
        },
        onFlatChange: function() {
            var self = this;
            if (self.controls.flat.timer) {
                clearTimeout(self.controls.flat.timer);
                delete self.controls.flat.timer;
            }
            self.values.flat = self.controls.flat.val();
            self.createFormatedAddress();
        },
        createFormatedAddress: function() {
            var self = this,
                address = new Array();
            if(self.values.settlement){
                var settlement = self.settlements['adrs' + self.values.settlement].title;
                address.push(settlement);
            }
            if(self.values.street){
                var street = self.streets.data['adrs' +self.values.street].title,
                    streetTypeId = self.controls.streettype.val();
                if(streetTypeId){
                    var steettype = self.streettypes['adrs' +streetTypeId].titleShort;
                    address.push(steettype);
                }
                address.push(street);
            }
            if(self.values.building) {
                var buildingNumber = self.values.building,
                    building = 'д.' + ' №' + buildingNumber;
                address.push(building);
            }
            if(self.values.flat) {
                var flatNumber = self.values.flat,
                    flat = 'кв.' + ' №' + flatNumber;
                address.push(flat);
            }
            self.controls.address.val(address.join(' '));
        },
        showLoaderIcon: function(){
            var self = this;
            self.loadIndicator.prependTo(self.element);
        },
        hideLoaderIcon: function() {
            var self = this;
            self.loadIndicator.remove();
        },
        openDialog: function(){
            var self = this;
            $.ui.dialog.prototype.open.call(self);
        },
        option: function(key, value) {
            var self = this;
            if (key === 'values') {
                if (value === undefined) {
                    return self._getValues();
                } else {
                    return self._setValues(value);
                }
            } else {
                return $.widget.prototype.option.call(self, key, value);
            }
        },
        close: function () {
            var self = this;
            self.controls.street.children().remove();
            self.controls.settlement.children().remove();
            $.ui.dialog.prototype.close.call(self);
        },
        _setValues: function (values) {
            var self = this;
            self.initialValues = {
                settlement: values.settlement ? values.settlement: self.options.initialValues.settlement,
                street: values.street  ? values.street: self.options.initialValues.street,
                building: values.building  ? values.building: self.options.initialValues.building,
                flat: values.flat  ? values.flat: self.options.initialValues.flat
            };
            self.loadSettlements();
            return this;
        },
        _getValues: function() {
            var self = this,
                values = $.extend({}, self.values, {address: self.controls.address.val()});
            return values;
        }
    }));

    $.fn.attachAddressDialog = function(pin){
        if(this.addressDialog) return;
        if(typeof pin == 'string') {
            var fn = $.addressattach.getAccessor($.fn.addressdialog,pin);
            if (!fn) {
                    throw ("addressdialog - No such method: " + pin);
            }
            var args = $.makeArray(arguments).slice(1);
	    return fn.apply(this, args);
        }
        return this.each(function(){
            if(this.addressAttachment)
               return;
            if(this.tagName.toUpperCase()!='TEXTAREA') {
                 alert("Element is not a textarea");
                 return;
            }
            var ts = this,
                addressAttachment = $.extend({},{
                    controlFields              : $.extend(pin.controlFields, {address: $(ts)}) || {},
                    url                        : pin.url,
                    'SettlementText'           : pin.SettlementText || 'Settlement',
                    'StreetText'               : pin.StreetText || 'Street',
                    'StreetTypeText'           : pin.StreetTypeText || 'StreetType',
                    'BuildingText'             : pin.BuildingText || 'BuildingNumber',
                    'FlatText'                 : pin.FlatText ||'FlatNumber',
                    'TitleText'                : pin.TitleText || 'Entering Address',
                    'ApplyCommandText'         : pin.ApplyCommandText || 'ApplyCommand',
                    'CancelCommandText'        : pin.CancelCommandText || 'CancelCommand'
                });
            this.addressAttachment = addressAttachment;
            var wrapper = $('<div />').css('position', 'relative'),
                btnEdit = $('<div />').addClass('ui-state-default ui-corner-all').append(
                            $('<span />').addClass('ui-icon ui-icon-pencil')
                       ),
                btnRemove = $('<div />').addClass('ui-state-default ui-corner-all').append(
                            $('<span />').addClass('ui-icon ui-icon-trash')
                       );
            $(this).wrap(wrapper);
            var container = $(this).parent();
            container.append(btnEdit.hide(), btnRemove.hide())
                .bind('mouseover', function(){
                    btnEdit.show();
                    btnRemove.show();
                })
                .bind('mouseout', function() {
                    btnEdit.hide();
                    btnRemove.hide();
                });

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
                    $.addressattach._clear.call(ts);
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
                    $.addressattach._openAddressDialog.call(ts);
                })
                .bind('mouseout', function() {
                     $(this).removeClass('ui-state-hover ui-state-focus ui-state-active');
                });
            $.addressattach._createAddressDialog.call(ts);
        });
    }
})(jQuery);