function setup_check_transfer_of_data_handler() {
    const btn_submit = document.getElementById("submit");
    const check_transfer_of_data = document.getElementById("transfer");
    if (check_transfer_of_data == null) {
        return;
    }
    check_transfer_of_data.onclick = function(e) {
        btn_submit.disabled = !check_transfer_of_data.checked;
    }
}

$(document).ready(function() {
    setup_check_transfer_of_data_handler();
});
