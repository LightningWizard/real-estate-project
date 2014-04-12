;(function($){
    $.contragentSearchUtil  = $.contragentSearchUtil || {};

    $.extend($.contragentSearchUtil, {
        _search: function(){
            var ts = this,
                el = $(this),
                searchParams = ts.searchParams,
                searchUrl = searchParams.url,
                phoneNumber = $.trim(el.val());
            if(!searchUrl){throw new Error('Undefined search param - url');}
            $.ajax({
                dataType: 'json',
                data:{
                    phone: phoneNumber
                },
                url: searchUrl,
                beforeSend: function(){
                    el.parents('body')
                        .prepend(searchParams.loadIndicator);
                },
                complete: function(){
                    searchParams.loadIndicator.remove();
                },
                success: function(data){
                    $.contragentSearchUtil._onLoadContragentSuccess.call(ts, data);
                }
            });
        },
        _onLoadContragentSuccess: function(data){
            var ts = this,
                controlFields = ts.searchParams.controlFields;
            for(var i in controlFields) {
                var itemControlField = controlFields[i],
                    elVal = data[i];
                if(i == 'phones'){
                    var phonesData = data[i],
                        phones = new Array();
                    for(var m in phonesData){
                        phones.push(phonesData[m]);
                    }
                    elVal = phones;
                }
                itemControlField.val(elVal ? elVal : '');
            }

        }
    });
    $.fn.searchContragentField = function(options) {
        if(this.searchParams){
            return;
        }

        return this.each(function(){
            var ts = this,
                el = $(this),
                loadIndicator = $('<div />').addClass('ui-dialog-loader');
                options = options || {};
                var searchParams = $.extend({}, options, {
                    loadIndicator: loadIndicator
                }),
                wrapper = $('<div />').css('position', 'relative'),
                btnSearch = $('<div />').addClass('ui-state-default ui-corner-all').append(
                              $('<span />').addClass('ui-icon ui-icon-search')
                          );
            el.wrap(wrapper).css({'width': '96%','padding': '.3em'});
            var container = el.parent();
            container.append(btnSearch.hide())
                .bind('mouseover', function(){
                    btnSearch.show();
                })
                .bind('mouseout', function() {
                    btnSearch.hide();
                });

            btnSearch.css({'cursor': 'pointer', 'position': 'absolute', 'top':'3px','right':'1px'})
                .addClass('ui-state-disabled')
                .bind('mouseover', function() {
                    if(!$(this).hasClass('ui-state-disabled')) {
                        $(this).addClass('ui-state-hover');
                        $(this).addClass('ui-state-focus');
                    }
                })
                .bind('mousedown', function() {
                    if(!$(this).hasClass('ui-state-disabled')){
                      $(this).addClass('ui-state-active');
                    }
                })
                .bind('mouseup', function() {
                    if(!$(this).hasClass('ui-state-disabled')){
                        $(this).removeClass('ui-state-active');
                        $.contragentSearchUtil._search.call(ts);
                    }
                })
                .bind('mouseout', function() {
                    $(this).removeClass('ui-state-hover ui-state-focus ui-state-active');
                });
            ts.searchParams = searchParams;
            var onSearchValueChange = function(){
                var self = $(this),
                    elVal = self.val();
                if(ts.keyUpTimer) {
                    clearTimeout(ts.keyUpTimer);
                    delete ts.keyUpTimer;
                }
                if(elVal && elVal.length > 2) {
                    btnSearch.removeClass('ui-state-disabled');
                } else {
                    if(!btnSearch.hasClass('ui-state-disabled'))
                        btnSearch.addClass('ui-state-disabled');
                }
            }
            el.keyup(function(){
                if(ts.keyUpTimer) {
                    clearTimeout(ts.keyUpTimer);
                    delete ts.keyUpTimer;
                }
                ts.keyUpTimer = setTimeout(onSearchValueChange.call(ts), 500);
            })
        });
    }
})(jQuery);