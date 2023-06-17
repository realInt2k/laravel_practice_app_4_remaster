// IIFE - Immediately Invoked Function Expression
(function (yourcode) {
    yourcode(window.jQuery, window, document);
}(function ($, window, document) {
    document.addEventListener('keyup', function (event) {
        if (event.key === 'Enter' || event.key === "Escape") {
            Swal.close();
        }
    });
    $(function () {
        userAction.getTable();
        $(document).on("click", ".button-edit", function (e) {
            e.preventDefault();
            const id = $(this).data("id");
            const url = $(this).data("url");
            userAction.pageNumber = $(this).data("page-number");
            userAction.pageCountElements = $(this).data("page-count-elements");
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

        $(document).on("click", ".button-delete", function (e) {
            e.preventDefault();
            notification.confirm().then((result) => {
                if (!result.isConfirmed) {
                    return;
                }
                const url = $(this).data("url");
                userAction.pageNumber = $(this).data("page-number");
                userAction.pageCountElements = $(this).data("page-count-elements");
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

        $(document).on("click", "#form-modal-save", function (e) {
            e.preventDefault();
            const formData = $("#form-data");
            const data = formData.serialize();
            const method = formData.data("method");
            const url = formData.attr("action");
            const getTableUrl = userAction.getTableUrl();
            userAction.sendAjax({ url: url, method: method, data: data })
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
                        console.log("response: ", response);
                    }
                });
        })

        $(document).on("click", ".page-link", function (e) {
            e.preventDefault();
            const url = $(this).attr("href");
            userAction.getTable(url);
        })
    })
}));