;(function($){
    $.errortracking = $.errortracking || {};

    $.extend($.errortracking, {
        getAccessor : function(obj, expr) {
            var ret = [];
            if(typeof expr === 'function') {
                return expr(obj);
            }
            ret = obj[expr];
            return ret;
	},
        __getErrorTracker: function() {
            var self = $(this),
                errorTracker = self.siblings('ul.form-element-errors');
            if(errorTracker.length){
                return errorTracker;
            }
            return null;
        },
        addMessage: function(message) {
            this.each(function(){
                 this.errorTracker.append($('<li />').text(message));
            });
            return $(this);
        },
        resetMessages: function(){
            this.each(function(){
                 this.errorTracker.children().remove();
            });
            return $(this);
        },
        setMessage: function(message) {
            $(this).setErrorTracker('resetMessages');
            $(this).setErrorTracker('addMessage', message);
            return $(this);
        }
    });

    $.fn.setErrorTracker = function(options) {
        if(typeof options == 'string') {
            var fn = $.errortracking.getAccessor($.fn.setErrorTracker,options);
            if (!fn) {
                 throw ("setErrorTracker - No such method: " + options);
            }
            var args = $.makeArray(arguments).slice(1);
	    return fn.apply(this, args);
        }
        return $(this).each(function(){
            if(this.errorTracker)
                return;
            var ts = this,
                element = $(this);

            options = options || {};
            if(!element.is(':input') && !element.is('textarea')){
                return;
            }

            var elementContainer = element.parents('div.form-element-item:eq(0)');
            if(!elementContainer.length)
                return;
            var errorTracker = $.errortracking.__getErrorTracker.call(ts);
            if(!errorTracker) {
                errorTracker = $('<ul />').addClass('form-element-errors');
                elementContainer.append(errorTracker);
            }
            ts.errorTracker = errorTracker;
            if(options.message) {
                element.setErrorTracker('addMessage', options.message);
            }
        });
    }

    $.extend($.fn.setErrorTracker, {
        addMessage: $.errortracking.addMessage,
        setMessage: $.errortracking.setMessage,
        resetMessages: $.errortracking.resetMessages,
    });
})(jQuery)