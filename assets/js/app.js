const memberForm = document.getElementById("memberForm");

if (memberForm) {
    memberForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const password = document.getElementById("memberPassword").value;
        const confirmPassword = document.getElementById("confirmPassword").value;
        const message = document.getElementById("memberFormMessage");

        message.className = "member-form-message";

        if (password !== confirmPassword) {
            message.textContent = "Konfirmasi password tidak sesuai.";
            message.classList.add("error");
            return;
        }

        message.textContent =
            "Formulir berhasil diperiksa. Ini hanya simulasi Front-End, data belum disimpan.";

        message.classList.add("success");
    });
}
