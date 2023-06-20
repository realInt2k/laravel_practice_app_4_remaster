// IIFE - Immediately Invoked Function Expression
(function (yourcode) {
    yourcode(window.jQuery, window, document);
}(function ($, window, document) {

    document.addEventListener('keyup', function (event) {
        if (event.key === 'Enter' || event.key === "Escape") {
            Swal.close();
        }
    });
    $(document).ready(function () {
        userAction.getTable();
    })
    $(function () {
        $(".search-select").each(function () {
            $(this).select2({
            });
        })

        $(document).off("keypress", ".is-invalid").on("keypress", ".is-invalid", function () {
            $(this).removeClass("is-invalid");
        });

        $("#form-search").off("keyup", ".search-input").on("keyup", ".search-input", userAction.debounce(function () {
            userAction.getTable();
        }));

        $(document).off("change", ".search-select").on("change", ".search-select", userAction.debounce(function () {
            userAction.getTable();
        }));

        $("#form-search").on("submit", function (e) {
            e.preventDefault();
            return false;
        });

        $(document).off("submit", "#form-data").on("submit", "#form-data", function (e) {
            e.preventDefault();
            return false;
        })

        $(document).off("click", ".button-create").on("click", ".button-create", function (e) {
            e.preventDefault();
            e.stopPropagation();
            const url = $(this).data("url");
            userAction.getPageInfo();
            userAction.sendAjax({ url: url, method: 'get', data: {} })
                .done(function (response) {
                    userAction.openModal({ populateHtml: response.html });
                })
                .fail(function (response) {
                    if (userAction.debug) {
                        console.log(response);
                    }
                });
        })

        $(document).off("click", ".button-edit").on("click", ".button-edit", function (e) {
            e.preventDefault();
            e.stopPropagation();
            const id = $(this).data("id");
            const url = $(this).data("url");
            userAction.getPageInfo();
            userAction.sendAjax({ url: url, method: 'get', data: {} })
                .done(function (response) {
                    userAction.openModal({ populateHtml: response.html });
                })
                .fail(function (response) {
                    if (userAction.debug) {
                        console.log(response);
                    }
                });
        });

        $(document).off("click", ".button-show").on("click", ".button-show", function (e) {
            e.preventDefault();
            e.stopPropagation();
            const id = $(this).data("id");
            const url = $(this).data("url");
            userAction.getPageInfo();
            userAction.sendAjax({ url: url, method: 'get', data: {} })
                .done(function (response) {
                    userAction.openModal({ modalId: "#show-modal", populateHtml: response.html });
                })
                .fail(function (response) {
                    if (userAction.debug) {
                        console.log(response);
                    }
                });
        });

        $(document).off("click", ".button-delete").on("click", ".button-delete", function (e) {
            e.preventDefault();
            e.stopPropagation();
            notification.confirm().then((result) => {
                if (!result.isConfirmed) {
                    return;
                }
                const url = $(this).data("url");
                userAction.getPageInfo();
                const getTableUrl = userAction.getTableUrl("delete");
                userAction.sendAjax({
                    url: url, method: 'delete', data: {
                        "_token": $('meta[name="csrf-token"]').attr('content')
                    }
                })
                    .done(function (response) {
                        notification.success("Deleted!", "Data has been deleted successfully.");
                        userAction.getTable(getTableUrl);
                    })
                    .fail(function (response) {
                        notification.error("Data has been deleted failed.");
                        if (userAction.debug) {
                            console.log(response);
                        }
                    });
            });
        });

        $(document).off("click", "#form-modal-save").on("click", "#form-modal-save", function (e) {
            e.preventDefault();
            const formData = $("#form-data");
            if (formData.length == 0) {
                if (userAction.debug) {
                    console.log("can't find form-data");
                }
                return;
            }
            let data = formData.serialize();
            let sendWithFile = false;
            if (formData.attr('enctype') === 'multipart/form-data') {
                data = new FormData($("#form-data")[0]);
                sendWithFile = true;
            }
            const method = formData.data("method");
            const url = formData.attr("action");
            const getTableUrl = userAction.getTableUrl();
            userAction.sendAjax({ url: url, method: method, data: data, sendWithFile })
                .done(function (response) {
                    notification.success("Success!", response.message);
                    userAction.getTable(getTableUrl);
                    domStatus.removeError(response);
                })
                .fail(function (response) {
                    domStatus.renderError(response);
                })
                .always(function (response) {
                    if (userAction.debug) {
                        console.log("update's response: ", response);
                    }
                });
        })


        $(document).off("click", ".page-link").on("click", ".page-link", function (e) {
            e.preventDefault();
            const url = $(this).attr("href");
            userAction.getTable(url);
        })
    })
}));