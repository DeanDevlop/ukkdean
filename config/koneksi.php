<?php
// It is highly recommended to use a library like 'vlucas/phpdotenv' for robust environment variable management.
// For simplicity, this patch directly uses getenv().
// Ensure these environment variables (DB_HOST, DB_USER, DB_PASS, DB_NAME) are set in your server configuration
// (e.g., Apache, Nginx, Docker, or a .env file loaded by your application).

$host = getenv('DB_HOST') ?: 'localhost'; // Default to 'localhost' for development if not set
$user = getenv('DB_USER') ?: 'root';     // Default to 'root' for development if not set
$pass = getenv('DB_PASS') ?: '';         // Default to empty for development if not set
$db   = getenv('DB_NAME') ?: 'nama_database'; // Replace 'nama_database' with your actual database name

// Establish database connection
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$koneksi) {
    // In a production environment, avoid exposing detailed error messages.
    // Log the error and display a generic message to the user.
    error_log("Koneksi database gagal: " . mysqli_connect_error());
    die("Koneksi ke database gagal. Silakan coba lagi nanti.");
}
?>