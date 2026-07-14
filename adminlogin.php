<?php
// ─────────────────────────────────────────────
// Super Admin Login
// ─────────────────────────────────────────────
session_start();
require_once 'db.php';

if (isset($_SESSION['admin_user'])) {
    header('Location: admin/');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $conn->prepare("SELECT user_name FROM admins WHERE user_name = ? AND password = ? AND role IN ('super_admin','admin')");
    if ($stmt) {
        $stmt->bind_param('ss', $name, $password);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $_SESSION['admin_user'] = $name;
            header('Location: admin/');
            exit();
        } else {
            $error = 'Invalid username or password.';
        }
        $stmt->close();
    } else {
        $error = 'System error. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — iRecovery</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/1570/1570887.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body class="bg-overlay">

    <div class="login-wrapper">
        <div class="login-card">

            <div class="login-logo">
                <a href="index.php" title="Back to home">
                    <img src="https://cdn-icons-png.flaticon.com/512/1570/1570887.png" alt="iRecovery">
                </a>
            </div>

            <div class="login-heading">
                <div class="role-badge"><i class="bi bi-shield-lock"></i> Super Admin</div>
                <h1><i class="bi bi-shield-lock me-2"></i>Admin Login</h1>
                <p>Administrator access only</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="mb-3">
                    <label for="name" class="form-label">Username</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-person"></i>
                        <input type="text" class="form-control" name="name" id="name"
                               autocomplete="username" placeholder="Admin username" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-lock"></i>
                        <input type="password" class="form-control" name="password" id="password"
                               autocomplete="current-password" placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </button>
            </form>

        </div>
    </div>

    <footer class="site-footer">
        &copy; <?= date('Y') ?> iRecovery &mdash; Powered by
        <a href="https://kakebe.tech/" target="_blank" rel="noopener">Kakebe Technologies Limited</a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
