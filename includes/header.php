
<?php

/**
 * E-BOOK LIBRARY
 * Shared Header Component
 */

// Mengambil judul halaman dari index.php
$documentTitle = $pageTitle ?? 'E-Book Library';

// Mengambil nama pengguna dari session
$displayName = $_SESSION['user']['name'] ?? 'Pengguna';

// Mengambil inisial nama pengguna
$userInitial = strtoupper(substr($displayName, 0, 1));

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description"
          content="E-Book Library - Perpustakaan Digital">

    <title>
        <?= htmlspecialchars($documentTitle) ?> | E-Book Library
    </title>

    <!-- CSS UTAMA -->
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

    <!-- CONTAINER UTAMA -->
    <div class="app-container">

