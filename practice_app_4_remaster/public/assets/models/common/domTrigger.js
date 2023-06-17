// IIFE - Immediately Invoked Function Expression
(function (yourcode) {
    yourcode(window.jQuery, window, document);
}(function ($, window, document) {
    $(function () {
        userAction.getTable();
        $('.button-edit').on("click", function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            const url = $(this).data('url');
            userAction.sendAjax(url, 'get', {})
                .done(function (response) {
                    userAction.openModal({ populateHtml: response.html });
                })
                .fail(function (response) {
                    if (userAction.debug) {
                        console.log(response);
                    }
                });
        })
    })
}));