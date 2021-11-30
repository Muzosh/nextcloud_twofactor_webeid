function showSpinner() {
    $("#twofactor_smartcard-loading").show();
}

function hideSpinner() {
    $("#twofactor_smartcard-loading").hide();
}

function reloadStatus() {
    showSpinner();
    var url = OC.generateUrl("/apps/twofactor_smartcard/settings/getStatus");
    var loading = $.ajax(url, {
        method: "GET",
    });

    $.when(loading).done(function(data) {
        if (!Array.isArray(data) || data.length != 1) {
            $("#twofactor_smartcard-status").text(
                "Unexpected getStatus() result. Please contact administrator: " +
                JSON.stringify(data)
            );
        } else {
            let status = data[0];
            if (status === null) {
                $("#twofactor_smartcard-status").text("SERVER ERROR");
            } else if (status) {
                $("#twofactor_smartcard-status").text("ENABLED");
            } else {
                $("#twofactor_smartcard-status").text("DISABLED");
            }
        }
        hideSpinner();
    });
}

$(document).ready(function() {
    $("#twofactor_smartcard-submit-button").click(function() {
        $("#twofactor_smartcard-settings-msg").hide();
        var password = $("#twofactor_smartcard-smartcard-password").val();

        if (password.length != 12) {
            $("#twofactor_smartcard-settings-msg").text(
                "Message: Password needs to have exactly 12 characters!"
            );
            $("#twofactor_smartcard-settings-msg").show();
            return;
        }

        showSpinner();
        var url = OC.generateUrl(
            "/apps/twofactor_smartcard/settings/setPassword"
        );

        var request = $.ajax(url, {
            method: "POST",
            data: {
                pass: password,
            },
        });

        $.when(request)
            .done(function() {
                $("#twofactor_smartcard-settings-msg").text(
                    "Message: Password was saved."
                );
                $("#twofactor_smartcard-settings-msg").show();
            })
            .fail(function() {
                $("#twofactor_smartcard-settings-msg").text(
                    "Message: Some ERROR occured!"
                );
                $("#twofactor_smartcard-settings-msg").show();
            });

        reloadStatus();
    });

    $("#twofactor_smartcard-delete-button").click(function() {
        $("#twofactor_smartcard-settings-msg").hide();
        showSpinner();
        var url = OC.generateUrl(
            "/apps/twofactor_smartcard/settings/deletePassword"
        );

        var request = $.ajax(url, {
            method: "DELETE",
        });

        $.when(request)
            .done(function() {
                $("#twofactor_smartcard-settings-msg").text(
                    "Message: Password was deleted."
                );
                $("#twofactor_smartcard-settings-msg").show();
            })
            .fail(function() {
                $("#twofactor_smartcard-settings-msg").text(
                    "Some ERROR occured!"
                );
                $("#twofactor_smartcard-settings-msg").show();
            });

        reloadStatus();
    });

    reloadStatus();
});