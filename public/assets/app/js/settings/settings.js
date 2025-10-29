$(document).ready(function () {
    _fetchSettings();
    $("[data-trigger]").click(function (e) {
        e.preventDefault();
        let trigger = $(this).data("trigger");
        let parentForm = $(this).closest("form");

        switch (trigger) {
            case "save-units":
                if (!_checkFormFields(parentForm)) {
                    return;
                }

                let formData = _collectFields(parentForm);
                ajaxRequest(
                    "/api/settings/units/save",
                    JSON.parse(formData),
                    ""
                );
                break;
        }
    });
});

function _fetchSettings() {
    ajaxRequest("/api/settings/units/list", "", "POST");
}

function _init_acitions() {
    $("[data-trigger='delete-unit']").click(function (e) {
        e.preventDefault();
        let id = $(this).data("id");
        ajaxRequest("/api/settings/units/delete", { id: id }, "POST");
    });
}
