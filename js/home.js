// if ('serviceWorker' in navigator) {
//   window.addEventListener('load', function() {
//     navigator.serviceWorker.register('js/sw.js').then(function(registration) {
//       // Registration was successful
//       console.log('ServiceWorker registration successful with scope: ', registration.scope);
//     }, function(err) {
//       // registration failed :(
//       console.log('ServiceWorker registration failed: ', err);
//     });
//   });
// }
// else
// {
//     // alert('No service worker');
// }

var buttonArray = [];

buttonArray['info'] = [];
buttonArray['info']['mfangano'] = 'Info/mfangano.html';
buttonArray['info']['team'] = 'Info/team.html';
buttonArray['info']['schule'] = 'Info/schule.html';

buttonArray['spende'] = [];
buttonArray['spende']['main'] = 'Spende/spende.html';
buttonArray['spende']['paten'] = 'Spende/paten.html';
buttonArray['spende']['projekte'] = 'Spende/projekte.html';
buttonArray['spende']['verein'] = 'Spende/verein.html';

buttonArray['kontakt'] = [];
buttonArray['kontakt']['E mail'] = 'Kontakt/email.html';
buttonArray['kontakt']['social'] = 'Kontakt/social.html';
buttonArray['kontakt']['impressum'] = 'Kontakt/impressum.html';


$(".KencefButton").click(function()
{
    $('#MainWindow').append('<div id="bottom_div"></div>');
    $("#KencefLogo").addClass('small');

    $("#KencefLogo").on('click', function(e)
    {
        e.preventDefault();
        location.reload();
    });

    $(this).addClass('Special');

    $("#LogoContainer").removeClass('col-12');
    $("#LogoContainer").addClass('col-3');

    $(".BottomZone").each(function()
    {
        $(this).removeClass('col-4');
        $(this).addClass('col-3');
    });

    $("#KencefKid").hide();

    $('#top_div').append('<div class="col-9" id="ButtonsSection"><div class="row ButtonRow" id="MainButtons"></div><div class="row ButtonRow" id="SubButtons"><div class="col-4 buttonCol"></div><div class="col-4 buttonCol"></div><div class="col-4 buttonCol"></div></div></div>');

    $(".KencefButton").each(function()
    {
        $(this).addClass('small');
        $(this).removeClass('Special');
        $(this).removeClass('Intro');

        $(this).off('click').on('click', function()
        {
            if ($(this).hasClass('Special'))
                return;

            $(".KencefButton").each(function()
            {
                $(this).removeClass('Special');
            });

            $(this).addClass('Special');

            $('.KencefSubButton').each(function()
            {
                $(this).remove();
            });

            var subArray = buttonArray[$(this).attr('name')];
            if (subArray != undefined)
            {
                var index = 0;
                for (var k in subArray)
                {
                    if (k === 'main')
                    {
                        $('#bottom_div').load(subArray['main']);
                        continue;
                    }

                    var button = CreateSubButton(k, subArray[k]);
                    if (index == 0 && subArray['main'] === undefined)
                    {
                        button.click();
                    }

                    $($("#SubButtons").find('.col-4').get(index)).append(button);

                    index++;
                }
            }

        });

        var mainButtonDiv = $(document.createElement('div'));
        mainButtonDiv.addClass('col-4');
        mainButtonDiv.addClass('buttonCol');
        $("#MainButtons").append(mainButtonDiv);
        $(this).appendTo(mainButtonDiv);

    });

    $(".BottomZone").remove();

    $(this).click();
})

function CreateSubButton(name, url)
{
    var subButton = $(document.createElement('button'));
    subButton.text(name);
    subButton.data('url', url);

    console.log(subButton.data('url'));

    subButton.addClass('KencefSubButton');
    subButton.on('click', function()
    {
        $('.KencefSubButton').each(function()
        {
            $(this).removeClass('Special');
        });

        $(this).addClass('Special');

        $('#bottom_div').load(url);
    });

    return subButton;
}