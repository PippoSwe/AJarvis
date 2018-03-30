function saveMemberEnable() {
    if ($("#project-member").val().length == 0) {
        $("#save-member").attr("disabled",true);
        return;
    }
    $("#save-member").attr("disabled",false);
};

$(document).ready(function() {
    loadList("/api/project/"+$.urlParam("project_id")+"/member/",
             "#list-member", "member.mst", "member");
    $( "#project-member" ).autocomplete({
        source: function( request, response ) {
            $.ajax({
                url: "/api/member/",
                data: {
                    q: request.term
                },
                success: function( data ) {
                    response($.map(data, function (item) {
                    	var name = item.firstname+" "+item.lastname;
                        return {
                            label: name,
                            value: name,
                            field: item.id
                        };
                    }));
                }
            });
        },
        select: function (event, ui) {
            var id = ui.item.field;
            $('#hidden-member-id').attr('value', id);
            saveMemberEnable();
            return true;
        }
    });

    $("#save-member").click(function(event) {
        event.preventDefault();
        $.ajax({
            url: "/api/project/"+$.urlParam("project_id")+"/member/",
            type: "POST",
            data: "member_id="+$("#hidden-member-id").val(),
            success: function(data) {
            	$( "#project-member" ).val("");
            	saveMemberEnable();
                loadList("/api/project/"+$.urlParam("project_id")+"/member/",
                 "#list-member", "member.mst", "member");

            },
            error: function(xhr, status, text) {
                alert("Non va");
            }
        });
    });
});