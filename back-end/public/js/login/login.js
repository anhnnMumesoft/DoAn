function validateForm() {
    var checkBox = document.getElementById("agree-checkbox");
    if (!checkBox.checked) {
        // Hiển thị modal sử dụng Bootstrap 5
        var termsModal = new bootstrap.Modal(document.getElementById('termsModal'));
        termsModal.show();
        return false;
    }
    // Nếu hộp kiểm đã được chọn, cho phép form được submit
    document.forms["form"].submit();
}