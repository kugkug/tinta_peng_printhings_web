$(document).ready(function () {
    if ($(".colorpicker").length) {
        $(".colorpicker").asColorPicker();
    }

    toastr.options = {
        closeButton: true,
        debug: false,
        newestOnTop: false,
        progressBar: false,
        positionClass: "toast-top-right",
        preventDuplicates: false,
    };

    if ($(".phone-number").length) {
        $(".phone-number").on("keyup", function (event) {
            // Only allow numeric input
            let input = $(this);
            let value = input.val();
            let numericValue = value.replace(/[^0-9]/g, "");

            // Limit to 10 digits
            if (numericValue.length > 10) {
                numericValue = numericValue.substring(0, 10);
            }

            // Update the input value
            input.val(numericValue);
        });
    }
});

function _show_toastr(type = "success", message = "", title = "") {
    toastr[type](message, title);
}
