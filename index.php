<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penduduk</title>
    <style>
        body {
            background-color: #f0f8ff;
            font-family: Arial, sans-serif;
        }
        h2 {
            text-align: center;
            color: #003366;
        }
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            background-color: #e6f7ff;
            border: 1px solid #003366;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #003366;
        }
        th {
            background-color: #003366;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #d9f2ff;
        }
        tr:hover {
            background-color: #b3e0ff;
        }
        .delete-button {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .delete-button:hover {
            background-color: #ff1a1a;
        }
    </style>
</head>
<body>
    <h2>Data Penduduk</h2>

    <?php
    $servername = "localhost";
    $username = "root";
    $password = ""; // Kosongkan jika tidak ada password
    $dbname = "latihann";

    // Membuat koneksi
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Cek koneksi
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle deletion request
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete']) && isset($_POST['id'])) {
        $id = $_POST['id'];

        if (!empty($id)) { // Periksa apakah ID tidak kosong
            // Query untuk menghapus data
            $delete_sql = "DELETE FROM penduduk WHERE id = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo "<p style='text-align: center; color: green;'>Data berhasil dihapus.</p>";
                // Refresh halaman untuk memperbarui tampilan data
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "<p style='text-align: center; color: red;'>Error deleting record: " . $conn->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='text-align: center; color: red;'>ID tidak ditemukan atau kosong.</p>";
        }
    }

    // Query untuk mengambil semua data dari tabel penduduk
    $sql = "SELECT * FROM penduduk";
    $result = $conn->query($sql);

    // Tampilkan data jika ada hasil
    if ($result->num_rows > 0) {
        echo "<table><tr> 
                <th>Kecamatan</th> 
                <th>Luas</th> 
                <th>Longitude</th>
                <th>Latitude</th>
                <th>Jumlah Penduduk</th>
                <th>Delete</th>
            </tr>";

        // Mengeluarkan data tiap baris
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row["kecamatan"]) . "</td>
                    <td>" . htmlspecialchars($row["Luas"]) . "</td>
                    <td>" . htmlspecialchars($row["Longitude"]) . "</td>
                    <td>" . htmlspecialchars($row["Latitude"]) . "</td>
                    <td align='right'>" . htmlspecialchars($row["Jumlah_Penduduk"]) . "</td>
                    <td>
                        <form method='POST' action=''>
                            <input type='hidden' name='id' value='" . htmlspecialchars($row["id"]) . "'>
                            <input type='submit' class='delete-button' name='delete' value='Hapus'>
                        </form>
                    </td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='text-align: center; color: red;'>0 results</p>";
    }

    $conn->close();
    ?>
</body>
</html>
