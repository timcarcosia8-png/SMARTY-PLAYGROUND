<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo "<tr><td colspan='3' class='text-center py-4 text-red-500'>Unauthorized access</td></tr>";
    exit;
}

$q = $_GET['q'] ?? '';
$role = $_SESSION['role'];

// Base SQL — searches name or email
$sql = "SELECT user_id, name, email, role FROM users WHERE (name LIKE ? OR email LIKE ?)";

// Restrict admin from viewing superadmins
if ($role === 'admin') {
    $sql .= " AND role != 'superadmin'";
}

$stmt = $conn->prepare($sql);
$like = "%$q%";
$stmt->bind_param("ss", $like, $like);
$stmt->execute();
$result = $stmt->get_result();

// Output results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Hide edit/delete buttons for superadmin accounts if the logged-in user is not a superadmin
        $showActions = !($row['role'] === 'superadmin' && $role !== 'superadmin');

        echo "<tr data-id='{$row['user_id']}' class='border-b border-gray-100 hover:bg-gray-50 transition'>";
        echo "<td class='px-6 py-4 text-gray-800 user-name'>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td class='px-6 py-4 text-gray-600 user-email'>" . htmlspecialchars($row['email']) . "</td>";

        echo "<td class='px-6 py-4'>";
        if ($showActions) {
            echo "<div class='flex gap-2'>
                    <button onclick=\"openEditModal({$row['user_id']}, '" . htmlspecialchars($row['name'], ENT_QUOTES) . "', '" . htmlspecialchars($row['email'], ENT_QUOTES) . "', '{$row['role']}')\" class='p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition'>
                    <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                       <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'></path>
                    </svg>
                    </button>
                    
                    <button onclick=\"openDeleteModal({$row['user_id']}, '" . htmlspecialchars($row['name'], ENT_QUOTES) . "', '{$row['role']}')\" class='p-2 text-red-500 hover:bg-red-50 rounded-lg transition'>
                     <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                         <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'></path>
                    </svg>️
                    </button>
                  </div>";
        } else {
            echo "<span class='text-gray-400 italic'>No actions available</span>";
        }
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3' class='text-center py-4 text-gray-500'>No users found</td></tr>";
}

$stmt->close();
$conn->close();
?>
