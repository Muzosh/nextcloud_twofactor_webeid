/**
 *
 * @copyright Copyright (c) 2022, Petr Muzikant (petr.muzikant@vut.cz)
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

const lang = "en";

window.onload = function () {
	const submitButton = document.querySelector("#webeid-authenticate");
	const form = document.querySelector("#webeid-form");
	const loading = document.querySelector("#webeid-loading");
	const error = document.querySelector("#webeid-error");
	const nonce = document.querySelector("#webeid-nonce").value;

	function showSpinner() {
		loading.style.display = "block";
	}

	function hideSpinner() {
		loading.style.display = "none";
	}

	function showError(message) {
		error.style.display = "block";
		error.textContent = "Error: " + message;
	}

	function hideError() {
		error.style.display = "none";
		error.textContent = "";
	}

	submitButton.addEventListener("click", async () => {
		try {
			showSpinner();
			hideError();
			const authToken = await webeid.authenticate(nonce, lang);
			document.querySelector("#webeid-token").value =
				JSON.stringify(authToken);
			form.submit();
		} catch (error) {
			switch (error.code) {
				case webeid.ErrorCode.ERR_WEBEID_USER_TIMEOUT: {
					showError("PIN enter timed out, please try again!");
					hideSpinner();
					break;
				}
				case webeid.ErrorCode.ERR_WEBEID_VERSION_MISMATCH: {
					message =
						"Browser extension and native application versions are not matching!";
					if (error.requiresUpdate?.extension) {
						message = message + " Please update browser extension.";
					}

					if (error.requiresUpdate?.nativeApp) {
						message =
							message + " Please update native application.";
					}
					showError(message);
					hideSpinner();
					break;
				}
				case webeid.ErrorCode.ERR_WEBEID_EXTENSION_UNAVAILABLE: {
					showError(
						"Web-eID browser extension is not available. Is it installed?"
					);
					hideSpinner();
					break;
				}
				case webeid.ErrorCode.ERR_WEBEID_NATIVE_UNAVAILABLE: {
					showError(
						"Web-eID native application is not available. Is it installed?"
					);
					hideSpinner();
					break;
				}
				case webeid.ErrorCode.ERR_WEBEID_CONTEXT_INSECURE: {
					showError("Web-eID requires HTTPS connection!");
					hideSpinner();
					break;
				}
				case webeid.ErrorCode.ERR_WEBEID_USER_CANCELLED: {
					showError("Operation was cancelled, please try again!");
					hideSpinner();
					break;
				}
				case webeid.ErrorCode.ERR_WEBEID_NATIVE_INVALID_ARGUMENT: {
					showError(
						"Web-eID native application received invalid argument! Please try again."
					);
					hideSpinner();
					break;
				}
				case webeid.ErrorCode.ERR_WEBEID_NATIVE_FATAL: {
					showError(
						"Web-eID native application terminated with fatal error! Please try again."
					);
					hideSpinner();
					break;
				}
				case webeid.ErrorCode.ERR_WEBEID_MISSING_PARAMETER: {
					showError("Required parameter is missing!");
					hideSpinner();
					break;
				}
				case webeid.ErrorCode.ERR_WEBEID_ACTION_PENDING: {
					showError(
						"Operation is alrady pending! Please check opened windows for PIN prompt."
					);
					break;
				}
				default: {
					showError(
						"An unknown or unexpected error occurred (" +
							error +
							"). Please try again and contact support if the problem persists!"
					);
					hideSpinner();
				}
			}
		}
	});

	// submitButton.click();
};
