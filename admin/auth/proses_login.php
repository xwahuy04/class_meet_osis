<?php
session_start();
require_once __DIR__ . '/../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validasi input
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Username dan password harus diisi!";
        header("Location: login.php");
        exit();
    }

    try {
        // Cari user di database
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Verifikasi dengan MD5
        if ($user && md5($password) === $user['password']) {
            // Set session
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_role'] = $user['role'];
            
            header("Location: " . BASE_URL . "admin/dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Username atau password salah!";
            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Terjadi kesalahan sistem";
        error_log("Login Error: " . $e->getMessage());
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}