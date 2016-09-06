(function ($, window) {
    if (window.jQuery === undefined) {
        console.error('Plugin "jQuery" required by "Menu.js" is missing!');
        return;
    }

    $(document).ready(function () {

        var time = 300;

        $('.Menu-container dt a').click(function () {
            var parent = $(this).closest('dl');

            parent.children('dd').slideToggle(time, function () {
                $(this).toggleClass('Menu-group-hidden');
            });

            parent.children('dt').toggleClass('Menu-group-hidden');
        });
    });

})(jQuery, window);