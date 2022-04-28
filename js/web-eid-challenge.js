const lang = 'en';
const submitButton = document.querySelector("#webeid-submit");
const challengeInput = document.querySelector("#webeid-challenge");

submitButton.addEventListener("click", async () => {
    try {
        var challengeNonce = "";

        var url = OC.generateUrl("/apps/twofactor_webeid/settings/getStatus");
        var loading = $.ajax(url, {
            method: "GET",
        });

        $.when(loading).done(function(data) {
            challengeNonce = data;
        });

        var url = OC.generateUrl(
            '/apps/twofactor_webeid/auth/challenge'
        );

        var request = $.ajax(url, {
            method: "GET",
        });
    
        $.when(request).done(function(data) {
            challengeNonce = data;
        }).fail(function() {
            console.log("error");
        });
        
        const authToken = await webeid.authenticate(challengeNonce, lang);
        
        const authTokenResponse = await fetch("/auth/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                [csrfHeaderName]: csrfToken
            },
            body: JSON.stringify({authToken})
        });
        if (!authTokenResponse.ok) {
            throw new Error("POST /auth/login server error: " +
                            authTokenResponse.status);
        }
        const authTokenResult = await authTokenResponse.json();
        
        console.log("Authentication successful! Result:", authTokenResult);

        window.location.href = "/welcome";

    } catch (error) {
        console.log("Authentication failed! Error:", error);
        throw error;
    }
});