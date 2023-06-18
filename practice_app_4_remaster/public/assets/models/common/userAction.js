userAction = (function () {
    let modules = {};
    modules.debug = true;

    modules.pageNumber = -1;
    modules.pageCount = -1;

    const DELAY_TIMEOUT = 500;

    modules.getTableUrl = function (action = null) {
        if (action == "delete" && userAction.pageCountElements == 1 && userAction.pageNumber > 1) {
            userAction.pageNumber -= 1;
        }
        return $("#form-search").attr("action") + "?page=" + userAction.pageNumber;
    }

    modules.debounce = function (callback, delay = DELAY_TIMEOUT) {
        let timer;
        return function () {
            clearTimeout(timer);
            timer = setTimeout(() => {
                callback.apply(this, arguments); // why use apply
            }, delay)
        }
    }

    modules.show = function ({ modalId = "#show-modal", populateHtml = "" } = {}) {
        if (userAction.debug) {
            console.log("open modal", modalId);
        }
        const modal = $(modalId);
        $(modalId + "-body").html(populateHtml);
        modal.modal("show");
    }

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

    modules.sendAjax = function ({ url, method = "get", data = "null", sendWithFile = false } = {}) {
        if (sendWithFile) {
            return userAction.sendAjaxProMax({ url, method, data });
        } else {
            return $.ajax({
                url,
                data,
                method,
                async: false
            });
        }

    }

    modules.sendAjaxProMax = function ({ url, method = "get", data = "null" } = {}) {
        return $.ajax({
            url,
            data,
            method,
            cache: false,
            contentType: false,
            processData: false,
            async: false
        });
    }

    modules.getTable = function (url = null) {
        const form = $("#form-search");
        const link = url ?? form.attr("action");
        const data = form.serialize();
        userAction.sendAjax({ url: link, method: "get", data: data })
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