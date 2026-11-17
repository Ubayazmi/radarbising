<?php
/**
 * Admin Profile — Ubah Nama & Ganti Password
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

requireAuth();

$error = '';
$success = '';
$csrfToken = generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCsrfToken($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_name') {
        $fullName = trim($_POST['full_name'] ?? '');
        if (!$fullName) {
            $error = 'Nama lengkap wajib diisi.';
        } else {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ? WHERE id = ?");
            if ($stmt->execute([$fullName, $_SESSION['admin_user_id']])) {
                $_SESSION['admin_username'] = $fullName;
                redirectWith('profile.php', 'success', 'Nama berhasil diperbarui.');
            } else {
                $error = 'Gagal memperbarui nama.';
            }
        }
    } elseif ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Verify current password
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['admin_user_id']]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            $error = 'Password saat ini salah.';
        } elseif (strlen($newPassword) < 6) {
            $error = 'Password baru minimal 6 karakter.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Konfirmasi password tidak cocok.';
        } else {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            if ($stmt->execute([$hash, $_SESSION['admin_user_id']])) {
                redirectWith('profile.php', 'success', 'Password berhasil diubah.');
            } else {
                $error = 'Gagal mengubah password.';
            }
        }
    }
}

$pageTitle = 'Profil Admin';
include __DIR__ . '/templates/profile.php';