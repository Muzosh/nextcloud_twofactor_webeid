const lang = 'en';
const submitButton = document.querySelector("#webeid-authenticate");
const form = document.querySelector("#webeid-form");
const nonce = document.querySelector("#webeid-nonce").value;

submitButton.addEventListener("click", async () => {
    try {
        const authToken = await webeid.authenticate(nonce, lang);
        document.querySelector("#webeid-token").value = JSON.stringify(authToken);
        form.submit();

    } catch (error) {
        console.log("Authentication failed! Error:", error);
        throw error;
    }
});