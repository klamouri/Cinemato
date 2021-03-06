// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

(function($) {
    $.fn.toggleDisabled = function () {
        return this.each(function () {
            return this.disabled = !this.disabled;
        });
    };

    $('.datetimepicker', 'form').each(function (datetimepicker) {
        $(this).datetimepicker();
    });
    $('.datepicker', 'form').each(function (datepicker) {
        console.log(this, datepicker);
        $(this).datetimepicker({
            pickTime: false
        });
    });
    $('.timepicker', 'form').each(function (timepicker) {
        console.log(this, timepicker);
        $(this).datetimepicker({
            pickDate: false
        });
    });

    $('.confirmation-modal-submit').on('click', function (e) {
        if ($($(this).data('form')).length > 0) {
            console.log($($(this).data('form')));
            $($(this).data('form')).submit();
        }
    })
})(jQuery);
