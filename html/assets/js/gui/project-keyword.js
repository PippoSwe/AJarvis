function saveKeywordEnable() {
    if ($("#project-keyword").val().length == 0) {
        $("#save-keyword").attr("disabled",true);
        return;
    }
    $("#save-keyword").attr("disabled",false);
};

$(document).ready(function() {
    loadList("/api/project/"+$.urlParam("project_id")+"/keyword/",
             "#list-keyword", "keyword.mst", "keyword");
    $( "#project-keyword" ).autocomplete({
        source: function( request, response ) {
            $.ajax({
                url: "/api/keyword/",
                data: {
                    q: request.term
                },
                success: function( data ) {
                    response($.map(data, function (item) {
                        return {
                            label: item.keyword,
                            value: item.keyword,
                            field: item.id
                        };
                    }));
                }
            });
        },
        select: function (event, ui) {
            var id = ui.item.field;
            $('#hidden-keyword-id').attr('value', id);
            saveKeywordEnable();
            return true;
        }
    });

    $("#save-keyword").click(function(event) {
        event.preventDefault();
        $.ajax({
            url: "/api/project/"+$.urlParam("project_id")+"/keyword/",
            type: "POST",
            data: "keyword_id="+$("#hidden-keyword-id").val(),
            success: function(data) {
                $("#project-keyword").val("");
                saveKeywordEnable();
                loadList("/api/project/"+$.urlParam("project_id")+"/keyword/",
                 "#list-keyword", "keyword.mst", "keyword");
            },
            error: function(xhr, status, text) {
                alert("Non va");
            }
        });
    });
});