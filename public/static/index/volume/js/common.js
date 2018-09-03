
$(document).ready(function () {
    var boxHeight = $("#inner li img").height();
    $("#inner").css("height", boxHeight);
    $(window).resize(function () {
        var boxHeight = $("#inner li img").height();
        $("#inner").height(boxHeight);
    });
});

$(document).ready(function(){
    $(".head-content .right").click(function(){
        $(".bg-weixin").slideToggle();
    });
    $("#weixin-code .close-tag i").click(function(){
        $(".bg-weixin").hide();
    });
});

$(document).ready(function(){
    $("#bottom-nav .service").click(function(){
        $(".bg-service").slideToggle();
    });
    $("#weixin-service .close-tag i").click(function(){
        $(".bg-service").hide();
    });
});

$(document).ready(function(){
    $("#top-nav li").click(function(){
        $(this).addClass("active").siblings().removeClass("active");
    });

});

$(document).ready(function(){
    $("#top-nav li:last-child").click(function(){
        $(".all-goods-classify").slideToggle();
        $(this).siblings().click(function(){
            $(".all-goods-classify").hide();
        });
    });
});


$(document).ready(function(){
    $(window).scroll(function(){
        if($(window).scrollTop() > 1000){
            $(".go-top").show();
        }else{
            $(".go-top").hide();
        }
    });
    $(".go-top").click(function(){
        $('html,body').animate({scrollTop:"0px"},500);
    });
});

$(document).ready(function(){
    $("#bottom-nav ul>li:last-child").click(function(){
        $(".bg-member").slideToggle();
    });
    $("#member-code .close-tag i").click(function(){
        $(".bg-member").hide();
    });
});


$(document).ready(function(){
    $("#goods-pic-text").click(function(){
        $(this).children("i").toggleClass("fa-angle-down");
        $("#goods-pic-detail").slideToggle("slow");
    });
});

