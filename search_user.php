<?php
include 'db_connection.php';

$q = $_GET['q'] ?? '';

$sql = "SELECT * FROM users WHERE name LIKE ? OR email LIKE ?";
$stmt = $conn->prepare($sql);
$like = "%$q%";
$stmt->bind_param("ss", $like, $like);
$stmt->execute();
$result = $stmt->get_result();

echo "<table class='w-full text-left'>
        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr></thead>
        <tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['user_id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['email']}</td>
            <td>
              <button onclick=\"openEditModal({$row['user_id']}, '{$row['name']}', '{$row['email']}')\">✏️</button>
              <button onclick=\"openDeleteModal({$row['user_id']}, '{$row['name']}')\">🗑️</button>
            </td>
          </tr>";
}
echo "</tbody></table>";
?>
