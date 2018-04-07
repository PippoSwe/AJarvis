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
            url: "/api/project/"+$.urlParam("project_id")+"/standup/",
            type: 'POST',
            data: data,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#stop").addClass("d-none");
                $("#pause").removeClass("d-none");
                $("#pause").addClass("d-none");
                $("#resume").removeClass("d-none");
                $("#resume").addClass("d-none");
                $("#record").removeClass("d-none");
                Fr.voice.stop();
                $("#modalLoading").modal("hide");
            },
            error: function(xhr, status, text) {
                var structure = {x:xhr, s:status, t:text, class:"danger", strong:"Errore!", dismiss:"alert-dismissible fade show"};
                $.get('/assets/tpl/alert.mst', function(template) {
                    var rendered = Mustache.render(template, {items: structure}); //richiede un'array associativo
                    $('#message').html(rendered);
                });
                $("#modalLoading").modal("hide");
            }
        });
    }, "blob");

}

$(document).ready(function() {

    if( !/[?&]project_id=/.test(location.search) ) { $('#record').addClass('disabled') }
    else {
        
        var wavesurfer = WaveSurfer.create({
            container: '#waveform',
            barHeight: 30,
            barWidth: null,
            cursorWidth: 0,
            progressColor: 'black',
            cursorColor: 'black',
            waveColor: 'salmon',
            height: 200
        });
        var microphone = Object.create(WaveSurfer.Microphone);

        microphone.init({
            wavesurfer: wavesurfer
        });

        microphone.on('deviceReady', function (stream) {
            console.log('Device ready!', stream);
        });
        microphone.on('deviceError', function (code) {
            console.warn('Device error: ' + code);
        });

        $("#timer").countimer({
            // Auto start on inti
            autoStart: false
        });

        $("#record").click(function () {
            $("#record").toggleClass("d-none");
            Fr.voice.record(false, recoding_session());
            microphone.start()
        });


        $("#abort-upload").click(function () {
            Fr.voice.stop();
            $("#stop").addClass("d-none");
            $("#pause").removeClass("d-none");
            $("#pause").addClass("d-none");
            $("#resume").removeClass("d-none");
            $("#resume").addClass("d-none");
            $("#record").removeClass("d-none");
            microphone.pause();
        });


        $("#confirm-upload").click(function () {
            $("#modalLoading").modal("show");
            upload_session();
            microphone.pause();
        });


        $("#stop").click(function () {
            //$("#stop").toggleClass("d-none");
            Fr.voice.pause();
            $("#pause").removeClass("d-none");
            $("#pause").addClass("d-none");
            $("#resume").removeClass("d-none");
            $("#timer").countimer('stop');
            microphone.pause();
        });


        $("#pause").click(function () {
            Fr.voice.pause();
            $("#pause").toggleClass("d-none");
            $("#resume").toggleClass("d-none");
            $("#timer").countimer('stop');
            microphone.pause();
        });

        $("#resume").click(function () {
            Fr.voice.resume();
            $("#pause").toggleClass("d-none");
            $("#resume").toggleClass("d-none");
            $("#timer").countimer('resume');
            microphone.play();
        });
    }
});