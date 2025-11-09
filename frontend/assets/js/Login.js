function togglePassword() {
    const pwd = document.getElementById("password");
    pwd.type = pwd.type === "password" ? "text" : "password";
}

window.onload = function () {
            <? php if (isset($_SESSION['success'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success 🎉',
            text: '<?php echo $_SESSION['success']; ?>',
            confirmButtonColor: '#2563eb'
        });
                <? php unset($_SESSION['success']); ?>
            <? php endif; ?>

            <? php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error ❌',
            text: '<?php echo $_SESSION['error']; ?>',
            confirmButtonColor: '#dc2626'
        });
                <? php unset($_SESSION['error']); ?>
            <? php endif; ?>

            <? php if (isset($_GET['logout']) && $_GET['logout'] === 'success'): ?>
        Swal.fire({
            icon: 'success',
            title: 'Logged Out ✅',
            text: 'You have been logged out successfully.',
            confirmButtonColor: '#2563eb'
        });
            <? php endif; ?>
        };