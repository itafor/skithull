    //Auto fill state when a country has been picked
$("#channel").change(function () {
    var channel = $(this).val();
    console.log(channel);
    if (channel =='youTube') {

       $("#youtube_video_url_holder").css("display", "block");
       $('#youtube_video_url').attr('required', true);

       $("#device_video_url_holder").css("display", "none");
       $('#device_video_url').attr('required', false);
       $('#device_video_url').val("");

    }else if(channel =='device'){
       $("#youtube_video_url_holder").css("display", "none");
       $("#device_video_url_holder").css("display", "block");
       $('#device_video_url').attr('required', true);
       $('#youtube_video_url').attr('required', false);
       $('#youtube_video_url').val("");

    }else if(channel == ''){
       $("#youtube_video_url_holder").css("display", "none");
       $("#device_video_url_holder").css("display", "none");
       $('#device_video_url').attr('required', false);
       $('#youtube_video_url').attr('required', false);
       $('#device_video_url').val("");
       $('#youtube_video_url').val("");
    }
});