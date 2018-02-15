$(document).ready(function(){
    menumore();
    var page = window.location.hash;
    $('#menu > a').each(function(){
        if ($(this).attr('href') == page) {
            $(this).addClass('active');
        } else {
            $(this).removeClass('active');
        }
    });
    $('#menu > a').click(function(){
        $('.account > .menu > a').each(function(){
            $(this).removeClass('active');
        });
        $(this).addClass('active');
    });
    $('#menu > #more').click(function(){
        toggleMenu();
    });
    $('input[type=file]').change(function(){
        updateFileInput($(this));
    });
    $('.optionbox > div button').click(function(){profileblocks($(this))});
});
$(window).resize(function(){
    menumore();
});
function toggleMenu() {
    if ($('#menu').hasClass('more')) {
        closemenu();
    } else {
        openmenu();
    }
    height = $('#menu').height() + 'px';
    $('#more a').css({'height': height});
    $('#more span').css({'line-height': height});
}
function closemenu() {
    if ($('#menu').hasClass('more')) {
        $('#more span').html('More');
        $('#menu').removeClass('more');
    }            
}
function openmenu() {
    if (!$('#menu').hasClass('more')) {
        $('#more span').html('Less');
        $('#menu').addClass('more');
    }
}
function menumore() {
    closemenu();
    initoffset = $('#menu > .flex > a:first-of-type').offset();
    inittop = initoffset.top;
    more = false;
    $('#menu > .flex > a').each(function(){
        offset = $(this).offset();
        if (offset.top > inittop) {
            if (!$('#menu > #more').hasClass()) {
                $('#menu > #more').addClass('overflowed');
            }
            more = true;
        }
    });
    if (!more) {
        $('#menu > #more').removeClass('overflowed');
    }            
}
function updateFileInput(el) {
    el.prev().html(el.val().replace(/^.*[\\\/]/, ''));
}
function profileblocks(button) {
    if (button.hasClass('submit')) {
        button.parent('form').submit();
    } else if (button.hasClass('close')) {
        submit = button.prev();
        input = submit.prev().prev();
        content = input.prev();
        
        button.addClass('hidden');
        input.addClass('hidden');
        content.removeClass('hidden');
        
        submit.removeClass('submit').text(submit.attr('orig'));
    } else {
        close = button.next();
        input = button.prev().prev();
        content = input.prev();
        close.removeClass('hidden');
        input.removeClass('hidden');
        content.addClass('hidden');
        button.addClass('submit').text('Submit');
        close.css('left', button.outerWidth(true) + 16);
    }
}