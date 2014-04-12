;(function($){
    $.appValidation = $.appValidation || {};

    $.extend($.appValidation, {
        errorMessages: {
            isEmpty       : "value is empty",
            notDigit      : "'%value%' contains not only digit characters",
            toShot        : "'%value%' is less than %min% characters long",
            toLong        : "'%value%' is greater than %max% characters long",
            notDate       : "'%value%' does not appear to be a valid date",
            notEqual      : "'%value%'  is not equal %etalon%",
            notNumeric    : "'%value%' is not numeric",
            notMatch      : "'%value%' is not match with pattern"
        },
        prepareErrorMessage: function(template) {
            var message = template,
                pattern = /%\w+%/g,
                result = message.match(pattern);
            if(result) {
                for(var i = 0, j = result.length; i < j; i++) {
                    message = message.replace(result[i], arguments[i + 1]);
                }
            }
            return message;
        },
        notEmpty: function() {
            var isValid = true, ts = this,
                el = $(this), errorMessage, elVal = el.val();

            el.setErrorTracker();
            if(elVal.length < 1 || /^\s+$/.test(elVal)){
                isValid = false;
                errorMessage = $.appValidation.errorMessages.isEmpty;
                el.setErrorTracker('setMessage', errorMessage);
            } else {
                el.setErrorTracker('resetMessages');
                isValid = true;
            }
            return isValid;
        },
        regExp: function(pattern, required, errorMessage){
            var isValid = true, ts = this,
                el = $(this), elVal = el.val(), notEmpty = true;
            if(required){
                notEmpty = $.appValidation.notEmpty.call(ts);
                if(!notEmpty) { return false;}
            }
            if(!required && !elVal) {return true;}
            el.setErrorTracker();
            if(pattern.test(elVal)){
                isValid = true;
                el.setErrorTracker('resetMessages');
            } else {
                errorMessage = errorMessage || $.appValidation.errorMessages.notMatch;
                errorMessage = $.appValidation.prepareErrorMessage(errorMessage, elVal);
                el.setErrorTracker('setMessage', errorMessage);
                isValid = false;
            }
            return isValid;
        },
        digits: function(required, errorMessage){
            var isValid = true, ts = this,
                el = $(this), elVal = el.val(), notEmpty = true;
            if(required){
                notEmpty = $.appValidation.notEmpty.call(ts);
            }
            if(!notEmpty) { return false;}
            el.setErrorTracker();
            errorMessage = errorMessage || $.appValidation.errorMessages.notDigit;
            return $.appValidation.regExp.call(ts, /^\d+$/, required, errorMessage);
        }
    });

    $.extend($.appValidation, {
        getAccessor : function(obj, expr) {
            var ret = [];
            if(typeof expr === 'function') {
                return expr(obj);
            }
            ret = obj[expr];
            return ret;
	},
        validate: function(){
            var isValid = false;
            this.each(function(){
                var ts = this,
                    validateFunction = ts.jqSimpleValidator.validatefunc;
                isValid = validateFunction.call(ts);
            });
            return isValid;
        }

    });

    $.fn.jqSimpleValidator = function(options) {
        if(typeof options == 'string') {
            var fn = $.appValidation.getAccessor($.fn.jqSimpleValidator,options);
            if (!fn) {
                 throw ("jqSimpleValidator - No such method: " + options);
            }
            var args = $.makeArray(arguments).slice(1);
	    return fn.apply(this, args);
        }
        return $(this).each(function(){
            if(this.jqSimpleValidator)
                return;
            var ts = this,
                element = $(this);

            options = $.extend({},{validatefunc: null}, options);
            if(!element.is(':input') && !element.is('textarea')){ return;}
            if(!$.isFunction(options.validatefunc)){ return;}
            var elementContainer = element.parents('div.form-element-item:eq(0)');
            if(!elementContainer.length)
                return;
            ts.jqSimpleValidator = {validatefunc: options.validatefunc};
            if(options.event){
                element.bind(options.event, function(){ ts.jqSimpleValidator.validatefunc.apply(ts, arguments)});
            }

        });
    }

    $.extend($.fn.jqSimpleValidator, {
        validate: $.appValidation.validate
    });

})(jQuery)