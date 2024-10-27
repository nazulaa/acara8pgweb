<?php
// Pastikan data POST tersedia
$kecamatan = isset($_POST['kecamatan']) ? $_POST['kecamatan'] : '';
$longitude = isset($_POST['longitude']) ? $_POST['longitude'] : '';
$latitude = isset($_POST['latitude']) ? $_POST['latitude'] : '';
$luas = isset($_POST['luas']) ? $_POST['luas'] : '';
$jumlah_penduduk = isset($_POST['Jumlah_Penduduk']) ? $_POST['Jumlah_Penduduk'] : '';

// Cek apakah semua data diisi
if (empty($kecamatan) || empty($longitude) || empty($latitude) || empty($luas) || empty($jumlah_penduduk)) {
    die("Semua field harus diisi!");
}

// Konfigurasi MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "latihann";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// MErrorenggunakan prepared statement untuk keamanan
$stmt = $conn->prepare("INSERT INTO penduduk (kecamatan, Longitude, Latitude, Luas, Jumlah_Penduduk) 
                        VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sdddd", $kecamatan, $longitude, $latitude, $luas, $jumlah_penduduk);

// Eksekusi dan cek hasilnya
if ($stmt->execute()) {
    echo "Data baru berhasil ditambahkan";
} else {
    echo ": " . $stmt->error;
}

// Tutup statement dan koneksi
$stmt->close();
$conn->close();
?>
