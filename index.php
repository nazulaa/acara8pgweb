<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penduduk</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        body {
            background-color: #ffffff;
            font-family: Arial, sans-serif;
        }
        h2 {
            text-align: center;
            color: #990033;
        }
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            background-color: #ffffff;
            border: 1px solid #ffffff;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ffffff;
        }
        th {
            background-color: #990033;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #ffe6ee;
        }
        tr:hover {
            background-color: #ffffcc;
        }
        .delete-button, .edit-button {
            background-color: #990033;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .delete-button:hover, .edit-button:hover {
            background-color: #ff1a1a;
        }
        #map {
            height: 400px;
            width: 80%;
            margin: 20px auto;
            border: 1px solid #003366;
        }
        .edit-form {
            display: none; /* Hidden by default */
            margin: 20px auto;
            width: 80%;
            border: 1px solid #990033;
            padding: 20px;
            background-color: #ffffe1;
        }
    </style>
</head>
<body>
    <h2>Data Penduduk</h2>

    <!-- Div untuk peta -->
    <div id="map"></div>

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

    // Handle edit request
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        $kecamatan = $_POST['kecamatan'];
        $luas = $_POST['luas'];
        $longitude = $_POST['longitude'];
        $latitude = $_POST['latitude'];
        $jumlah_penduduk = $_POST['jumlah_penduduk'];

        // Query untuk mengupdate data
        $update_sql = "UPDATE penduduk SET kecamatan = ?, Luas = ?, Longitude = ?, Latitude = ?, Jumlah_Penduduk = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssssi", $kecamatan, $luas, $longitude, $latitude, $jumlah_penduduk, $id);

        if ($stmt->execute()) {
            echo "<p style='text-align: center; color: green;'>Data berhasil diupdate.</p>";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            echo "<p style='text-align: center; color: red;'>Error updating record: " . $conn->error . "</p>";
        }
        $stmt->close();
    }

    // Query untuk mengambil semua data dari tabel penduduk
    $sql = "SELECT * FROM penduduk";
    $result = $conn->query($sql);

    $locations = []; // Array untuk menyimpan lokasi
    $rows = []; // Array untuk menyimpan data baris

    if ($result->num_rows > 0) {
        echo "<table><tr> 
                <th>Kecamatan</th> 
                <th>Luas</th> 
                <th>Longitude</th>
                <th>Latitude</th>
                <th>Jumlah Penduduk</th>
                <th>Actions</th>
            </tr>";

        while ($row = $result->fetch_assoc()) {
            $locations[] = [
                'nama' => $row["kecamatan"],
                'longitude' => $row["Longitude"],
                'latitude' => $row["Latitude"]
            ];
            $rows[] = $row; // Menyimpan data baris

            echo "<tr>
                    <td>" . htmlspecialchars($row["kecamatan"]) . "</td>
                    <td>" . htmlspecialchars($row["Luas"]) . "</td>
                    <td>" . htmlspecialchars($row["Longitude"]) . "</td>
                    <td>" . htmlspecialchars($row["Latitude"]) . "</td>
                    <td align='right'>" . htmlspecialchars($row["Jumlah_Penduduk"]) . "</td>
                    <td>
                        <form method='POST' action='' style='display:inline;'>
                            <input type='hidden' name='id' value='" . htmlspecialchars($row["id"]) . "'>
                            <input type='submit' class='delete-button' name='delete' value='Hapus'>
                        </form>
                        <button class='edit-button' onclick='openEditForm(" . htmlspecialchars($row["id"]) . ")'>Edit</button>
                    </td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='text-align: center; color: red;'>0 results</p>";
    }
    
    $conn->close();
    ?>

    <!-- Edit Form -->
    <div class="edit-form" id="editForm">
        <h3>Edit Data Penduduk</h3>
        <form id="formEdit" method="POST">
            <input type="hidden" name="id" id="editId">
            <label for="kecamatan">Kecamatan:</label>
            <input type="text" name="kecamatan" id="editKecamatan" required><br><br>
            <label for="luas">Luas:</label>
            <input type="text" name="luas" id="editLuas" required><br><br>
            <label for="longitude">Longitude:</label>
            <input type="text" name="longitude" id="editLongitude" required><br><br>
            <label for="latitude">Latitude:</label>
            <input type="text" name="latitude" id="editLatitude" required><br><br>
            <label for="jumlah_penduduk">Jumlah Penduduk:</label>
            <input type="number" name="jumlah_penduduk" id="editJumlahPenduduk" required><br><br>
            <input type="submit" class="edit-button" name="edit" value="Update">
            <button type="button" onclick="closeEditForm()">Cancel</button>
        </form>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([-6.200000, 106.816666], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var locations = <?php echo json_encode($locations); ?>;
        var rows = <?php echo json_encode($rows); ?>;

        locations.forEach(function(location) {
            if (location.latitude && location.longitude) {
                L.marker([location.latitude, location.longitude])
                    .addTo(map)
                    .bindPopup("<b>" + location.nama + "</b>");
            }
        });

        function openEditForm(id) {
            const row = rows.find(item => item.id == id);
            if (row) {
                document.getElementById('editId').value = row.id;
                document.getElementById('editKecamatan').value = row.kecamatan;
                document.getElementById('editLuas').value = row.Luas;
                document.getElementById('editLongitude').value = row.Longitude;
                document.getElementById('editLatitude').value = row.Latitude;
                document.getElementById('editJumlahPenduduk').value = row.Jumlah_Penduduk;
                document.getElementById('editForm').style.display = 'block';
            }
        }

        function closeEditForm() {
            document.getElementById('editForm').style.display = 'none';
        }
    </script>
</body>
</html>
