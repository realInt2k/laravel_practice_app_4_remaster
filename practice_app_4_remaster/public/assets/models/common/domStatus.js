domStatus = (function () {
    let modules = {};
    modules.renderError = function (errors) {
        this.removeError();
        const errorsObj = errors.responseJSON.errors;
        for (const key in errorsObj) {
            $(`#input-${key}`).addClass("is-invalid");
            $(`#error-${key}`).html(errorsObj[key][0]);
        }
        $(".is-invalid").first().focus();
        $(".is-invalid").css("border", "none");
    };

    modules.removeError = function () {
        $(".is-invalid").toggleClass("is-invalid");
        $(".error").html("");
    };

    return modules;
}(window.jQuery, window, document))