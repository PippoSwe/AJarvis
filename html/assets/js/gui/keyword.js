function Keyword() {
    this.saveEnabled = function() {
        if ($("#keyword").val().length == 0) {
            $("#confirm-save").attr("disabled",true);
            return;
        }
        $("#confirm-save").attr("disabled",false);
    };
    this.inizializeInsertForm = function(controller) {
        $("#save-title").html("Inserisci una nuova keyword");
        $("#confirm-save").attr("disabled",true);
        $("#save").attr("method","POST");
        $("#save").attr("action",controller.page);
        $("#keyword").val("");
    };
    this.inizializeUpdateForm = function(controller, data) {
        $("#save-title").html("Modifica keyword: "+data.keyword);
        $("#confirm-save").attr("disabled",false);
        $("#save").attr("method","PUT");
        $("#save").attr("action",controller.page+data.id);
        $("#keyword").val(data.keyword);
    };
    this.checkSaveButton = function() {
        var ref = this;
        $("#keyword").keyup(function() {
            ref.saveEnabled();
        });
    };
}

$(document).ready(function() {
    var entity = new Keyword();
    var c = new Controller(entity, "/api/keyword/");
    c.loadList();
    c.onClickSave();
});