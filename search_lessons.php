<?php
include "db_connect.php";

$search = $_GET['q'] ?? '';
$search = $conn->real_escape_string($search); // sanitize input

$query = "SELECT * FROM videos";
if (!empty($search)) {
    $query .= " WHERE title LIKE '%$search%'";
}
$query .= " ORDER BY uploaded_at DESC";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr data-title='" . htmlspecialchars(strtolower($row['title'])) . "' class='border-b border-gray-100 hover:bg-gray-50 transition'>";
        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['description']) . "</td>";
        echo "<td class='px-6 py-4'><span class='text-teal-600 font-medium'>Active</span></td>";
        echo "<td class='px-6 py-4'>
                <video width='120' controls>
                  <source src='" . htmlspecialchars($row['file_path']) . "' type='video/mp4'>
                  Your browser does not support the video tag.
                </video>
              </td>";
        echo "<td class='px-6 py-4'>
                <div class='flex gap-2'>
                  <button onclick=\"editVideo({$row['id']}, '" . addslashes($row['title']) . "', '" . addslashes($row['description']) . "')\" 
                          class='bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-lg text-sm font-semibold transition'>
                    Edit
                  </button>
                  <button onclick=\"deleteVideo({$row['id']})\" 
                          class='bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm font-semibold transition'>
                    Delete
                  </button>
                </div>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center py-4 text-gray-500'>No lessons found</td></tr>";
}
?>
