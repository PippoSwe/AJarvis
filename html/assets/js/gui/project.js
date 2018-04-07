function Project() {
    this.saveEnabled = function() {
        if ($("#project").val().length == 0) {
            $("#confirm-save").attr("disabled",true);
            return;
        }
        $("#confirm-save").attr("disabled",false);
    };
    this.inizializeInsertForm = function(controller) {
        $("#save-title").html("Inserisci un nuovo progetto");
        $("#confirm-save").attr("disabled",true);
        $("#save").attr("method","POST");
        $("#save").attr("action",controller.page);
        $("#project").val("");
    };
    this.inizializeUpdateForm = function(controller, data) {
        $("#save-title").html("Modifica progetto: "+data.project);
        $("#confirm-save").attr("disabled",false);
        $("#save").attr("method","PUT");
        $("#save").attr("action",controller.page+data.id);
        $("#project").val(data.project);
    };
    this.checkSaveButton = function() {
        var ref = this;
        $("#project").keyup(function() {
            ref.saveEnabled();
        });
    };
}

$(document).ready(function() {
    var entity = new Project();
    var c = new Controller(entity, "/api/project/");
    c.loadList();
    c.onClickSave();
    checKeySetted();
});