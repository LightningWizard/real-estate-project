;(function($){
    $.extend($.appValidation, {
        errorMessages: {
            isEmpty       : "Значение не может быть пустым",
            notDigit      : "Значение '%value%' должно содержать только цифровые символы",
            toShot        : "'%value%' меньше разрешенной минимальной длины в %min% символов",
            toLong        : "'%value%' больше разрешенной максимальной длины в %max% символов",
            notDate       : "'%value%' не является корректной датой",
            notEqual      : "'%value%' не равно %etalon%",
            notNumeric    : "'%value%' должно быть числом",
            notDecimal    : "'%value%' должно быть числом с точностью %size% и масштабом %scale%",
            notMatch      : "'%value%' не соответствует шаблону"
        }
    });
})(jQuery);


