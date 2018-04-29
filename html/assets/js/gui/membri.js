function Membri() {
    this.saveEnabled = function() {
        if ($("#firstname").val().length == 0) {
            $("#confirm-save").attr("disabled",true);
            return;
        }
        if ($("#lastname").val().length == 0) {
            $("#confirm-save").attr("disabled",true);
            return;
        }
        $("#confirm-save").attr("disabled",false);
    };
    this.inizializeInsertForm = function(controller) {
        $("#save-title").html("Inserisci un nuovo membro");
        $("#confirm-save").attr("disabled",true);
        $("#save").attr("method","POST");
        $("#save").attr("action",controller.page);
        $("#firstname").val("");
        $("#lastname").val("");
        $("#work").prop("checked", true);
    };
    this.inizializeUpdateForm = function(controller, data) {
        $("#save-title").html("Modifica membro: "+data.firstname+" "+data.lastname);
        $("#confirm-save").attr("disabled",false);
        $("#save").attr("method","PUT");
        $("#save").attr("action",controller.page+data.id);
        $("#firstname").val(data.firstname);
        $("#lastname").val(data.lastname);
        if( data.work == 0 )
            $("#work").prop("checked", false);
        else
            $("#work").prop("checked", true);
    };
    this.checkSaveButton = function() {
        var ref = this;
        $("#firstname").keyup(function() {
            ref.saveEnabled();
        });
        $("#lastname").keyup(function() {
            ref.saveEnabled();
        });
    };
}

$(document).ready(function() {
    var entity = new Membri();
    var c = new Controller(entity, "/api/member/");
    c.loadList();
    c.onClickSave();
    checKeySetted();
});