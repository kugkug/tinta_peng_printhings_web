$(document).ready(function () {
    $('[data-action="toggle-theme"]').on("click", function () {
        let icon = $(this).find("i");
        let theme = "";
        if (icon.hasClass("fa-moon")) {
            icon.removeClass("fa-moon").removeClass("text-secondary");
            icon.addClass("fa-sun").addClass("text-warning");
            theme = "dark";
        } else {
            icon.removeClass("fa-sun").removeClass("text-warning");
            icon.addClass("fa-moon").addClass("text-secondary");
            theme = "light";
        }

        ajaxRequest(
            "/executor/settings/toggle-theme",
            {
                Theme: theme,
            },
            ""
        );
    });
    $('[data-action="logout"]').on("click", function () {
        console.log("logout");
        ajaxRequest("/executor/account/logout", "", "");
    });
});

function ajaxRequest(sUrl = "", sData = "", sLoadParent = "") {
    $.ajax({
        url: sUrl,
        type: "POST",
        headers: { "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content") },
        data: sData,
        beforeSend: function () {
            if (sLoadParent != "" && sLoadParent == "sub-loader") {
                $("#sub-loader").fadeIn(200);
            } else {
                $("#full-loader").fadeIn(200);
            }
        },
        success: function (result) {
            console.log(result);
            if (sLoadParent != "" && sLoadParent == "sub-loader") {
                $("#sub-loader").fadeOut(200);
            } else {
                $("#full-loader").fadeOut(500);
            }
            if (result.js) {
                eval(result.js);
            }
        },
        error: function (e) {
            if (sLoadParent != "" && sLoadParent == "sub-loader") {
                $("#sub-loader").fadeOut(200);
            } else {
                $("#full-loader").fadeOut(500);
            }
            console.log(e);

            _show_toastr(
                "error",
                "Please call system administrator!",
                "System Error"
            );
        },
    });
}

function ajaxSubmit(sUrl = "", sFormData = "", sLoadParent = "") {
    $.ajax({
        url: sUrl,
        type: "POST",
        headers: { "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content") },
        data: sFormData,
        cache: false,
        processData: false,
        contentType: false,
        beforeSend: function () {
            if (sLoadParent != "" && sLoadParent == "sub-loader") {
                $("#sub-loader").fadeIn(200);
            } else {
                $("#full-loader").fadeIn(200);
            }
        },
        success: function (result) {
            console.log(result);
            if (sLoadParent != "" && sLoadParent == "sub-loader") {
                $("#sub-loader").fadeOut(200);
            } else {
                $("#full-loader").fadeOut(500);
            }
            eval(result.js);
        },
        error: function (e) {
            if (sLoadParent != "" && sLoadParent == "sub-loader") {
                $("#sub-loader").fadeOut(200);
            } else {
                $("#full-loader").fadeOut(500);
            }
            console.log(e);

            _show_toastr(
                "error",
                "Please call system administrator!",
                "System Error"
            );
        },
    });
}

function _checkFormFields(parentForm) {
    var nCnt = 0;
    var nEmpty = 0;
    var aElements = $(parentForm).find("input, textarea, select");

    for (nCnt = 0; nCnt < aElements.length; nCnt++) {
        var element = aElements[nCnt];
        var elem_value = $(element).val();
        var is_required = $(element).attr("data");

        if ($(element).is(":visible")) {
            if (is_required != "req") continue;

            if (elem_value == "") {
                $(element)
                    .next(".invalid-feedback")
                    .removeClass("d-none")
                    .fadeIn(200);
                nEmpty++;
            } else {
                $(element)
                    .next(".invalid-feedback")
                    .fadeOut(200)
                    .addClass("d-none");
            }
        }
    }

    if (nEmpty > 0) return false;
    else return true;
}

function _collectFields(parentForm) {
    var sJsonFields = {};
    var nCnt = 0;
    var nEmpty = 0;
    var aElements = $(parentForm).find(
        "input:not(:checkbox):not(:radio), textarea, select"
    );

    for (nCnt = 0; nCnt < aElements.length; nCnt++) {
        var sElement = aElements[nCnt];

        var sDataKey = $(sElement).attr("data-key");
        var sValue = $(sElement).val();

        if ($(sElement).is(":visible") === true) {
            if (sDataKey) {
                sJsonFields[sDataKey] = sValue;
            }
        }
    }

    return JSON.stringify(sJsonFields);
}

function _applyTheme(sTheme) {
    if (sTheme == "dark") {
        new quixSettings({
            version: "dark",
            layout: "vertical",
            navheaderBg: "color_2",
            headerBg: "color_2",
            sidebarBg: "color_2",
        });
    } else {
        new quixSettings({
            version: "light",
            layout: "vertical",
            navheaderBg: "color_1",
            headerBg: "color_1",
            sidebarBg: "color_1",
        });
    }
}

function _confirm(
    title,
    text,
    type,
    confirmButtonText,
    closeOnConfirm,
    funcCallback
) {
    swal(
        {
            title: title,
            text: text,
            type: type,
            showCancelButton: !0,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: confirmButtonText,
            closeOnConfirm: closeOnConfirm,
        },
        function () {
            funcCallback();
        }
    );
}

function _show_toastr(type, message, title) {
    toastr.options = {
        closeButton: true,
        progressBar: true,
        showMethod: "slideDown",
        timeOut: 4000,
    };

    if (type === "success") {
        toastr.success(message, title);
    } else if (type === "info") {
        toastr.info(message, title);
    } else if (type === "warning") {
        toastr.warning(message, title);
    } else if (type === "error") {
        toastr.error(message, title);
    }
}
