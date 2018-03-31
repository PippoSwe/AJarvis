$(document).ready(function() {
    populateQueue();

    setInterval(populateQueue, 5000);

    function populateQueue() {
        $.ajax({
            url: "/api/queue/",
            type: "GET",
            success: function(data) {
                if(data.length > 0)
                    $.get('item.mst', function(template) {
                        var rendered = Mustache.render(template, {items: data});
                        $('#list').html(rendered);
                    });
            }
        });
    }

});