<?php
session_start(); // <-- TAMBAHKAN INI DI PALING ATAS
include 'koneksi.php';?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Sembako Nenek Yoyoh</title>
    <link rel="stylesheet" href="asset/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Welcome Page -->
        <div class="welcome-page active">
            <div class="welcome-image">
                <img src="asset/images/sembako.png">
            </div>
            <br><br>
            <h1>Selamat Datang!</h1>
            <h2>Warung Sembako<br>Nenek Yoyoh</h2>
            <br>
            <a href="katalog.php" class="welcome-button">Mari Belanja!</a>
        </div>
    </div>
</body>
</html>