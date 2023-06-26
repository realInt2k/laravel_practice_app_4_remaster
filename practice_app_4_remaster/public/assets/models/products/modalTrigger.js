(function (yourcode) {
    yourcode(window.jQuery, window, document);
}(function ($, window, document) {
    $(function () {
        $("#image-input").change(function () {
            if (this.files && this.files[0]) {
                $("#remove-image-request").val(false);
                var reader = new FileReader();
                reader.onload = function (e) {
                    $("#preview-image").attr("src", e.target.result);
                    $("#image-preview").attr("hidden", false);
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        $("#delete-image").click(function (e) {
            e.preventDefault();
            $("#remove-image-request").val(true);
            $("#image-input").val("");
            $("#preview-image").attr("src", "#");
            $("#image-preview").attr("hidden", true);
        });

        // const newHeight = $("#input-description").prop('scrollHeight');
        // console.log(newHeight);
        // $("#input-description").height(newHeight);

        $(".select2").select2();
    })
}))
