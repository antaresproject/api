$(document).ready(function () {
    $('.switch-api').on('change', function () {
        return $(this).closest('form').submit();
    });
    $('.api-auths').on('ifChecked', function () {
        $(this).closest('fieldset').next().find('input:checkbox').iCheck('check');

    });
    $('.api-auths').on('ifUnchecked', function () {
        $(this).closest('fieldset').next().find('input:checkbox').iCheck('uncheck');
    });
});