<?php
<<<<<<< HEAD
session_start();
include "db_connect.php";
include "filter_input.php";// connection file
include "admin_session.php";
// include "user_session.php";


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
=======
include "../filter_input.php";
include "../database/db_connect.php";// connection file


if (!isset($_SESSION['user_id'])) {
    // Not logged in — redirect to login page
>>>>>>> bcaab525dbca1757cae1a32c88efa8c34fd8ca95
    header("Location: admin_login.php");
    exit();
}

$loggedInId = $_SESSION['user_id'];
<<<<<<< HEAD
$loggedInName = $_SESSION['name'] ?? 'Admin';
=======
>>>>>>> bcaab525dbca1757cae1a32c88efa8c34fd8ca95

// Fetch logged-in admin’s info
$userQueryLoggedIn = "SELECT name, email, role FROM users WHERE user_id = ?";
$stmt = $conn->prepare($userQueryLoggedIn);
$stmt->bind_param("i", $loggedInId);
$stmt->execute();
$loggedInResult = $stmt->get_result();
$loggedInUser = $loggedInResult->fetch_assoc();
$stmt->close();


$userQuery = "SELECT user_id, name, email FROM users WHERE role != 'admin'";
$userResult = $conn->query($userQuery);

// 🧩 Fetch Top Users for Leaderboard
<<<<<<< HEAD
// $leaderboardQuery = "
//     SELECT users.name, user_progress.points
//     FROM user_progress
//     INNER JOIN users ON user_progress.user_id = users.user_id
//     WHERE users.role != 'admin'
//     ORDER BY user_progress.points DESC
//     LIMIT 10
// ";

// $leaderboardResult = $conn->query($leaderboardQuery);
// $users = [];

// if ($leaderboardResult && $leaderboardResult->num_rows > 0) {
//     while ($row = $leaderboardResult->fetch_assoc()) {
//         $users[] = $row;
//     }
// }
=======
$leaderboardQuery = "
    SELECT users.name, user_progress.points
    FROM user_progress
    INNER JOIN users ON user_progress.user_id = users.user_id
    WHERE users.role != 'admin'
    ORDER BY user_progress.points DESC
    LIMIT 10
";

$leaderboardResult = $conn->query($leaderboardQuery);
$users = [];

if ($leaderboardResult && $leaderboardResult->num_rows > 0) {
    while ($row = $leaderboardResult->fetch_assoc()) {
        $users[] = $row;
    }
}
>>>>>>> bcaab525dbca1757cae1a32c88efa8c34fd8ca95
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SMARTY PLAYGROUND</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100">

<<<<<<< HEAD
        
    
=======
        <div class="logout-container">
            <a href="../adminpage/admin_logout.php" title="Logout and end session">
                Logout
                <i class="fa-solid fa-power-off"></i>
            </a>
        </div>

>>>>>>> bcaab525dbca1757cae1a32c88efa8c34fd8ca95
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar"
            class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-40">
            <div class="p-6">
                <div class="flex items-center gap-2 mb-8">
                    <img src="logo.png" alt="Logo">
                </div>
                <nav class="space-y-2">
                    <a href="admin_dashboard.php"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">Dashboard</a>
<<<<<<< HEAD
                    <div x-data="{ open: false }" class="space-y-1">
                    <button @click="open = !open" 
                      class="flex items-center justify-between w-full px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                      <span class="font-medium">Content Management</span>
                      <svg :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    </button>
                    <div x-show="open" class="pl-6 space-y-1">
                      <a href="admin_readingmissions.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition">Reading Mission</a>
                      <a href="admin_readinglessons.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition">Reading Lesson</a>
                    </div>
                  </div>
=======
                    <a href="admin_readingmissions.php"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">Reading
                        Mission</a>
                    <a href="admin_readinglessons.php"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">Reading
                        Lesson</a>
>>>>>>> bcaab525dbca1757cae1a32c88efa8c34fd8ca95
                    <a href="admin_dailyprogress.php"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">Daily
                        Progress</a>
                    <a href="admin_userinfo.php"
                        class="flex items-center gap-3 px-4 py-3 bg-teal-500 text-white rounded-lg font-medium">User
                        Info</a>
                </nav>
<<<<<<< HEAD
                <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
                <div class="absolute bottom-6 left-6 right-6">
                    <a onclick="logout()" 
=======
                <div class="absolute bottom-6 left-6 right-6">
                    <a href="admin_logout.php"
>>>>>>> bcaab525dbca1757cae1a32c88efa8c34fd8ca95
                        class="flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition">Logout</a>
                </div>
            </div>
        </div>

        <!-- ✅ MAIN CONTENT AREA -->
        <div id="mainContent" class="flex-1 ml-0 md:ml-64 transition-all duration-300">
            <div class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
                        <p class="text-gray-500 text-sm">
                            Hi, Welcome Back
                            <span class="font-semibold text-gray-700">
                                <?php echo htmlspecialchars($loggedInUser['name']); ?>
                            </span>!
                        </p>

                    </div>


                    <div class="flex items-center gap-4">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($loggedInUser['name']); ?>&background=random"
                            alt="User" class="w-10 h-10 rounded-full">

                    </div>
                </div>
<<<<<<< HEAD
                
=======

>>>>>>> bcaab525dbca1757cae1a32c88efa8c34fd8ca95
                <div class="flex gap-6">
                    <!-- 🧾 USER INFO TABLE -->
                    <div class="flex-1 bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">User Info</h2>
                        <div class="overflow-x-auto">
<<<<<<< HEAD
                            <div class="flex items-center gap-2 mb-4">
                              <input type="text" id="searchInput" placeholder="Search user..."
                                     class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                              <button id="searchBtn" class="p-2 hover:bg-gray-100 rounded-lg transition">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                              </button>
                            </div>
                  
=======
>>>>>>> bcaab525dbca1757cae1a32c88efa8c34fd8ca95
                            <table class="w-full">
                                <thead class="border-b-2 border-gray-200">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">User Name
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Email</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Action</th>
                                    </tr>
                                </thead>
<<<<<<< HEAD
                                <tbody id="userTableBody">
                                    <?php
                                    
                                    if ($userResult->num_rows > 0) {
                                        while ($row = $userResult->fetch_assoc()) {
                                            echo "<tr data-id='{$row['user_id']}' class='border-b border-gray-100 hover:bg-gray-50 transition'>";
                                            echo "<td class='px-6 py-4 text-gray-800 user-name'>" . htmlspecialchars($row['name']) . "</td>";
                                            echo "<td class='px-6 py-4 text-gray-600 user-email'>" . htmlspecialchars($row['email']) . "</td>";
                                            echo "<td class='px-6 py-4'>
                                                    <div class='flex gap-2'>
                                                      <button onclick=\"openEditModal({$row['user_id']}, '{$row['name']}', '{$row['email']}')\" class='p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition'>
                                                        <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                                          <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'></path>
                                                        </svg>
                                                      </button>
                                                      <button onclick=\"openDeleteModal({$row['user_id']}, '{$row['name']}')\" class='p-2 text-red-500 hover:bg-red-50 rounded-lg transition'>
                                                        <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                                          <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'></path>
                                                        </svg>
                                                      </button>
                                                    </div>
                                                  </td>";


=======
                                <tbody>
                                    <?php
                                    if ($userResult->num_rows > 0) {
                                        while ($row = $userResult->fetch_assoc()) {
                                            echo "<tr class='border-b border-gray-100 hover:bg-gray-50 transition'>";
                                            echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['name']) . "</td>";
                                            echo "<td class='px-6 py-4 text-gray-600'>" . htmlspecialchars($row['email']) . "</td>";
                                            echo "<td class='px-6 py-4'>
                                            <button class='text-gray-400 hover:text-gray-600'>⋮</button>
                                          </td>";
>>>>>>> bcaab525dbca1757cae1a32c88efa8c34fd8ca95
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3' class='text-center py-4 text-gray-500'>No users found</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- 🧭 Pagination -->
                        <div class="flex justify-between items-center mt-6 pt-4 border-t">
                            <span class="text-sm text-gray-600">Page 1 of 1</span>
                            <div class="flex gap-2">
                                <button
                                    class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-gray-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Previous
                                </button>
                                <button class="px-4 py-2 bg-gray-200 rounded-lg font-medium text-gray-700">01</button>
                                <button
                                    class="flex items-center gap-2 px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition font-medium">
                                    Next
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 🏆 USER LEADERBOARD -->
<<<<<<< HEAD
                    <div class="w-96 bg-white rounded-lg shadow-md border-4 border-white-500 p-6">
=======
                    <div class="w-96 bg-white rounded-lg shadow-md border-4 border-teal-500 p-6">
>>>>>>> bcaab525dbca1757cae1a32c88efa8c34fd8ca95
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">User Leaderboard</h2>

                        <!-- 🥇 Top 3 Users -->
                        <div class="flex justify-center gap-4 mb-6">
                            <?php
                            $colors = ['yellow', 'purple', 'teal']; // color scheme for top 3
                            for ($i = 0; $i < 3 && $i < count($users); $i++) {
                                $user = $users[$i];
                                echo "<div class='text-center'>
                                <div class='w-16 h-16 bg-{$colors[$i]}-200 rounded-full mx-auto mb-2 flex items-center justify-center'>
                                    <svg class='w-8 h-8 text-{$colors[$i]}-600' fill='currentColor' viewBox='0 0 20 20'>
                                        <path fill-rule='evenodd' d='M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z' clip-rule='evenodd'></path>
                                    </svg>
                                </div>
                                <p class='text-sm font-bold text-gray-800'>{$user['name']}</p>
                                <p class='text-xs text-gray-500'>{$user['points']} pts</p>
                              </div>";
                            }
                            ?>
                        </div>

                        <!-- 🧾 Remaining Rankings -->
                        <div class="space-y-3">
                            <?php
                            for ($i = 3; $i < count($users); $i++) {
                                $rank = str_pad($i + 1, 2, "0", STR_PAD_LEFT);
                                echo "<div class='flex items-center justify-between p-3 bg-gray-50 rounded-lg'>
                                <div class='flex items-center gap-3'>
                                    <span class='text-sm font-bold text-gray-500'>{$rank}</span>
                                    <span class='text-sm font-medium text-gray-800'>{$users[$i]['name']}</span>
                                </div>
                                <span class='text-sm font-bold text-gray-700'>{$users[$i]['points']}</span>
                              </div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<<<<<<< HEAD
        
        <div id="logoutModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-3xl p-8 max-w-sm w-11/12 mx-4 text-center">
      <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-xl">
        <span class="text-5xl">👋</span>
      </div>
      <h3 class="text-2xl font-bold text-gray-800 mb-2">Logout?</h3>
      <p class="text-gray-600 mb-6">Are you sure you want to logout?</p>
      <div class="flex gap-3">
        <button onclick="closeModal()" class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-2xl font-bold">
          Cancel
        </button>
        <button onclick="confirmLogout()" class="flex-1 bg-gradient-to-r from-orange-400 to-red-500 text-white py-3 rounded-2xl font-bold shadow-lg">
          Logout
        </button>
      </div>
    </div>
    </div>   
                  <!-- ✏️ EDIT MODAL -->
        <div id="editModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div class="bg-white rounded-3xl p-8 max-w-sm w-11/12 mx-4 text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Edit User</h3>
            <p class="text-gray-600 mb-6">Update user details below.</p>
        
            <form id="editForm">
              <input type="hidden" id="editUserId" name="id">
        
              <div class="mb-3 text-left">
                <label class="block text-gray-700 font-medium mb-1">Name</label>
                <input type="text" id="editName" name="name"
                  class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
              </div>
        
              <div class="mb-6 text-left">
                <label class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" id="editEmail" name="email"
                  class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
              </div>
        
              <div class="flex gap-3">
                <button type="button" onclick="closeEditModal()" class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-2xl font-bold">
                  Cancel
                </button>
                <button type="submit"
                  class="flex-1 bg-gradient-to-r from-blue-400 to-blue-600 text-white py-3 rounded-2xl font-bold shadow-lg">
                  Save
                </button>
              </div>
            </form>
          </div>
        </div>
        
        <!-- 🗑️ DELETE MODAL -->
        <div id="deleteModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div class="bg-white rounded-3xl p-8 max-w-sm w-11/12 mx-4 text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-xl">
              <span class="text-5xl">⚠️</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Delete User?</h3>
            <p class="text-gray-600 mb-6" id="deleteMessage"></p> <!-- ✅ Must exist -->
            <div class="flex gap-3">
              <button type="button" onclick="closeModal('deleteModal')" class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-2xl font-bold">Cancel</button>
              <button onclick="confirmDelete()" class="flex-1 bg-gradient-to-r from-orange-400 to-red-500 text-white py-3 rounded-2xl font-bold shadow-lg">Delete</button>
            </div>
          </div>
        </div>


        
    <script>
            function logout() {
            document.getElementById('logoutModal').classList.remove('hidden');
        }
    
        function closeModal() {
            document.getElementById('logoutModal').classList.add('hidden');
        }
        
        
        function confirmLogout() {
          // Redirect to login or home
          alert('Logged out successfully!');
          window.location.href = 'admin_logout.php';
        }
        
                  
        let selectedUserId = null; // store the current user ID globally
        
        // 🧩 Open Edit Modal + prefill form
        function openEditModal(id, name, email) {
          selectedUserId = id;
          document.getElementById('editUserId').value = id;
          document.getElementById('editName').value = name;
          document.getElementById('editEmail').value = email;
          document.getElementById('editModal').classList.remove('hidden');
        }
        
        // 🧩 Close Edit Modal
        function closeEditModal() {
          document.getElementById('editModal').classList.add('hidden');
          document.getElementById('editForm').reset();
        }
        
        // 🧩 Open Delete Modal
        function openDeleteModal(id, name) {
          selectedUserId = id;
          document.getElementById('deleteMessage').textContent = `Are you sure you want to delete ${name}?`;
          document.getElementById('deleteModal').classList.remove('hidden');
        }
        
        // 🧩 Close Delete Modal
        function closeModal(id) {
          document.getElementById(id).classList.add('hidden');
        }
        
        /* ------------------ 🧾 AJAX: EDIT FORM SUBMIT ------------------ */
        document.getElementById('editForm').addEventListener('submit', function(e) {
          e.preventDefault();
        
          const formData = new FormData(this);
        
          fetch('update_user.php', {
            method: 'POST',
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              alert('✅ User updated successfully!');
              closeEditModal();
        
              // Update table row without reload
              const row = document.querySelector(`tr[data-id="${selectedUserId}"]`);
              if (row) {
                row.querySelector('.user-name').textContent = formData.get('name');
                row.querySelector('.user-email').textContent = formData.get('email');
              }
            } else {
              alert('❌ Error updating user: ' + data.message);
            }
          })
          .catch(err => console.error('Error:', err));
        });
        
        /* ------------------ 🧾 AJAX: DELETE CONFIRM ------------------ */
        function confirmDelete() {
          fetch('delete_user.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id=${encodeURIComponent(selectedUserId)}`
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              alert('🗑️ User deleted successfully!');
              closeModal('deleteModal');
        
              // Remove deleted user's row dynamically
              const row = document.querySelector(`tr[data-id="${selectedUserId}"]`);
              if (row) row.remove();
            } else {
              alert('❌ Error deleting user: ' + data.message);
            }
          })
          .catch(err => console.error('Error:', err));
        }
        
        const searchInput = document.getElementById('searchInput');
          const searchBtn = document.getElementById('searchBtn');
          const userTableBody = document.getElementById('userTableBody');
        
          async function searchUsers() {
            const query = searchInput.value.trim();
            const response = await fetch(`search_user.php?q=${encodeURIComponent(query)}`);
            const html = await response.text();
            userTableBody.innerHTML = html;
          }
        
          searchBtn.addEventListener('click', searchUsers);
          searchInput.addEventListener('keyup', () => {
            if (searchInput.value === '') searchUsers(); // show all when cleared
          });


       
    </script>
=======

>>>>>>> bcaab525dbca1757cae1a32c88efa8c34fd8ca95
</body>

</html>