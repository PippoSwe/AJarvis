function Controller(entity, page) {
    this.entity =entity;
    this.page = page;
    this.alert = function(structure) {
        $.get('/assets/tpl/alert.mst', function(template) {
            var rendered = Mustache.render(template, {items: structure}); //richiede un'array associativo
            $('#message').html(rendered);
        });
    };
    this.onClickDelete = function(selector) {
        var ref = this;
        $(selector).click(function() {
            var data_id = $(this).attr("data-id");
            var div_id = "#list-item-"+data_id;
            $("body").on('click', '#confirm-delete', function () {
                $.ajax({
                    url: ref.page+data_id,
                    type: "DELETE",
                    success: function(data) {
                        $("div").remove(div_id);
                        var structure = {strong:"Eliminazione avvenuta con successo", class:"success", t:"", dismiss:"alert-dismissible fade show"}; //passo i messaggi per riusare il file mst
                        ref.alert(structure);
                    },
                    error: function(xhr, status, text) {
                        var structure = {x:xhr, s:status, t:text, class:"danger", strong:"Errore!", dismiss:"alert-dismissible fade show"};
                        ref.alert(structure);
                    }
                });
            });
        });
    };
    this.onClickEdit = function(selector) {
        var ref = this;
        $(selector).click(function() {
            var data_id = $(this).attr("data-id");
            $.ajax({
                url: ref.page+data_id,
                type: "GET",
                success: function(data) {
                    ref.inizializeUpdateForm(data);
                    $("#save-modal").modal("show");
                },
                error: function(xhr, status, text) {
                    var structure = {x:xhr, s:status, t:text, class:"danger", strong:"Errore!", dismiss:"alert-dismissible fade show"};
                    ref.alert(structure);
                }
            });
        });
    };
    this.sendSaveRequest = function() {
        var ref = this;
        $.ajax({
            url: $("#save").attr("action"),
            type: $("#save").attr("method"),
            data: $("#save").serialize(),
            success: function(data) {
                $("#save-modal").modal("hide");
                var structure = {strong:"Salvataggio avvenuto con successo", class:"success", t:""}; //passo i messaggi per riusare il file mst
                ref.alert(structure);
                ref.loadList();
            },
            error: function(xhr, status, text) {
                var structure = {x:xhr, s:status, t:text, class:"danger", strong:"Errore!"};
                ref.alert(structure);
                $(".modal").modal('hide');
            }
        });
    };
    this.onClickSave = function() {
        var ref = this;
        $("#btn-insert").click(function() {
            ref.inizializeInsertForm();
            $("#save-modal").modal("show");
        });
        ref.checkSaveButton();
        $("#save").submit(function(event) {
            event.preventDefault();
            ref.sendSaveRequest();
        });
    };

    this.loadList = function() {
        var ref = this;
        $.ajax({
            url: this.page,
            type: "GET",
            success: function(data) {
                if(data.length > 0)
                    $.get('item.mst', function(template) {
                        var rendered = Mustache.render(template, {items: data});
                        $('#list').html(rendered);
                        ref.onClickDelete(".delete");
                        ref.onClickEdit(".edit");
                    });
            },
            error: function(xhr, status, text) {
                var structure = {x:xhr, s:status, t:xhr.responseText, class:"danger", strong:"Errore!", dismiss:"alert-dismissible fade show"};
                ref.alert(structure);
            }
        });
    };

    this.inizializeInsertForm = function() {
        this.entity.inizializeInsertForm(this);
    };
    this.inizializeUpdateForm = function(data) {
        this.entity.inizializeUpdateForm(this, data);
    };
    this.checkSaveButton = function() {
        this.entity.checkSaveButton();
    };
};