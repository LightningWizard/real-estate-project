;(function($){
    AppLibValidation = $.extend(AppLibValidation, {
        errorMessages: {
            isEmpty       : "Значення не може бути пустим",
            notDigit      : "Значення '%value%' повинно містити лише цифрові символи",
            toShot        : "'%value%' менше дозволеної мінімальної довжини в %min% символів",
            toLong        : "'%value%' більше дозволеної максимальної довжини в %max% символів",
            notDate       : "'%value%' не є коректною датою",
            notEqual      : "'%value%' не дорівнює %etalon%",
            notNumeric    : "'%value%' повинно бути числом",
            notDecimal    : "'%value%' повинно бути числом з точністю %size% і масштабом %scale%"
        }
    });
})(jQuery);