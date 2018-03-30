$.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    return results[1] || 0;
};

function populateSideBar() {
    $.ajax({
        url: "/api/project/",
        type: "GET",
        success: function(data) {
            $.get('/assets/tpl/item.mst', function(template) {
                var rendered = Mustache.render(template, {items: data});
                $('#components').html(rendered);
                $('#component').html(rendered);
            });
        },
        error: function(xhr, status, text) {
            alert(xhr.responseText);
        }
    });
}

function clearSideBar() {
    $('#components').remove();
    $('#component').remove();
}

$(document).ready(function() {
    populateSideBar();
});