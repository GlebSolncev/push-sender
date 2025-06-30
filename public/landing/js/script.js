function getCookie(name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ))
    return matches ? decodeURIComponent(matches[1]) : undefined
}

function randRange(data) {
       var newTime = data[Math.floor(data.length * Math.random())];
       return newTime;
}

function lastpack(numpack) {
    var minNumPack = 3; // Минимальное количество упаковок
    var lastClass = $('.lastpack'); // Объект
    var numpackCookie = getCookie("lastpack");
    var timeArray = new Array(2000, 13000, 15000, 7000, 6000, 11000);

    if(numpackCookie == undefined) {
        document.cookie = numpack;
    } else {
        var numpack =  numpackCookie;
    }
    
    if (numpack > minNumPack) {
        numpack--;
        document.cookie = "lastpack="+numpack;
        lastClass.text(numpack);   
    } else {
        lastClass.text(minNumPack);
    }
    clearInterval(timer);
    timer = setInterval(lastpack, randRange(timeArray), numpack);
}

var timer = setInterval(lastpack, 0, 40);



$(document).ready(function() {

    $('[name="country"]').on('change', function() {
        var geoKey = $(this).find('option:selected').val();
        var data = $jsonData.prices[geoKey];
        var price = data.price;
        var oldPrice = data.old_price;
        var currency = data.currency
        $("[value = "+geoKey+"]").attr("selected", true).siblings().attr('selected', false);

        $('.price_land_s1').text(price);
        $('.price_land_s2').text(oldPrice);
        $('.price_land_curr').text(currency);
    });


    $('.scrollTo').click( function(){
        var scroll_el = $(this).attr('href');
        if ($(scroll_el).length != 0) {
            $('html, body').animate({ scrollTop: $(scroll_el).offset().top }, 500);
        }
        return false;
    });

});





$(document).ready(function (e) {
    $('.slide').css({
        "position": "absolute"
        , "top": '0'
        , "left": '0'
    }).hide().eq(0).show();
    var slNeedLinks = true;
    var slideNum = 0; //счетчик, номер активного слайда
    var slideTime;
    var slTimeOut = 6000;
    var slSpead = 600;
    slideCount = $("#slider .slide").size();
    var animSlide = function (arrow) { // Основная функция, логика нашего слайдера
        clearTimeout(slideTime);
        $('.slide').eq(slideNum).fadeOut(slSpead);
        if (arrow == "next") {
            if (slideNum == (slideCount - 1)) {
                slideNum = 0;
            } else {
                slideNum++
            }
        } else if (arrow == "prew") {
            if (slideNum == 0) {
                slideNum = slideCount - 1;
            } else {
                slideNum -= 1
            }
        } else {
            slideNum = arrow;
        }
        $('.slide').eq(slideNum).fadeIn(slSpead, rotator);
        $(".control-slide.active").removeClass("active");
        $('.control-slide').eq(slideNum).addClass('active');
    };
    if (slNeedLinks) {
        var $linkArrow = $('<button id="prewbutton"></button><button id="nextbutton"></button>')
            .prependTo('#slider');
        $('#nextbutton').click(function () {
            animSlide("next");

        });
        $('#prewbutton').click(function () {
            animSlide("prew");
        })
    }
    var $adderSpan = '';
    $('.slide').each(function (index) {
        $adderSpan += '<span class = "control-slide">' + index + '</span>';
    });
    $('<div class ="sli-links">' + $adderSpan + '</div>').appendTo('#slider-wrap');
    $(".control-slide:first").addClass("active");

    $('.control-slide').click(function () {
        var goToNum = parseFloat($(this).text());
        animSlide(goToNum);
    });
    var pause = false; //отвечает за остановку слайдера, если user навел курсор на слайд
    var rotator = function () {
        if (!pause) {
            slideTime = setTimeout(function () {
                animSlide('next')
            }, slTimeOut);
        }
    };
    $('#slider-wrap').hover(
        function () {
            clearTimeout(slideTime);
            pause = true;
        }
        , function () {
            pause = false;
            rotator();
        });
    rotator();
});