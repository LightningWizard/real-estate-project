var AppLibValidation = new Object();
AppLibValidation.errorMessages = {
    isEmpty       : "value is empty",
    notDigit      : "'%value%' contains not only digit characters",
    toShot        : "'%value%' is less than %min% characters long",
    toLong        : "'%value%' is greater than %max% characters long",
    notDate       : "'%value%' does not appear to be a valid date",
    notEqual      : "%value%  is not equal %etalon%",
    notNumeric    : "'%value%' is not numeric"
};
AppLibValidation.quoteStringParam = function(stringParam){
    stringParam = stringParam.replace("\"", "\\\"");
    return '\"' + stringParam + '\"';
};
AppLibValidation.createValidator = function(validatorParams) {
    var validator;
    for (var i = 0, j = validatorParams.length; i < j; i++) {
        if(typeof validatorParams[i] == 'string'){
            validatorParams[i] = this.quoteStringParam(validatorParams[i]);
        }
    }
    var argumentsList = validatorParams.join(",");
    eval('validator = new AppLibValidation.Validator(' + argumentsList + ');');
    return validator;

};
AppLibValidation.createErrorMessage = function(template) {
    var message = template,
        pattern = /%\w+%/g,
        result = message.match(pattern);
    for(var i = 0, j = result.length; i < j; i++) {
        message = message.replace(result[i], arguments[i + 1]);
    }
    return message;
};
AppLibValidation.notEmpty = function() {
    var isValid = true, errorMessage;
    if(this.value.length < 1 || /^\s+$/.test(this.value)){
        isValid = false;
        errorMessage = AppLibValidation.errorMessages.isEmpty;
        this.setErrorMessage(errorMessage);
    } else {
        isValid = true;
    }
    return isValid;
};
AppLibValidation.digits = function(){
    var isValid = true, errorMessage;
    if(this.required && this.value.length < 1){
        isValid = false;
        errorMessage = AppLibValidation.errorMessages.isEmpty;
        this.setErrorMessage(errorMessage);
    } else {
        if (/^\d*$/.test(this.value)) {
            isValid = true;
        } else {
            isValid = false;
            errorMessage = AppLibValidation.createErrorMessage(AppLibValidation.errorMessages.notDigit, this.value);
            this.setErrorMessage(errorMessage);
        }
    }
    return isValid;
};

AppLibValidation.equal = function(value){
    var isValid = true, errorMessage;
    if(this.required && this.value.length < 1){
        isValid = false;
        this.setErrorMessage(AppLibValidation.errorMessages.isEmpty);
    } else {
        if (this.value == value) {
            isValid = true;
        } else {
            isValid = false;
            errorMessage = AppLibValidation.createErrorMessage(AppLibValidation.errorMessages.notEqual, this.value, value);
            this.setErrorMessage(errorMessage);
        }
    }
    return isValid;
};

AppLibValidation.stringLength = function(min, max){
    var errorMessage;
    if(this.required && !AppLibValidation.notEmpty.apply(this)) {
        return false;
    }
    else if(this.value.length < min) {
        errorMessage = AppLibValidation.createErrorMessage(AppLibValidation.errorMessages.toShot, this.value, min);
        this.errorMessages.push(errorMessage);
        return false;
    } else if (this.value.length > max) {
        errorMessage = AppLibValidation.createErrorMessage(AppLibValidation.errorMessages.toLong, this.value, max);
        this.errorMessages.push(errorMessage);
        return false;
    } else {
        return true;
    }
};

AppLibValidation.date = function(){
    var isValid = true, errorMessage;
    if(this.required && this.value.length<1){
        isValid = false;
        this.setErrorMessage(AppLibValidation.errorMessages.isEmpty);
    } else if(this.value.length < 1){
        isValid = true;
    } else {
        var reg = new RegExp('^([0-9]{1,2})\.([0-9]{2})\.([0-9]{4})$');
        var rez = reg.exec(this.value);
        if(null!==rez){
            var day = parseInt(rez[1],10);
            var mon = parseInt(rez[2],10);
            var year = parseInt(rez[3],10);
            var days = [0,31, AppLibValidation.daysInFebruary(year),31,30,31,30,31,31,30,31,30,31];
            if((mon>=1 && mon<=12) && (day>=1 && day<=days[mon])){
                isValid = true;
            } else {
                isValid = false;
                errorMessage = AppLibValidation.createErrorMessage(AppLibValidation.errorMessages.notDate, this.value);
                this.setErrorMessage(errorMessage);
            }
        } else {
            isValid = false;
            errorMessage = AppLibValidation.createErrorMessage(AppLibValidation.errorMessages.notDate, this.value);
            this.setErrorMessage(errorMessage);
        }
    }
    return isValid;
};

AppLibValidation.daysInFebruary = function(year){
    return (((year%4==0) && ( (!(year%100==0)) || (year%400==0))) ? 29 : 28 );
};

AppLibValidation.numeric = function(){
    var isValid = true, errorMessage;
    if(this.required && this.value.length<1){
        isValid = false;
    } else {
        if (/^$|^\d+(?:,\d+)?$/.test(this.value)) {
            isValid = true;
        } else {
            isValid = false;
            errorMessage = AppLibValidation.createErrorMessage(AppLibValidation.errorMessages.notNumeric, this.value);
            this.setErrorMessage(errorMessage);
        }
    }
    return isValid;
};

AppLibValidation.decimal = function(size, scale) {
    var isValid = true, errorMessage;
    if(this.required && this.value.length < 1){
        isValid = false;
        this.setErrorMessage(AppLibValidation.errorMessages.isEmpty);
    } else {
        if(size > 0 && (size - scale) > 0){
            var regstr;
            if(scale > 0)
                regstr = '(^$|^-?[0-9]{1,' + (size-scale) +'}(|[,.][0-9]{1,' + scale + '})$)';
            else
                regstr = '(^$|^-?[0-9]{1,' + (size-scale) + '}$)';
            var reg = new RegExp(regstr);
            if(reg.test(this.value)){
                isValid = true;
            } else {
                errorMessage = AppLibValidation.createErrorMessage(AppLibValidation.errorMessages.notDecimal, this.value, size, scale);
                this.setErrorMessage(errorMessage);
                isValid = false;

            }
        } else{
            errorMessage = AppLibValidation.createErrorMessage(AppLibValidation.errorMessages.notDecimal, this.value, size, scale);
            this.setErrorMessage(errorMessage);
            isValid = false;
        }
    }
    return isValid;
};

AppLibValidation.Validator = function(type, required) {
    this.required = required;
    this.validatorType = type;
    this.validatorParams = new Array();
    this.errorMessages = new Array();
    for(var param in arguments) {
        if(param > 1) {
            this.validatorParams.push(arguments[param]);
        }
    }
};

AppLibValidation.Validator.prototype.isValid = function(){
    this.errorMessages = new Array();
    return  AppLibValidation[this.validatorType].apply(this, this.validatorParams);
};
AppLibValidation.Validator.prototype.setValueRequired = function(required){
    this.requred = required;
};
AppLibValidation.Validator.prototype.setValue = function(value){
    this.value = value;
};
AppLibValidation.Validator.prototype.setErrorMessage = function(message){
    this.errorMessages.push(message);
};
AppLibValidation.Validator.prototype.getErrorMessages = function(){
    return this.errorMessages;
};


AppLibValidation.FormElementValidator = function(formElement, validatorType, required){
    this.validators = new Object();
    this.errorMessages = new Array();
    var validator, validatorParams = [];
    if(formElement != undefined) {
        this.setFormElement(formElement);
    }
    for(var param in arguments) {
        if(param > 0){
            validatorParams.push(arguments[param]);
        }
    }
    if(validatorType != undefined && required != undefined) {
        validator = AppLibValidation.createValidator(validatorParams);
        this.addValidatorObject(validator);
    }
};
AppLibValidation.FormElementValidator.prototype.addValidatorObject = function(validator) {
    if(validator instanceof AppLibValidation.Validator) {
        this.validators[validator.validatorType] = validator;
    }
}
AppLibValidation.FormElementValidator.prototype.setFormElement = function(formElement) {
    if(typeof formElement == 'string'){
        formElement = $(formElement)
    }
    if(formElement.is(':input') || formElement.is('textarea')){
        this.formElement = formElement;
    }
}
AppLibValidation.FormElementValidator.prototype.isValid = function() {
    var isValid = true,
    errorMessages;
    this.errorMessages = new Array();
    for(var validator in this.validators) {
        this.validators[validator].setValue(this.formElement.val());
        if(!this.validators[validator].isValid()){
            isValid = false;
            errorMessages = this.validators[validator].getErrorMessages();
            for(var i = 0, j = errorMessages.length; i < j; i++){
                this.errorMessages.push(errorMessages[i]);
            }
        }
    }
    return isValid;
};
AppLibValidation.FormElementValidator.prototype.getErrors = function(){
    return this.errorMessages;
};
AppLibValidation.FormElementValidator.prototype.setErrorsTracker = function(){
    if(this.errorTracker == undefined) {
        if(this.formElement.siblings(".form-element-errors").length == 0){
            this.errorTracker = $('<ul />').addClass('form-element-errors');
            var siblingsElement = this.formElement.siblings(':last').not(':input');
            if(siblingsElement.length > 0) {
                this.errorTracker.insertAfter(siblingsElement);
                this.formElement.parent().append(this.errorTracker);
            } else {
                this.errorTracker.insertAfter(this.formElement);
            }
        } else {
            this.errorTracker =  this.formElement.siblings(".form-element-errors");
        }
    } else {
        this.errorTracker.find('li').remove();
    }
    for(var i = 0, j = this.errorMessages.length; i < j; i++){
        this.errorTracker.append(
            $('<li />').text(this.errorMessages[i])
        );
    }
};
AppLibValidation.FormElementValidator.prototype.resetErrorsTracker = function() {
    if(this.errorTracker){
        this.errorTracker.find('li').remove();
    } else {
        var errorTracker = this.formElement.siblings(".form-element-errors");
        if(errorTracker.length > 0) {
            this.errorTracker = errorTracker;
            this.errorTracker.find('li').remove();
        }
    }
};
AppLibValidation.FormElementValidator.prototype.validate = function(){
    var isValid = true;
    this.resetErrorsTracker();
    isValid = this.isValid();
    if(!isValid){
        this.setErrorsTracker();
    }
    return isValid;
};

AppLibValidation.FormValidator = function() {
    this.formElementsValidators = new Object();
};
AppLibValidation.FormValidator.prototype.addElementValidator = function(formElement, validatorType, required) {
    var validator, formElementId, formElementValidator, validatorParams = new Array();
    for(var param in arguments) {
        if(param > 0) {
            validatorParams.push(arguments[param]);
        }
    }
    validator = AppLibValidation.createValidator(validatorParams);
    formElementId = formElement;
    if(typeof formElement == 'object'){
        formElementId = formElement.attr('id');
    }
    if(!(formElementId in this.formElementsValidators)) {
        formElementValidator = new AppLibValidation.FormElementValidator(formElement);
        formElementValidator.addValidatorObject(validator);
        this.formElementsValidators[formElementId] = formElementValidator;
    } else {
        this.formElementsValidators[formElementId].addValidatorObject(validator);
    }
};
AppLibValidation.FormValidator.prototype.addElementValidatorObject = function(elValidator) {
    if(elValidator instanceof AppLibValidation.FormElementValidator){
        var formElementId = elValidator.formElement.attr('id');
        if(!(formElementId in this.formElementsValidators)){
            this.formElementsValidators[formElementId] = elValidator;
        } else {
            for (var validator in elValidator.validators){
                this.formElementsValidators[formElementId].addValidatorObject(validator);
            }
        }
    }
};
AppLibValidation.FormValidator.prototype.validate = function(){
    var isValid = true, isValidElement
    for (var element in this.formElementsValidators){
        isValidElement = this.formElementsValidators[element].validate();
        isValid = isValid && isValidElement;
    }
    return isValid;
};

AppLibValidation.FormValidator.prototype.resetErrorsTrackers = function() {
    for (var element in this.formElementsValidators){
        this.formElementsValidators[element].resetErrorsTracker();
    }
}