(function ($, window) {
    if (window.jQuery === undefined) {
        console.error('Plugin "jQuery" required by "menu.js" is missing!');
        return;
    }

    $(document).ready(function () {

        var time = 300;

        $('.menu-container dt a').click(function () {
            var parent = $(this).closest('dl');

            parent.children('dd').slideToggle(time, function () {
                $(this).toggleClass('menu-group-hidden');
            });

            parent.children('dt').toggleClass('menu-group-hidden');
        });
    });

})(jQuery, window);