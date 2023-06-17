
notification = (function () {
    const modules = {};

    modules.confirm = function (message = "You won't be able to revert this!") {
        return Swal.fire({
            title: "Are you sure?",
            text: message,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes"
        });
    };

    modules.success = function (message) {
        Swal.fire({
            icon: "success",
            title: "Success!",
            text: message
        });
    };

    modules.error = function (message) {
        Swal.fire({
            icon: "error",
            type: "error",
            title: "Error!",
            text: message
        });
    };

    return modules;
}(window.jQuery, window, document))