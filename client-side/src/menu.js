/* ******************************** menu.js ********************************* */

$(document).ready(function () {

    var time = 300;

    $('.menu-container dt a').click(function () {
        var parent = $(this).closest('dl');

        parent.children('dd').slideToggle(time, function () {
            $(this).toggleClass('itemHidden');
        });

        parent.children('dt').toggleClass('itemHidden');
    });
});