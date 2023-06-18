(function (yourcode) {
    yourcode(window.jQuery, window, document);
}(function ($, window, document) {
    $(function () {
        $('#form-data').keypress(function (event) {
            if (event.keyCode === 13) {
                event.preventDefault();
            }
        });

        $(document).off("click", ".profile-button-confirm").on("click", ".profile-button-confirm", function (e) {
            e.preventDefault();
            const formData = $("#form-data");
            if (formData.length == 0) {
                if (userAction.debug) {
                    console.log("can't find form-data");
                }
                return;
            }
            let data = formData.serialize();
            const method = formData.data("method");
            const url = formData.attr("action");
            userAction.sendAjax({ url: url, method: method, data: data, sendWithFile: false })
                .done(function (response) {
                    notification.success("profile update ok");
                    $("#profile-name").html(response.data.name);
                    domStatus.removeError();
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
    })
}))