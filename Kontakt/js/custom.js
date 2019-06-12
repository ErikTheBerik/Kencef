$(document).ready(function() {


    var userFeed = new Instafeed({
        get: 'user',
        userId: '11396314736',
        limit: 6,
        resolution: 'standard_resolution',
        accessToken: '11396314736.1677ed0.a9e84078cf8c4cddad2880ba4d96266a',
        sortBy: 'most-recent',
        template: '<div class="col-sm-4 col-6 instaimg" style="display: inline-block;"><a href="{{link}}" target="_blank"><img src="{{image}}" alt="{{caption}}" class="img-fluid"/></a></div>',
    });


    userFeed.run();
});