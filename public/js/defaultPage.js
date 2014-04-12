(function($){
    $.isEmptyObject = function(obj) {
        if (typeof obj != 'object')
            return false;

        for(var prop in obj) {
            if(obj.hasOwnProperty(prop))
                return false;
        }

        return true;
    };
    $.fn.setErrorTracker = function(event, errorMsgGenerator) {
        if (!$(this).is(':input') || !$.isFunction(errorMsgGenerator)) {
            return $(this);
        }

        $(this).bind(event, function() {
            var errorMsg = errorMsgGenerator.call(this);

            var elementContainer = $(this).parents('.form-element:eq(0)');
            $('.form-element-additional-errors', elementContainer).remove();
            if (errorMsg) {
                if (!$('.form-element-additional', elementContainer).size()) {
                    $(elementContainer).append($('<div class="form-element-additional" />').css('marginLeft', $('.form-element-main-control', elementContainer).css('marginLeft')));
                }
                $('.form-element-additional', elementContainer).append('<div class="form-element-additional-errors"><ul class="error"><li>' + errorMsg + '</li></ul></div>');
                $(this).blur();
            }
        });
        return $(this);
    };
    $.fn.collapsibleFieldset = function() {
        $(this).each(function() {
            var self = $(this);
            var title = $(this).find('> legend:eq(0)');
            if (!self.is('fieldset') || title.size() == 0) {
                return self;
            }
            self
                .addClass(
                    'ui-collapsablefieldset '
                );
            title
                .addClass(
                    'ui-collapsablefieldset-title '
                )
                .bind('click.collapsablefieldset', function() {
                    self.toggleClass('ui-collapsablefieldset-closed');
                    if (self.hasClass('ui-collapsablefieldset-closed')) {
                        self.children().not(title).addClass('ui-collapsablefieldset-hidecontent');
                    } else {
                        self.children().not(title).removeClass('ui-collapsablefieldset-hidecontent');
                    }
                });
        });
        return $(this);
    };
    $.fn.linkableGroup = function(params) {
        var self = $(this);

        if (
            !self.is('fieldset') ||
            typeof params.buttonTitle == 'undefined' ||
            typeof params.linkedModeTitle == 'undefined' ||
            typeof params.standaloneModeTitle == 'undefined' ||
            typeof params.basicUrl == 'undefined' ||
            typeof params.idHolder == 'undefined'
        ) {
                return;
        }

        var phrases = $.extend(
            {
                ok: 'Ok',
                cancel: 'Cancel',
                select: 'Select',
                breakConnection: 'BreakConnection',
                noSelection: 'No row has been selected!',
                alert: 'Alert!'
            },
            (typeof params.popupPhrases !== 'undefined') ? params.popupPhrases : {}
        );

        var legend = self.find('legend')
        var text = $('<span />')
            .addClass('tc-related-oject-link')
            .attr('title', params.standaloneModeTitle)
            .text(legend.text());
        var buttons = new Object();
        buttons[phrases.cancel] = function() {
            $(this).iwindow('destroy');
        };
        buttons[phrases.select] = function() {
            try {
                var $$ = $(this).find('iframe').get(0).contentWindow.$;
            } catch (error) {
                return;
            }
            if (typeof $$ == 'undefined')
                return;
            var newId = $$('#list').getGridParam('selrow');
            if (!newId || newId == 0) {
                var buttons = new Object();
                buttons[phrases.ok] = function () {$(this).dialog('destroy').unbind().remove();};
                $('<div />')
                    .html(phrases.noSelection)
                    .dialog({
                        modal: true,
                        title: phrases.alert,
                        buttons: buttons,
                        zIndex: $(this).parents('.ui-dialog').css('zIndex') * 1 + 1
                    });
                return;
            }
            params.accept && $.isFunction(params.accept) && params.accept($$('#list').getRowData(newId));
            idHolder.val(newId).change();
            $(this).iwindow('destroy');
        };
        buttons[phrases.breakConnection] = function() {
            idHolder.val('').change();
            $(this).iwindow('destroy');
        };
        var btn = $('<img />')
            .attr('src', '/img/icons/16x16/link.gif')
            .attr('title', params.buttonTitle)
            .css({
                cursor:'pointer'
            })
            .click(function() {
                var getParams = {};
                if (params.getParams) {
                    for (var i in params.getParams) {
                        if ($.isFunction(params.getParams[i])) {
                            getParams[i] = params.getParams[i]();
                        } else {
                            getParams[i] = params.getParams[i];
                        }
                    }
                }
                $('<div />').iwindow({
                    modal: true,
                    title: text.text(),
                    url: params.basicUrl + '/items' + (getParams ? ("?" + $.param(getParams)) : ""),
                    maximized: false,
                    bubble: false,
                    buttons: buttons
                });

                return false;
            })
        legend
            .empty()
            .append(
                text,
                $('<span />').text(' '),
                btn
            );

        var idHolder = $(params.idHolder);
        var observe = function() {
            //remove eny formating
            text.text(text.find('a').unbind().end().text());

            var linkedObjectId = idHolder.val();
            if (linkedObjectId && linkedObjectId != 0) {
                text
                    .attr('title', params.linkedModeTitle)
                    .wrapInner(
                        $('<a />')
                            .css({color: '#000'})
                            .attr('href', params.basicUrl + '/item/id/' + linkedObjectId)
                    );
                text.find('a').bind('click', function() {
                    var self = $(this);
                    $('<div />').iwindow({
                        title: self.text(),
                        url: self.attr('href'),
                        maximized: true,
                        bubble: true
                    });

                    return false;
                });
            } else {
                text
                    .attr('title', params.standaloneModeTitle)
            }
        }
        idHolder.change(observe);

        observe();
        return self;
    }
})(jQuery);

function Page()
{
};
Page.prototype.init = function() {
};
Page.prototype.beforePageLayout = function() {
    var self = this,
        grid = $('#list');
    if(grid.size()){
        grid.setGridParam({
            gridComplete: function() {
                self.setActionsToolbarButtonsState();
                self.setGridToolbarButtonsState();
            },
            onSelectRow: function() {
                var grid = $('#list');
                var selectedRowId = grid.getGridParam('selrow');
                if ($('#' + selectedRowId, grid).hasClass('ui-state-highlight')) {
                    var unSelecting = $('.jqgrow.ui-state-highlight', grid).not('#' + selectedRowId);
                    for(var i=0; i<unSelecting.length; i++){
                        grid.setSelection(unSelecting.eq(i).attr('id'), false);
                    }
                }
                self.setActionsToolbarButtonsState();
                self.setGridToolbarButtonsState();
            },
            loadBeforeSend: function() {
                $('#page-toolbar .ui-button').button('disable');
            }
        });
    }
};
Page.prototype.afterPageLayout = function() {
    if ($('#list').size()) {
        //$('#list').parents('.ui-layout-pane:eq(0)').addClass('ui-layout-pane-nopadding');
    }
   $(window).resize();
};
Page.prototype.createToolbar = function(options) {
    var toolbar = $('<div />').addClass('ui-toolbar-group');
    for (var i in options) {
        $('<div />')
            .attr('id', options[i]['id'])
            .addClass('ui-toolbar-button')
            .text(i)
            .button({
                icon: options[i]['icon'] ? options[i]['icon'] : null,
                iconSize: options[i]['icon'] ? 24 : null,
                disabled: options[i]['disabled'] ? options[i]['disabled'] : false,
                showtext: (options[i]['icon'] && !options[i]['showtext']) ? false : true,
                action: options[i]['action']
            })
            .appendTo(toolbar);
    }
    toolbar.appendTo(
        $('body > .ui-layout-north:eq(0)')
            .attr('id', 'page-toolbar')
    );
}
Page.prototype.createGridToolbar = function(refresh, select, deselect, toggle) {
    var toolbar = $('<div />').addClass('ui-toolbar-group');
    var empty = true;
    if (refresh !== false) {
        $('<div id="action-grid-refresh"/>')
            .addClass('ui-toolbar-button')
            .text(refresh)
            .button({
                icon: '/img/icons/24x24/table_refresh.gif',
                iconSize: 24,
                showtext: false,
                action: function() {
                    var grid = $("#list");
                    //@TODO: проверку на наличие тулбара в условии сделать с использованием стандартных средств
                    if('clearToolbar' in grid[0] && $.isFunction(grid[0].clearToolbar)){
                       grid[0].clearToolbar();
                    } else {
                       grid.trigger("reloadGrid");
                    }
                }
            })
            .appendTo(toolbar);
            empty = false;
    }
    if (select !== false) {
        $('<div id="action-select-all"/>')
            .addClass('ui-toolbar-button')
            .text(select)
            .button({
                icon: '/img/icons/24x24/select_all.gif',
                iconSize: 24,
                showtext: false,
                action: function() {
                  var grid = $('#list'),
                      rows = grid.getDataIDs(),
                      selectedRows = grid.getGridParam('selarrrow');
                  for(var i = 0, j = rows.length; i < j; i++) {
                      var isSelected = $.inArray(rows[i], selectedRows);
                      if(isSelected < 0){
                         grid.setSelection(rows[i], true);
                      }
                  }
                }
            })
            .appendTo(toolbar);
            empty = false;
    }
    if (deselect !== false) {
        $('<div id="action-select-none"/>')
            .addClass('ui-toolbar-button')
            .text(deselect)
            .button({
                icon: '/img/icons/24x24/deselect_all.gif',
                iconSize: 24,
                showtext: false,
                action: function() {
                  var grid = $('#list'),
                      rows = grid.getDataIDs(),
                      selectedRows = grid.getGridParam('selarrrow');
                  for(var i = 0, j = rows.length; i < j; i++) {
                      var isSelected = $.inArray(rows[i], selectedRows);
                      if(isSelected >= 0){
                         grid.setSelection(rows[i], true);
                      }
                  }
                }
            })
            .appendTo(toolbar);
            empty = false;
    }
    if (toggle !== false) {
        $('<div id="action-select-toggle"/>')
            .addClass('ui-toolbar-button')
            .text(toggle)
            .button({
                icon: '/img/icons/24x24/toggle_select.gif',
                iconSize: 24,
                showtext: false,
                action: function() {
                  var grid = $('#list'),
                      rows = grid.getDataIDs(),
                      selectedRows = grid.getGridParam('selarrrow');
                  for(var i = 0, j = rows.length; i < j; i++) {
                      grid.setSelection(rows[i], true);
                  }
                }
            })
            .appendTo(toolbar);
            empty = false;
    }
    if (!empty) {
        toolbar.appendTo(
            $('body > .ui-layout-north:eq(0)')
                .attr('id', 'page-toolbar')
        );
    }
};
Page.prototype.createInterfaceToolbar = function(title) {
    if (title === false)
        return;
    $('<div />')
        .addClass('ui-toolbar-group')
        .append(
            $('<div id="action-interface"/>')
            .addClass('ui-toolbar-button')
            .text(title)
            .button({
                icon: '/img/icons/24x24/interface.gif',
                iconSize: 24,
                showtext: false,
                action: function() {}
            })
        )
        .appendTo('#page-toolbar');
};
Page.prototype.setActionsToolbarButtonsState = function() {
    var grid = $('#list');
    var treeGrid = grid.getGridParam('treeGrid');
    $('#action-create').button('enable');
    if(treeGrid){
        var selecectedRow = grid.getGridParam('selrow');
        $('#action-remove, #action-edit, #action-explore').button((null !== selecectedRow)? 'enable' : 'disable');
    } else {
        var selecectedRows = grid.getGridParam('selarrrow'); // In treeGrid is null
        $('#action-remove').button(selecectedRows.length == 0 ? 'disable' : 'enable');
        $('#action-edit, #action-explore').button(selecectedRows.length != 1 ? 'disable' : 'enable');
    }
};
Page.prototype.setGridToolbarButtonsState = function() {
    var selecectedRows = $('#list').getGridParam('selarrrow');
    $('#action-grid-refresh').button('enable');
    if (selecectedRows.length === 0) {
        $('#action-select-all').button('enable');
        $('#action-select-none').button('disable');
        $('#action-select-toggle').button('disable');
    } else if (selecectedRows.length === $('#list tr.jqgrow').length) {
        $('#action-select-all').button('disable');
        $('#action-select-none').button('enable');
        $('#action-select-toggle').button('disable');
    } else {
        $('#action-select-all').button('enable');
        $('#action-select-none').button('enable');
        $('#action-select-toggle').button('enable');
    }
};
Page.prototype.setInterfaceToolbarButtonsState = function () {
    $('#action-interface').button('enable');
};

Page.prototype.transformInputsToPlaceholder = function(jqElements, setState) {
    jqElements = jqElements.filter(':visible')
    if (!setState)
        setState = 'readonly';

    jqElements
        .filter(':text').each(function() {
                var el = $(this),
                    placeholder = null;

                el.attr(setState, setState);
                el.hide();

                if (el.hasClass('hasDatepicker')) {
                    el.datepicker('destroy');
                }

                if (el.attr('href')) {
                    placeholder = $('<a />')
                        .attr('href', el.attr('href'))
                        .click(function() {
                            $('<div />').iwindow({
                                title: el.parents('.form-element').find('label').text(),
                                url: el.attr('href'),
                                maximized: true,
                                bubble: true
                            });
                            return false;
                        });
                } else {
                    placeholder = $('<span />');
                }
                placeholder.text(el.val()).insertAfter(el);
            }).end()
        .filter('textarea').each(function() {
                var el = $(this),
                    placeholder = null;

                el.attr(setState, setState);
                el.hide();

                placeholder = $('<p />').css({"margin":"0","padding":"0"});
                placeholder.html(el.val().replace('\n', '<br />')).insertAfter(el);
                if (el.attr('href')) {
                    placeholder.wrapInner($('<a />')).find('a')
                        .attr('href', el.attr('href'))
                        .click(function() {
                            $('<div />').iwindow({
                                title: el.parents('.form-element').find('label').text(),
                                url: el.attr('href'),
                                maximized: true,
                                bubble: true
                            });
                            return false;
                        });
                }
            }).end()
        .filter(':checkbox').each(function() {
                var el = $(this),
                    placeholder = $('<img />');

                el.attr(setState, setState);
                el.hide();

                if (el.is(':checked')) {
                    placeholder.addClass('bool-true').attr('src', '/img/icons/16x16/tick.gif');
                } else {
                    placeholder.addClass('bool-false').attr('src', '/img/icons/16x16/cross.gif');
                }
                placeholder.insertAfter(el);
            }).end()
       .filter('select').each(function() {
                var el = $(this),
                    placeholder = $('<span />');

                el.attr(setState, setState);
                el.hide();
                placeholder.text(el.find('option[value=' + el.val() + ']').text()).insertAfter(el);
            }).end();
};

/* Создание двойного фильтра для jGrid */
Page.prototype.buildDoubleFilter = function () {
    var groups = new Object();
    var inputs = $("input[name*='DoubleFilter']");
    var name = null;
    var subNames = null;
    var groupName = null;
    var createDatepicker = '';
    var inputsLength = inputs.length;
    for(var i=0; i<inputsLength; i++){
        name = inputs.eq(i).attr('name');
        subNames = name.split('DoubleFilter');
        if('undefined' == typeof groups[subNames[1]]){
            groups[subNames[1]] = [name];
        } else {
            groups[subNames[1]].push(name);
        }
    }
    for(i in groups){
        if(2 == groups[i].length){
            inputs = $("input[name$='DoubleFilter"+i+"']");
            var input1Cont = inputs.eq(0).parent().parent().css({"height":"1px","overflov":"hidden"});
            var input2Cont = inputs.eq(1).parent().parent().css({"height":"1px","overflov":"hidden"});
            var newConteiner = $('<div />').attr({'id':'double-filter-'+i,'class':'form-element double-filter'});
            var newInputsCont = $('<div />').attr('class','form-element-item');
            var textBlock = input1Cont.find('label').text();
            var textBlocks = textBlock.split(':');
            var newLabel1 = '<span class="double-filter-label0">'+textBlocks[0]+'</span><span class="double-filter-label1">'+textBlocks[1]+'</span>';
            var newLabel2 = '<span class="double-filter-label2">'+input2Cont.find('label').text()+'</span>';
            input1Cont.before(newConteiner);
            newConteiner.append(newLabel1, newInputsCont);
            newInputsCont.append(inputs.eq(0), newLabel2, inputs.eq(1));
            if('Date' == i.substr(0,4)){
                for(var j=0; j<2; j++){
                    createDatepicker += '$("input[name$=\''+inputs.eq(j).attr('name')+'\']").datepicker({"changeMonth":true,"changeYear":true,"showOn":"button","duration":"fast","buttonImage":"\/img\/icons\/16x16\/calendar.gif","buttonImageOnly":true,"dateFormat":"dd.mm.yy"});';
                }
            }
            input1Cont.remove();
            input2Cont.remove();
        } else alert('No two elements in group '+i);
    }
    if('' != createDatepicker){
        setTimeout(createDatepicker,1000);
    }
};
page = new Page();