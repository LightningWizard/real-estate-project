/*
 * jQuery Window 0.1
 * jQuery DockBar 0.1
 */
(function($){
    $.widget('ui.window', $.extend({}, $.ui.dialog.prototype, {
        options: $.extend({}, $.ui.dialog.prototype.options, {
            dockbar: '.ui-dockbar:eq(0)',
            dockarea: '.ui-dockarea:eq(0)',
            maximizeText: 'Maximize',
            minimizeText: 'Minimize',
            restoreText: 'Restore',
            minimized: false,
            maximized: false,
            bubble: false
        }),
        _create: function() {
            var self = this
            if (self.options.bubble && window != top) {
                var element;
                if ($.browser.msie) {
                    element = '<div />';
                    self.element.unbind().remove();
                } else {
                    element = self.element.unbind().remove();
                }
                top.$(element).window(self.options);
                return;
            }

            $.ui.dialog.prototype._create.apply(self, arguments);
            self._isMaximized = false;
            self._maximizedMemory = {
                width: self.options.width,
                height: self.options.height,
                position: self.options.position,
                resizable: self.options.resizable,
                draggable: self.options.draggable
            };

            self.uiDialogTitlebar.find('.ui-dialog-titlebar-close')
            .unbind('click')
            .click(function(){
                self.destroy();
                return false;
            });
            self.uiDialogTitlebarMaximize = $('<a href="#"/>')
            .addClass(
                'ui-dialog-titlebar-maximize ' +
                'ui-corner-all'
                )
            .attr('role', 'button')
            .hover(
                function() {
                    self.uiDialogTitlebarMaximize.addClass('ui-state-hover');
                },
                function() {
                    self.uiDialogTitlebarMaximize.removeClass('ui-state-hover');
                }
                )
            .focus(function() {
                if (!self.uiDialogTitlebarMaximize.hasClass('ui-state-disabled')) {
                    self.uiDialogTitlebarMaximize.addClass('ui-state-focus');
                } else {
                    self.uiDialogTitlebarMaximize.blur();
                }
            })
            .blur(function() {
                self.uiDialogTitlebarMaximize.removeClass('ui-state-focus');
            })
            .mousedown(function(ev) {
                ev.stopPropagation();
            })
            .click(function(event) {
                !self.uiDialogTitlebarMaximize.hasClass('ui-state-disabled') && self.toggleMaximize(event);
                self.uiDialogTitlebarMaximize.blur();
                return false;
            });
            self.uiDialogTitlebarMaximize.appendTo(self.uiDialogTitlebar);
            self.uiDialogTitlebarMaximizeText = $('<span/>')
            .addClass(
                'ui-icon ' +
                'ui-icon-plusthick'
                )
            .text(self.options.maximizeText);
            self.uiDialogTitlebarMaximizeText.appendTo(self.uiDialogTitlebarMaximize);
            if (self.options.dockarea === false || $(self.options.dockarea).filter('.ui-dockarea').size() == 0) {
                self.uiDialogTitlebarMaximize.addClass('ui-state-disabled');
            } else {
                $(self.options.dockarea).dockarea('register', self.element);
            }
            self.uiDialogTitlebarMinimize = $('<a href="#"/>')
            .addClass(
                'ui-dialog-titlebar-minimize ' +
                'ui-corner-all'
                )
            .attr('role', 'button')
            .hover(
                function() {
                    self.uiDialogTitlebarMinimize.addClass('ui-state-hover');
                },
                function() {
                    self.uiDialogTitlebarMinimize.removeClass('ui-state-hover');
                }
                )
            .focus(function() {
                if (!self.uiDialogTitlebarMinimize.hasClass('ui-state-disabled')) {
                    self.uiDialogTitlebarMinimize.addClass('ui-state-focus');
                } else {
                    self.uiDialogTitlebarMinimize.blur();
                }
            })
            .blur(function() {
                self.uiDialogTitlebarMinimize.removeClass('ui-state-focus');
            })
            .mousedown(function(ev) {
                ev.stopPropagation();
            })
            .click(function(event) {
                !self.uiDialogTitlebarMinimize.hasClass('ui-state-disabled') && self.toggleMinimize(event);
                self.uiDialogTitlebarMinimize.blur();
                return false;
            });
            self.uiDialogTitlebarMinimize.appendTo(self.uiDialogTitlebar);
            self.uiDialogTitlebarMinimizeText = $('<span/>')
            .addClass(
                'ui-icon ' +
                'ui-icon-minusthick'
                )
            .text(self.options.maximizeText);
            self.uiDialogTitlebarMinimizeText.appendTo(self.uiDialogTitlebarMinimize);
            if (self.options.dockbar === false || $(self.options.dockbar).filter('.ui-dockbar').size() == 0) {
                self.uiDialogTitlebarMinimize.addClass('ui-state-disabled');
            } else {
                self.wlink = $('<div />')
                .data('owner', self)
                .addClass(
                    'ui-dialog-link ' +
                    'ui-state-default ' +
                    'ui-corner-all'
                    )
                .attr('role', 'button')
                .attr('title', self.options.title || self.element.attr('title') || '&nbsp;')
                .hover(
                    function() {
                        self.wlink.addClass('ui-state-hover');
                    },
                    function() {
                        self.wlink.removeClass('ui-state-hover');
                    }
                    )
                .focus(function() {
                    if (!self.wlink.hasClass('ui-state-disabled')) {
                        self.wlink.addClass('ui-state-focus');
                    } else {
                        self.wlink.blur();
                    }
                })
                .blur(function() {
                    self.wlink.removeClass('ui-state-focus');
                })
                .mousedown(function(ev) {
                    ev.stopPropagation();
                    return false;
                })
                .click(function(event) {
                    if (!self.wlink.hasClass('ui-state-disabled')) {
                        if (self.wlink.hasClass('ui-state-active')) {
                            self.toggleMinimize(event);
                        } else {
                            $(self.options.dockbar).dockbar('setFocus', self.wlink);
                            if (!self.isOpen()) {
                                self.restore();
                            }
                            self.moveToTop();
                        }
                    }
                    return false;
                })
                .html(self.options.title || self.element.attr('title') || '&nbsp;');
                $(self.options.dockbar).dockbar('register', self.wlink);
            }
            if (self.options.maximized) {
                self.maximize();
            }
            if (self.options.minimized) {
                self.minimize();
            }
        },
        _init: function() {
            if(this.uiDialog){
                $.ui.dialog.prototype._init.call(this);
            }
        },
        close: function() {
            $.ui.dialog.prototype.close.call(this);
        },
        destroy: function() {
            var self = this;
            $.ui.dialog.prototype.destroy.call(self);
            if (self.options.dockarea !== false && $(self.options.dockarea).filter('.ui-dockarea').size() > 0) {
                $(self.options.dockarea).dockarea('unregister', self.element);
            }
            if (self.options.dockbar !== false && $(self.options.dockbar).filter('.ui-dockbar').size() > 0) {
                self.wlink.unbind()
                $(self.options.dockbar).dockbar('unregister', self.wlink);
            }
            self.element.unbind().remove();
        },
        moveToTop: function() {
            var self = this;
            $.ui.dialog.prototype.moveToTop.call(self);
            self.wlink && self.wlink.parents('.ui-dockbar').dockbar('setFocus', self.wlink);
        },
        toggleMaximize: function() {
            var self = this;
            if (!self.isMaximized()) {
                self.maximize();
            } else {
                self.restore();
            }
        },
        toggleMinimize: function() {
            var self = this;
            if (!self.isMinimized()) {
                self.minimize();
            } else {
                self.restore();
            }
        },
        maximize: function() {
            var self = this;
            var dockarea = $(self.options.dockarea);
            var offsetCorrection = dockarea.dockarea('option', 'offsetCorrection');
            if ($.isFunction(offsetCorrection.x)) {
                offsetCorrection.x = offsetCorrection.x.call(dockarea.get(0));
            }
            if ($.isFunction(offsetCorrection.y)) {
                offsetCorrection.y = offsetCorrection.y.call(dockarea.get(0));
            }
            self._maximizedMemory = {
                width: self.options.width,
                height: self.options.height,
                position: self.options.position,
                resizable: self.options.resizable,
                draggable: self.options.draggable
            };
            self.options.width = dockarea.width() - offsetCorrection.x;
            self.options.height = dockarea.height() - offsetCorrection.y;
            self.options.position = [dockarea.get(0).offsetLeft + offsetCorrection.x, dockarea.get(0).offsetTop + offsetCorrection.y];
            self.options.resizable = false;
            self._maximizedMemory.resizable && self.uiDialog.resizable('destroy');
            self.options.draggable = false;
            self._maximizedMemory.draggable && self.uiDialog.draggable('destroy');
            self._size();
            self._position(self.options.position);
            self._isMaximized = true;
        },
        onDockareaResize: function() {
            var self = this;
            if (self.isMaximized()) {
                var dockarea = $(self.options.dockarea);
                var offsetCorrection = dockarea.dockarea('option', 'offsetCorrection');
                if ($.isFunction(offsetCorrection.x)) {
                    offsetCorrection.x = offsetCorrection.x.call(dockarea.get(0));
                }
                if ($.isFunction(offsetCorrection.y)) {
                    offsetCorrection.y = offsetCorrection.y.call(dockarea.get(0));
                }
                self.options.width = dockarea.width() - offsetCorrection.x;
                self.options.height = dockarea.height() - offsetCorrection.y;
                self.options.position = [dockarea.get(0).offsetLeft + offsetCorrection.x, dockarea.get(0).offsetTop + offsetCorrection.y];
                self._size();
                self._position(self.options.position);
            }
        },
        minimize: function() {
            var self = this;
            $.ui.dialog.prototype.close.call(self);
        },
        restore: function() {
            var self = this;
            if (self.isMinimized()) {
                $.ui.dialog.prototype.open.call(self);
            } else if (self.isMaximized()) {
                self.options.width = self._maximizedMemory.width;
                self.options.height = self._maximizedMemory.height;
                self.options.position = self._maximizedMemory.position;
                self.options.resizable = self._maximizedMemory.resizable;
                self.options.resizable && self._makeDraggable();
                self.options.draggable = self._maximizedMemory.draggable;
                self.options.draggable && self._makeResizable(true);
                self._size();
                self._position(self.options.position);
                self._isMaximized = false;
            }
        },
        isMinimized: function() {
            return !this._isOpen;
        },
        isMaximized: function() {
            return this._isMaximized;
        }
    }));

    $.extend($.ui.window, {
        version: '0.1 beta'
    });

    $.widget('ui.iwindow', $.extend({}, $.ui.window.prototype, {
        options: $.extend({}, $.ui.window.prototype.options, {
            url: false,
            refreshText: 'Refresh',
            width: 640,
            height: 480,
            maximized: true,
            refreshAtStartPosition: false
        }),
        _create: function() {
            var self = this;
            if (self.options.bubble && window != top) {
                var element;
                if ($.browser.msie) {
                    element = '<div />';
                    self.element.unbind().remove();
                } else {
                    element = self.element.unbind().remove();
                }
                top.$(element).iwindow(self.options);
                return;
            }
            $.ui.window.prototype._create.call(self);
            if (!self.options.url)
                return;
            self.uiDialogTitlebarRefresh = $('<a href="#"/>')
            .addClass(
                'ui-dialog-titlebar-refresh ' +
                'ui-corner-all'
                )
            .attr('role', 'button')
            .hover(
                function() {
                    self.uiDialogTitlebarRefresh.addClass('ui-state-hover');
                },
                function() {
                    self.uiDialogTitlebarRefresh.removeClass('ui-state-hover');
                }
                )
            .focus(function() {
                if (!self.uiDialogTitlebarRefresh.hasClass('ui-state-disabled')) {
                    self.uiDialogTitlebarRefresh.addClass('ui-state-focus');
                } else {
                    self.uiDialogTitlebarRefresh.blur();
                }
            })
            .blur(function() {
                self.uiDialogTitlebarRefresh.removeClass('ui-state-focus');
            })
            .mousedown(function(ev) {
                ev.stopPropagation();
            })
            .click(function(event) {
                !self.uiDialogTitlebarRefresh.hasClass('ui-state-disabled') && self.refresh(event);
                self.uiDialogTitlebarRefresh.blur();
                return false;
            });
            self.uiDialogTitlebarRefresh.appendTo(self.uiDialogTitlebar);
            self.uiDialogTitlebarRefreshText = $('<span/>')
            .addClass(
                'ui-icon ' +
                'ui-icon-refresh'
                )
            .text(self.options.refreshText);
            self.uiDialogTitlebarRefreshText.appendTo(self.uiDialogTitlebarRefresh);

            self.loadIndicator = $('<div />')
            .addClass('ui-dialog-loader');


            self.port = $('<iframe />')
            .addClass(
                'ui-dialog-port'
                )
            .css({
                width: '100%',
                height: '100%'
            })
            .load(function(){
                if(undefined !== self.port){
                    var innerWindow = self.port[0].contentWindow;
                    self.option('title', innerWindow.document.title);
                    self.wlink && self.wlink.text(innerWindow.document.title ? innerWindow.document.title : '...').attr('title', innerWindow.document.title);
                }
            })
            .appendTo(self.element.css('overflow', 'hidden'));

            self.cover = $('<div />')
            .addClass(
                'ui-iwindow-cover ' +
                'ui-corner-all'
                )
            .css({
                backgroundColor: 'white',
                opacity: 0.0,
                position: 'absolute',
                top: self.element.offsetParent().top,
                left: 0,
                width: '100%',
                height: '100%'
            })
            .prependTo(self.element);

            self.options.resizeStart = function() {
                self.cover.show();
            }
            self.options.resizeStop = function() {
                self.cover.hide();
            }
            self.options.dragStart = function() {
                self.cover.show();
            }
            self.options.dragStop = function() {
                self.cover.hide();
            }

            self.moveToTop();
            self.refresh();
        },
        moveToTop: function() {
            $('.ui-iwindow-cover').show();
            this.cover.hide();
            $.ui.window.prototype.moveToTop.call(this);
        },
        refresh: function() {
            var self = this;
            var innerWindow = self.port[0].contentWindow;

            innerWindow.document.location = (self.options.refreshAtStartPosition || innerWindow.document.location == 'about:blank') ? self.options.url : innerWindow.document.location;
            self.port.one('load', function() {
                self.hideLoaderIcon();
            });
            self.showLoaderIcon();
        },
        showLoaderIcon: function() {
            var self = this;
            self.element.prepend(self.loadIndicator);
        },
        hideLoaderIcon: function() {
            var self = this;
            self.loadIndicator.remove();
        },
        close: function() {
            this.element.parent().hide();
            this._isOpen = false;
        },
        open: function() {
            if (this._isOpen) {
                return;
            }
            this._size();
            this._position(this.options.position);
            this.element.parent().show();
            this._isOpen = true;
        },
        minimize: function() {
            var self = this;
            self.close();
        },
        restore: function() {
            var self = this;
            if (self.isMinimized()) {
                self.open();
            } else if (self.isMaximized()) {
                self.options.width = self._maximizedMemory.width;
                self.options.height = self._maximizedMemory.height;
                self.options.position = self._maximizedMemory.position;
                self.options.resizable = self._maximizedMemory.resizable;
                self.options.resizable && self._makeDraggable();
                self.options.draggable = self._maximizedMemory.draggable;
                self.options.draggable && self._makeResizable(true);
                self._size();
                self._position(self.options.position);
                self._isMaximized = false;
            }
        }
    }));

    $.extend($.ui.iwindow, {
        version: '0.1 beta'
    });

    $.widget('ui.dockbar', {
        options: {
            elementWidth: 150,
            flexibility: 0.5,
            prevText: 'Prevoiuse',
            nextText: 'Next',
            onChange: false,
            clearBtn: false,
            clearAllText: 'Minimize All',
            clearAllConfirmText: 'If data in other browser windows is not saved then it will be lost',
            yesText: 'Yes',
            noText: 'No',
            clearAllImg: null
        },
        _create: function() {
            var self = this;
            self.element.addClass(
                'ui-widget ' +
                'ui-dockbar ' +
                'ui-corner-all'
                );
            self.container = $('<div />')
                .addClass('ui-dockbar-content')
                .appendTo(self.element);
            self.btnPrev = $('<div />')
                .addClass(
                    'ui-dockbar-navigation ' +
                    'ui-dockbar-prev ' +
                    'ui-corner-tr ' +
                    'ui-state-disabled'
                    )
            .attr('role', 'button')
            .hover(
                function() {
                    self.btnPrev.addClass('ui-state-hover');
                },
                function() {
                    self.btnPrev.removeClass('ui-state-hover');
                }
                )
            .focus(function() {
                if (!self.btnPrev.hasClass('ui-state-disabled')) {
                    self.btnPrev.addClass('ui-state-focus');
                } else {
                    self.btnPrev.blur();
                }
            })
            .blur(function() {
                self.btnPrev.removeClass('ui-state-focus');
            })
            .mousedown(function(ev) {
                ev.stopPropagation();
            })
            .click(function(event) {
                !self.btnPrev.hasClass('ui-state-disabled') && self.showPrev(event);
                self.btnPrev.blur();
                return false;
            })
            .prependTo(self.element);
            self.btnPrevText = $('<div />')
            .addClass(
                'ui-icon ' +
                'ui-icon-carat-1-n'
                )
            .text(self.options.prevText)
            .appendTo(self.btnPrev);
            self.btnNext = $('<div />')
            .addClass(
                'ui-dockbar-navigation ' +
                'ui-dockbar-next ' +
                'ui-corner-br ' +
                'ui-state-disabled'
                )
            .attr('role', 'button')
            .hover(
                function() {
                    self.btnNext.addClass('ui-state-hover');
                },
                function() {
                    self.btnNext.removeClass('ui-state-hover');
                }
                )
            .focus(function() {
                if (!self.btnNext.hasClass('ui-state-disabled')) {
                    self.btnNext.addClass('ui-state-focus');
                } else {
                    self.btnNext.blur();
                }
            })
            .blur(function() {
                self.btnNext.removeClass('ui-state-focus');
            })
            .mousedown(function(ev) {
                ev.stopPropagation();
            })
            .click(function(event) {
                !self.btnNext.hasClass('ui-state-disabled') && self.showNext(event);
                self.btnNext.blur();
                return false;
            })
            .appendTo(self.element);
            self.btnNextText = $('<div />')
            .addClass(
                'ui-icon ' +
                'ui-icon-carat-1-s'
                )
            .text(self.options.prevText)
            .appendTo(self.btnNext);

            if (self.options.clearBtn) {
                self.btnClearDescktop = $('<div />')
                .text(self.options.clearAllText)
                .css({
                    "float":"left"
                })
                .prependTo(self.element);
                self.btnClearDescktop.button({
                    width: 18,
                    height: 18,
                    icon: self.options.clearAllImg,
                    iconSize: 18,
                    showtext: self.options.clearAllImg ? false : true,
                    disabled: false,
                    action: function() {
                        self.clearDockArea.call(self);
                        return true;
                    }
                });
            }

            $(window).bind('keydown.dockbar', function(event) {
                if (event.ctrlKey) {
                    switch (event.keyCode) {
                        case 37:
                            var wlink = $('.ui-dialog-link.ui-state-active', self.element).parent().prev().find('.ui-dialog-link');
                            if (!wlink.size()) {
                                wlink = $('.ui-dialog-link:last', self.element);
                            }
                            (!wlink.hasClass('ui-state-active')) && wlink.click();
                            break;
                        case 39:
                            var wlink = $('.ui-dialog-link.ui-state-active', self.element).parent().next().find('.ui-dialog-link');
                            if (!wlink.size()) {
                                wlink = $('.ui-dialog-link:first', self.element);
                            }
                            (!wlink.hasClass('ui-state-active')) && wlink.click();
                            break;
                        default:
                    //do nothing
                    }
                }
            })
        },
        resize: function() {
            var self = this;
            self._setParams();
            self._show();
        },
        showPrev: function() {
            var self = this;
            if (self.params.displayGroup > 0) {
                self.params.displayGroup--;
            }
            self._setButtonsState();
            self._show();
        },
        showNext: function() {
            var self = this;
            if (self.params.displayGroup < Math.ceil(self.params.totalCount / self.params.displayCount) - 1) {
                self.params.displayGroup++;
            }
            self._setButtonsState();
            self._show();
        },
        showThis: function(wlink) {
            var self = this;
            var displayGroup = Math.floor(self.container.find('.ui-dockbar-content-element > .ui-dialog-link').index(self.container.find('.ui-dockbar-content-element > .ui-dialog-link.ui-state-active')) / self.params.displayCount)
            if (displayGroup >= 0) {
                self.params.displayGroup = displayGroup;
                self._setButtonsState();
                self._show();
            }
        },
        register: function(wlink) {
            var self = this;
            $('<div />')
            .addClass('ui-dockbar-content-element')
            .append(wlink)
            .appendTo(self.container);
            if ($.isFunction(self.options.onChange)) {
                self.options.onChange($('.ui-dialog-link', self.element).size());
            }
            self.resize();
            self.setFocus(wlink);
        },
        unregister: function(wlink) {
            var self = this;
            wlink.parent().remove();
            if ($.isFunction(self.options.onChange)) {
                self.options.onChange($('.ui-dialog-link', self.element).size());
            }
            self.resize();
        },
        setFocus: function(wlink) {
            var self = this;
            self.container.find('.ui-dockbar-content-element > .ui-dialog-link').not(wlink).removeClass('ui-state-active');
            wlink.addClass('ui-state-active');
            self.showThis(wlink)
        },
        _setParams: function() {
            var self = this;
            var totalCount = self.container.find('.ui-dockbar-content-element').size();
            var containerWidth = self.container.innerWidth();
            var displayCount = Math.min(totalCount, Math.floor(containerWidth / (self.options.elementWidth * (1 - self.options.flexibility))));
            var elementWidth = Math.min(self.options.elementWidth + self.options.elementWidth * self.options.flexibility, containerWidth / displayCount);
            self.params = {
                totalCount: totalCount,
                displayCount: displayCount,
                displayGroup: Math.floor(self.container.find('.ui-dockbar-content-element > .ui-dialog-link').index(self.container.find('.ui-dockbar-content-element > .ui-dialog-link.ui-state-focus')) / displayCount)
            };
            if (self.params.displayGroup < 0) {
                self.params.displayGroup = 0;
            }
            self.container.find('.ui-dockbar-content-element').width(elementWidth);
            self._setButtonsState();
        },
        _setButtonsState: function() {
            var self = this;
            if (self.params.displayGroup > 0) {
                self.btnPrev.removeClass('ui-state-disabled');
            } else {
                self.btnPrev.addClass('ui-state-disabled');
            }
            if (self.params.displayGroup < Math.ceil(self.params.totalCount / self.params.displayCount) - 1) {
                self.btnNext.removeClass('ui-state-disabled');
            } else {
                self.btnNext.addClass('ui-state-disabled');
            }
        },
        _show: function() {
            var self = this;
            self.container.find('.ui-dockbar-content-element').each(function(i) {
                if (i >= self.params.displayGroup * self.params.displayCount && i < (self.params.displayGroup + 1) * self.params.displayCount ) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        },
        clearDockArea: function() {
            var self = this;
            var buttons = Object();
            buttons[self.options.noText] = function(){
                $(this).dialog("destroy");
            };
            buttons[self.options.yesText] = function(){
                var docbarLinks = self.container.find('.ui-dockbar-content-element > .ui-dialog-link');
                var n = docbarLinks.length;
                var currZindex = 0;
                var maxZindex = 0;
                var maxZindexIWindow = null;
                for(var i=0; i<n; i++){
                    var currIWindow = docbarLinks.eq(i).data('owner');
                    currZindex = currIWindow.element.parent().css("z-index");
                    if(currZindex>maxZindex){
                        maxZindex = currZindex;
                        maxZindexIWindow = currIWindow;
                    }
                }
                for(var i=0; i<n; i++){
                    var currIWindow = docbarLinks.eq(i).data('owner');
                    if(currIWindow!=maxZindexIWindow) currIWindow.destroy();
                }
                $(this).dialog("destroy");
            };
            $('<div />').text(self.options.clearAllConfirmText).dialog({
                "buttons":buttons,
                "modal":true
            });
        }
    });

    $.extend($.ui.dockbar, {
        version: '0.1 beta'
    });

    $.widget('ui.dockarea', {
        options: {
            offsetCorrection: {
                x: 0,
                y: 0
            }
        },
        _create: function() {
            var self = this;
            self.element.addClass('ui-dockarea');

            self.windows = $([]);
        },
        resize: function() {
            var self = this;
            self.windows.window('onDockareaResize');
            self.windows.iwindow('onDockareaResize');
            self.windows.wizard('onDockareaResize');
        },
        register: function(window) {
            var self = this;
            self.windows = self.windows.add(window);
        },
        unregister: function(window) {
            var self = this;
            self.windows = self.windows.not(window.get(0));
        }
    });
    $.extend($.ui.dockarea, {
        version: '0.1 beta'
    });
})(jQuery);

/*
 * jQuery UI Tree Widget
 */

(function($) {
    $.widget('ui.tree', {
        options: {
            collapsed: false,
            action: false
        },
        _create: function() {
            var self = this;
            self.element.addClass('ui-tree');
            if ($.browser.mozilla) {
                self.element.css('-moz-user-select', 'none');
            }
            self.element.find('li').each(function(){
                var element = $(this);
                var title = element.find('>*:first-child');
                var isNode = element.is(':has(ul)');
                var isFirst = element.is(':first-child');
                var isLast = element.is(':last-child');
                var isSingle = isFirst && isLast;
                var baseClass = isNode ? 'ui-tree-node' : 'ui-tree-leaf';
                var classes = [
                'ui-tree-element',
                baseClass,
                ];
                if (isSingle) {
                    classes.push(baseClass + '-single');
                } else if (isFirst) {
                    classes.push(baseClass + '-first');
                } else if (isLast) {
                    classes.push(baseClass + '-last');
                }
                if (isNode) {
                    if (!self.options.collapsed) {
                        classes.push('ui-tree-node-hidden');
                        $('> ul', element).hide();
                    }
                    title.bind('click.ui-tree', function() {
                        $('> ul', element).toggle();
                        element.toggleClass('ui-tree-node-hidden');
                        return false;
                    });
                } else {
                    title.bind('click.ui-tree', function(event) {
                        if (self.options.action && typeof self.options.action == 'function') {
                            self.options.action.call(element.get(0), title.text(), typeof title.attr('href') != 'undefined' ? title.attr('href') : null)
                            return false;
                        } else {
                            return true;
                        }
                    });
                }
                element.addClass(classes.join(' '));
                title.addClass('ui-tree-element-title')
                .attr('title', title.text());
            });
        }
    });
    $.extend($.ui.tree, {
        version: '0.1 beta'
    });
})(jQuery);

/*
 * jQuery UI Button Widget
 * version 0.1
 */
(function($){
    $.widget('ui.button', {
        options: {
            width: 'auto',
            height: '24px',
            icon: false,
            iconSize: 24,
            showtext: true,
            disabled: false,
            before: function() {
                return true;
            },
            action: function() {
                return true;
            },
            after: function() {
                return true;
            }
        },
        _create: function() {
            var self = this;

            self.element
            .wrapInner('<div />')
            .addClass('ui-button ui-state-default ui-corner-all')
            .css({
                overflow: 'hidden',
                cursor: 'pointer',
                padding: '2px',
                width: self.options.width,
                height: self.options.height,
                lineHeight: self.options.height
            })
            .bind('mouseover.ui-button', function() {
                self.element.addClass('ui-state-hover');
                self.isEnable && self.element.addClass('ui-state-focus');
            })
            .bind('mousedown.ui-button', function() {
                self.isEnable && self.element.addClass('ui-state-active');
            })
            .bind('mouseup.ui-button', function() {
                self.element.hasClass('ui-state-active') && self.isEnable && self.trigger.call(self);
                self.element.removeClass('ui-state-active');
            })
            .bind('mouseout.ui-button', function() {
                self.element.removeClass('ui-state-hover ui-state-focus ui-state-active');
            });
            self.container = $('>div:first-child', self.element)
            if (self.options.icon) {
                self.container.css({
                    paddingLeft: self.options.iconSize + 2 + 'px',
                    paddingRight: '5px',
                    backgroundColor: 'transparent',
                    backgroundPosition: 'left center',
                    backgroundRepeat: 'no-repeat',
                    backgroundImage: 'url(' + self.options.icon + ')'
                });
            } else {
                self.container.css({
                    paddingLeft: '5px',
                    paddingRight: '5px'
                });
            }
            if (!self.options.showtext) {
                self.element.attr('title', self.container.text());
                self.container.text('');
                self.container.css({
                    padding: '0px',
                    width: self.options.iconSize + 'px',
                    height: self.options.iconSize + 'px'
                });
            }
            if (self.options.disabled) {
                self.disable();
            } else {
                self.enable();
            }
        },
        disable: function() {
            var self = this;
            self.element.removeClass('ui-state-focus ui-state-active').addClass('ui-state-disabled');
            self.isEnable = false;
        },
        enable: function() {
            var self = this;
            self.element.removeClass('ui-state-disabled');
            self.isEnable = true;
        },
        trigger: function() {
            var self = this;
            if (self.options.before.call(self.element) !== false &&
                self.options.action.call(self.element) !== false &&
                self.options.after.call(self.element) !== false
                ) {
                return true;
            }
            return false;
        }
    });
    $.extend($.ui.button, {
        version: '0.1 beta'
    })
})(jQuery);

/*
 * jQuery jqGridFilter widget 0.1
 *
 * register block with controls as filter for jqGrid
 */

(function($){
    $.widget('ui.jqGridFilter', {
        options: {
            grid: null,
            url: null,
            btnFilter: {
                show: true,
                title: 'Filter'
            },
            btnReset: {
                show: true,
                title: 'Reset'
            }
        },
        _create: function() {
            if (!this.options.btnFilter.show && !this.options.btnReset.show && !this.options.grid)
                return;
            var self = this;
            this.grid = $(this.options.grid);
            this.url = this.options.url ? this.options.url : this.grid.getGridParam('url');
            this.element.wrapInner('<div class="ui-jqgridfilter-edit" style="float: left" />');
            var buttons = $('<div class="ui-jqgridfilter-execute" style="float: right" />');
            var separator = false;
            if (this.options.btnFilter.show) {
                this.btnFilter = $('<input type="button" />');
                this.options.btnFilter.title && this.btnFilter.attr('value', this.options.btnFilter.title);
                this.btnFilter.click(function(){
                    self.submit.call(self);
                });
                buttons.append(this.btnFilter);
                separator = $('<span>&nbsp;</span>');
            }
            if (this.options.btnReset.show) {
                this.btnReset = $('<input type="reset" />');
                this.options.btnReset.title && this.btnReset.attr('value', this.options.btnReset.title);
                this.btnReset.click(function(){
                    self.reset.call(self);
                });
                separator && buttons.append(separator);
                buttons.append(this.btnReset);
            }
            this.element.prepend(buttons);
            this.element.submit(function(event){
                event.stopPropagation();
                event.preventDefault();
                return false;
            });
        },
        option: function(key, value) {
            var self = this;
            if (key === 'filters') {
                if (value === undefined) {
                    return self._getFilters();
                } else {
                    return self._setFilters(filters)
                }
            } else {
                return $.widget.prototype.option.call(self, key, value);
            }
        },
        _getFilters: function() {
            var data = {};
            $(':input').each(function() {
                if (!/^\S+=\S+$/.test($(this).serialize()))
                    return;
                data['filter[' + $(this).attr('name') + ']'] = $(this).val();
            });
            return data;
        },
        _setFilters: function(filters) {
            // no actions available
        },
        submit: function() {
            var self = this;
                data = self.option('filters');
            self.grid.setGridParam({
                url: self.url + (self.url.indexOf('?') < 0 ? '?' : '&') + $.param(data),
                page: 1
            }).trigger('reloadGrid');
        },
        reset: function() {
            var self = this;
            self.element.find('input[type=hidden]').val('');
            setTimeout(function(){
                self.submit.call(self);
            }, 1);
        }
    });

    $.extend($.ui.jqGridFilter, {
        version: '0.1'
    });
})(jQuery);


(function($){
    $.widget('ui.linkableField', {
        options: {
            popupPhrases: {
                ok: 'Ok',
                cancel: 'Cancel',
                select: 'Select',
                breakConnection: 'BreakConnection',
                noSelection: 'No row has been selected!',
                alert: 'Alert!',
                placeholder: '',
                getParams: {}
            },
            img: '/img/icons/16x16/link.gif',
            enabled: true
        },
        _create: function(){
            var self = (this),
                element = self.element,
                options = self.options;
            if (
                !element.is(':input') ||
                (typeof self.options.visibleParam == 'undefined' && typeof self.options.transferFunction == 'undefined') ||
                typeof self.options.basicUrl == 'undefined' ||
                typeof self.options.idHolder == 'undefined'
            ) {
               return;
            }
            element.addClass('ui-linkedfield');
            self.btn = $('<img />').attr({"src":options.img}).addClass('ui-linkedfield-btn').insertAfter(element);
            if (element.is('textarea')) {
                self.btn.css({"marginBottom": (self.btn.offset().top - element.offset().top).toFixed() + "px"});
            }
            self.holder = $(options.idHolder);
            self.buttons = new Object();
            self.buttons[options.popupPhrases.cancel] = function() {
                $(this).iwindow('destroy');
            };
            self.buttons[options.popupPhrases.select] = function() {
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
                    buttons[options.popupPhrases.ok] = function () {$(this).dialog('destroy').unbind().remove();};
                    $('<div />')
                        .html(options.popupPhrases.noSelection)
                        .dialog({
                            modal: true,
                            title: options.popupPhrases.alert,
                            buttons: buttons,
                            zIndex: $(this).parents('.ui-dialog').css('zIndex') * 1 + 1
                        });
                    return;
                }
                var data = $$('#list').getRowData(newId);
                options.accept && $.isFunction(options.accept) && options.accept(data);
                self.holder.val(newId).change();
                if (options.transferFunction != undefined) {
                    element.val(options.transferFunction(data));
                } else if (typeof options.visibleParam == "string") {
                    element.val(data[options.visibleParam]);
                } else {
                    var valHolderValue = new Array();
                    for (var i=0, j=options.visibleParam.length; i < j; i++) {
                        if (data[options.visibleParam[i]] != undefined) {
                            valHolderValue.push(data[options.visibleParam[i]]);
                        }
                    }
                    if(options.visibleParamSeparator != undefined)
                        element.val(valHolderValue.join(options.visibleParamSeparator));
                    else
                        element.val(valHolderValue.join(' '));
                }
                $(this).iwindow('destroy');
            };
            self.buttons[options.popupPhrases.breakConnection] = function() {
                self.breakConnection();
                $(this).iwindow('destroy');
            };

            self.onBtnClickHandler = function() {
                var getParams = {};
                if (options.getParams) {
                    for (var i in options.getParams) {
                        if ($.isFunction(options.getParams[i])) {
                            getParams[i] = options.getParams[i]();
                        } else {
                            getParams[i] = options.getParams[i];
                        }
                    }
                }

                $('<div />').iwindow({
                    modal: true,
                    title: $('label[for="' + element.attr('id') + '"]').text(),
                    url: options.basicUrl + '/items' + (getParams ? ("?" + $.param(getParams)) : ""),
                    maximized: false,
                    bubble: false,
                    buttons: self.buttons
                }).parent().css('zIndex', '5000');
            };
            self._setEnabled(false);
            if(options.enabled) {
                self.enable();
            }
            self.btn.bind('click', function(){
                if(self._isEnabled()) {
                   self.onBtnClickHandler.call(self);
                }
            });
        },
        destroy: function() {
            var self = this,
            element = self.element;
            element.removeClass('ui-linkedfield').unbind('click');
            self.btn.unbind().remove();
        },
        _isEnabled: function() {
            return this._isenabled;
        },
        _setEnabled: function(flag) {
            this._isenabled = flag;
        },
        enable: function() {
            var self = this;
            if(!self._isEnabled()){
                self.btn.css({"cursor":"pointer"})
                        .removeClass("ui-state-disabled");
                self._setEnabled(true);
            }
        },
        disable: function() {
            var self = this;
            if(self._isEnabled()) {
                self.btn.css({"cursor":"default"})
                        .addClass("ui-state-disabled");
                self._setEnabled(false);
            }
        },
        breakConnection: function(){
            var self = this,
                options = self.options;
            self.holder.val('').change();
            if(options.unlink && $.isFunction(options.unlink)) options.unlink();
            self.element.val(options.popupPhrases.placeholder);
        }
    });

    $.extend($.ui.linkableField, {
        version: '0.1 beta'
    });

})(jQuery);