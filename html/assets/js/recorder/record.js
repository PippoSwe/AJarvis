function recoding_session() {
    $("#stop").toggleClass("d-none");
    $("#pause").toggleClass("d-none");
    $("#timer").countimer('start');
}

function upload_session() {

    Fr.voice.export(function(blob){
        var data = new FormData();
        data.append('file', blob);
        $.ajax({
            url: "/api/project/1/standup/",
            type: 'POST',
            data: data,
            contentType: false,
            processData: false,
            success: function(data) {
                // Sent to Server
                $("#pause").removeClass("d-none");
                $("#pause").addClass("d-none");
                $("#resume").removeClass("d-none");
                $("#resume").addClass("d-none");
                $("#record").removeClass("d-none");
                $("#timer").countimer('stop');
                Fr.voice.stop();
            },
            error: function(xhr, status, text) {
                Fr.voice.stop();
                $( "<div class=\"alert alert-danger\">\n" +
                    "  <strong>Response Error!</strong> " + text + ".\n" +
                    "</div>" ).appendTo( "#messages"  );
            }
        });
    }, "blob");

}

$(document).ready(function() {

    var wavesurfer = WaveSurfer.create({
        container: '#waveform',
        barHeight: 30,
        barWidth: null,
        cursorWidth: 0,
        progressColor: 'black',
        cursorColor: 'black',
        waveColor: 'black',
        height: 200
    });
    var microphone = Object.create(WaveSurfer.Microphone);

    microphone.init({
        wavesurfer: wavesurfer
    });

    microphone.on('deviceReady', function(stream) {
        console.log('Device ready!', stream);
    });
    microphone.on('deviceError', function(code) {
        console.warn('Device error: ' + code);
    });

    $("#timer").countimer({
        // Auto start on inti
        autoStart : false
    });

    $("#record").click(function() {
        $("#record").toggleClass("d-none");
        Fr.voice.record(false, recoding_session());
        microphone.start()
    });

    $("#stop").click(function() {
        $("#stop").toggleClass("d-none");
        microphone.pause();
        upload_session();
    });


    $("#pause").click(function() {
        Fr.voice.pause();
        $("#pause").toggleClass("d-none");
        $("#resume").toggleClass("d-none");
        $("#timer").countimer('stop');
        microphone.pause();
    });

    $("#resume").click(function() {
        Fr.voice.resume();
        $("#pause").toggleClass("d-none");
        $("#resume").toggleClass("d-none");
        $("#timer").countimer('resume');
        microphone.play();
    });

});