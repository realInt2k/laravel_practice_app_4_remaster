userAction = (function () {
    let modules = {};
    modules.debug = true;
    modules.openModal = function ({ modalId = "#form-modal", populateHtml = "" } = {}) {
        if (userAction.debug) {
            console.log("open modal", modalId);
        }
        const modal = $(modalId);
        $(modalId + "-body").html(populateHtml);
        modal.modal("show");
    }

    modules.closeModal = function (modalId = "#form-modal") {
        const modal = $(modalId);
        modal.modal("hide");
    }

    modules.sendAjax = function (url, method = "get", data = "null") {
        return $.ajax({
            url,
            data,
            method,
            typeData: "json",
            processData: false,
            contentType: false,
            async: false
        });
    }
    modules.getTable = function (url = null) {
        const form = $("#form-search");
        const link = url ?? form.attr("action");
        const data = form.serialize();
        userAction.sendAjax(link, "get", data)
            .done(function (data) {
                if (userAction.debug) {
                    console.log("get", link, "table ok");
                }
                $("#table-data").html(data.html);
            })
            .fail(function (errors) {
                console.log("get table error", errors);
            });
    }
    return modules;
}(window.jQuery, window, document))