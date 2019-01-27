$("button").click(function()
{
    $("#KencefLogo").toggleClass('small');
    
    $(".KencefButton").each(function()
    {
        $(this).toggleClass('small');
    });

    $("#LogoContainer").toggleClass('col-12');
    $("#LogoContainer").toggleClass('col-3');

    $(".BottomZone").each(function()
    {
        $(this).toggleClass('col-4');
        $(this).toggleClass('col-3');
    });

    $("#KencefKid").toggle();
})