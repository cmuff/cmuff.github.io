/**
 * Created by Tomson on 19/11/2016.
 */

$('#createForm').submit(accountFormSubmit);

function accountFormSubmit() {
    var errorMsg = $('#message');
    
    if ($('#user').val() === "") {
        errorMsg.text("Username field required.");
        return false;
    }

    var passInput = $('#pass');
    if (passInput.val() === "") {
        errorMsg.text("Password field required.");
        return false;
    }

    var confPassInput = $('#confirmPass');
    if (confPassInput.val() === "") {
        errorMsg.text("Please confirm password.");
        return false;
    }

    if (passInput.val() !== confPassInput.val()) {
        errorMsg.text("Passwords don't match.");
        return false;
    }

    return true;
}

