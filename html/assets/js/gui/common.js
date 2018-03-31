$.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    return results[1] || 0;
};

function populateSideBar() {
    $.ajax({
        url: "/api/project/",
        type: "GET",
        success: function(data) {
            $.get('/assets/tpl/menu-recorder.mst', function(template) {
                var rendered = Mustache.render(template, {items: data});
                $('#component').html(rendered);
            });
            $.get('/assets/tpl/menu-dashboard.mst', function(template) {
                var rendered = Mustache.render(template, {items: data});
                $('#components').html(rendered);
            });
        }
    });
}

function populateBell() {
    $.ajax({
        url: "/api/queue/?limit=3&pending=true",
        type: "GET",
        success: function(data) {
            if(data.length > 0)
                $.get('/assets/tpl/bell.mst', function(template) {
                    var rendered = Mustache.render(template, {items: data});
                    $('#bellList').html(rendered);
                });
        }
    });
}

function populateNotify() {
    $.ajax({
        url: "/api/queue/count",
        type: "GET",
        success: function(data) {
            if(data.length > 0)
                $("#notify").text(data[0]['result']);
            populateBell();
        }
    });
}

$(document).ready(function() {
    refresh();
    setInterval(refresh, 5000);
    
    function refresh() {
        populateNotify();
        populateSideBar();
    }

});

