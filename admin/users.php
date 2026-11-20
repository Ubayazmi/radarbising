<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

requireAuth();

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$error = '';
$success = '';
$csrfToken = generateCsrfToken();

// --- CREATE ---
if ($action === 'create') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $fullName = trim($_POST['full_name'] ?? '');
        $role = trim($_POST['role'] ?? 'Writer');
        $password = trim($_POST['password'] ?? '');
        
        if (!$fullName) {
            $error = 'Nama lengkap wajib diisi.';
        } else {
            // Auto-generate user_id: 001, 002, ... (only numeric ones)
            $last = $pdo->query("SELECT user_id FROM users WHERE user_id NOT LIKE '%[^0-9]%' ORDER BY user_id::bigint DESC LIMIT 1")->fetch();
            $nextNum = $last ? ((int)$last['user_id']) + 1 : 1;
            $userId = sprintf('%03d', $nextNum);
            
            // Password: if empty, auto-generate
            if (!$password) {
                $password = bin2hex(random_bytes(4)); // 8 chars
            }
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (user_id, full_name, role, password_hash, created_at) VALUES (?, ?, ?, ?, NOW())");
            if ($stmt->execute([$userId, $fullName, $role, $hash])) {
                $msg = "User berhasil dibuat. ID: $userId";
                if (!$_POST['password']) {
                    $msg .= " | Password: $password (simpan & bagikan ke user)";
                }
                redirectWith('users.php', 'success', $msg);
            } else {
                $error = 'Gagal membuat user (ID mungkin duplikat).';
            }
        }
    }
    $pageTitle = 'Tambah User Baru';
    include __DIR__ . '/templates/user_form.php';
    exit;
}

// --- EDIT ---
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    if (!$user) {
        redirectWith('users.php', 'error', 'User tidak ditemukan.');
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $fullName = trim($_POST['full_name'] ?? '');
        $role = trim($_POST['role'] ?? 'Writer');
        $newPassword = trim($_POST['password'] ?? '');
        
        if (!$fullName) {
            $error = 'Nama lengkap wajib diisi.';
        } else {
            if ($newPassword) {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, role = ?, password_hash = ? WHERE id = ?");
                $stmt->execute([$fullName, $role, $hash, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, role = ? WHERE id = ?");
                $stmt->execute([$fullName, $role, $id]);
            }
            redirectWith('users.php', 'success', 'User berhasil diperbarui.');
        }
    }
    
    $pageTitle = 'Edit User';
    include __DIR__ . '/templates/user_form.php';
    exit;
}

// --- DELETE ---
if ($action === 'delete' && $id > 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCsrfToken($_POST['csrf_token'] ?? '')) {
        // Prevent deleting self
        if ($id === ($_SESSION['admin_user_id'] ?? 0)) {
            redirectWith('users.php', 'error', 'Tidak bisa menghapus akun sendiri.');
        }
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        redirectWith('users.php', 'success', 'User berhasil dihapus.');
    }
    redirectWith('users.php', 'error', 'Permintaan tidak valid.');
}

// --- LIST ---
$stmt = $pdo->query("SELECT * FROM users ORDER BY id ASC");
$users = $stmt->fetchAll();

$pageTitle = 'Kelola User';
$listMode = true;
include __DIR__ . '/templates/user_list.php';