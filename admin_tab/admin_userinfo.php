<?php
session_start();
include "db_connect.php";
include "filter_input.php";
include "admin_session.php";

// ✅ Allow both 'admin' and 'superadmin' to access dashboard
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    header("Location: admin_login.php");
    exit();
}

$loggedInId = $_SESSION['user_id'];
$loggedInName = $_SESSION['name'] ?? 'Admin';
$loggedInRole = $_SESSION['role'];

// Fetch logged-in admin’s info
$userQueryLoggedIn = "SELECT name, email, role FROM users WHERE user_id = ?";
$stmt = $conn->prepare($userQueryLoggedIn);
$stmt->bind_param("i", $loggedInId);
$stmt->execute();
$loggedInResult = $stmt->get_result();
$loggedInUser = $loggedInResult->fetch_assoc();
$stmt->close();

// ✅ Dynamic user query based on logged-in role
if ($loggedInRole === 'superadmin') {
    // 🟢 Super Admin: show everyone except other Super Admins
    $userQuery = "SELECT user_id, name, email, role FROM users WHERE role != 'superadmin'";
} else {
    // 🔵 Admin: show only student users
    $userQuery = "SELECT user_id, name, email, role FROM users WHERE role = 'student'";
}

// ✅ PAGINATION SETTINGS
$limit = 10; // number of users per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// ✅ Count total records for pagination
$countQuery = str_replace("SELECT user_id, name, email, role", "SELECT COUNT(*) as total", $userQuery);
$countResult = $conn->query($countQuery);
$totalUsers = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalUsers / $limit);

// ✅ Add LIMIT and OFFSET to main query
$userQuery .= " LIMIT $limit OFFSET $offset";
$userResult = $conn->query($userQuery);

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
                    <div x-data="{ open: false }" class="space-y-1">
                    <button @click="open = !open" 
                      class="flex items-center justify-between w-full px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                      <span class="font-medium">Content Management</span>
                      <svg :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    </button>
                    <div x-show="open" class="pl-6 space-y-1">
                      <!--<a href="admin_readingmissions.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition">Reading Mission</a>-->
                      <a href="admin_readinglessons.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition">Reading Lesson</a>
                    </div>
                  </div>
                    <a href="admin_dailyprogress.php"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">Daily
                        Progress</a>
                    <a href="admin_userinfo.php"
                        class="flex items-center gap-3 px-4 py-3 bg-teal-500 text-white rounded-lg font-medium">User
                        Info</a>
                </nav>
                <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
                <div class="absolute bottom-6 left-6 right-6">
                    <a onclick="logout()" 
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
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($loggedInName); ?>&background=random" class="w-10 h-10 rounded-full">
                    </div>
                </div>
                
                <div class="flex justify-between items-center mb-4">
                      <h2 class="text-2xl font-bold text-gray-800">User Info</h2>
                      <button onclick="openAddUserModal()" 
                              class="bg-gradient-to-r from-teal-500 to-green-500 text-white px-4 py-2 rounded-xl font-semibold hover:opacity-90 transition">
                        ➕ Add User
                      </button>
                      <?php if ($loggedInRole === 'superadmin'): ?>
                      <button onclick="openAddAdminModal()" 
                          class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-2 rounded-xl font-semibold hover:opacity-90 transition">
                        ⚙️ Add Admin/Super Admin
                      </button>
                    <?php endif; ?>

                    </div>
                
                <div class="flex gap-6">
                    
                    

                    
                    <!-- 🧾 USER INFO TABLE -->
                    <div class="flex-1 bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">User Info</h2>
                        <div class="overflow-x-auto">
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
                  
                            <table class="w-full">
                                <thead class="border-b-2 border-gray-200">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">User Name
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Email</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Action</th>
                                    </tr>
                                </thead>
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
                            <span class="text-sm text-gray-600">
                                Page <?php echo $page; ?> of <?php echo $totalPages ?: 1; ?>
                            </span>
                        
                            <div class="flex gap-2">
                                <!-- Previous Button -->
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?>"
                                        class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-gray-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                        Previous
                                    </a>
                                <?php else: ?>
                                    <button class="flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-400 rounded-lg cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                        Previous
                                    </button>
                                <?php endif; ?>
                        
                                <!-- Current Page -->
                                <span class="px-4 py-2 bg-gray-200 rounded-lg font-medium text-gray-700">
                                    <?php echo str_pad($page, 2, '0', STR_PAD_LEFT); ?>
                                </span>
                        
                                <!-- Next Button -->
                                <?php if ($page < $totalPages): ?>
                                    <a href="?page=<?php echo $page + 1; ?>"
                                        class="flex items-center gap-2 px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition font-medium">
                                        Next
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                <?php else: ?>
                                    <button class="flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-400 rounded-lg cursor-not-allowed">
                                        Next
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>

                    
                    
                </div>
            </div>
        </div>
        
        <div id="logoutModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div class="bg-white rounded-3xl p-8 max-w-sm w-11/12 mx-4 text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-xl">
              <span class="text-5xl">👋</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Logout?</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to logout?</p>
            <div class="flex gap-3">
              <button onclick="closeLModal()" class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-2xl font-bold">Cancel</button>
              <button onclick="confirmLogout()" class="flex-1 bg-gradient-to-r from-orange-400 to-red-500 text-white py-3 rounded-2xl font-bold shadow-lg">Logout</button>
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
            
                  <div class="mb-3 text-left">
                    <label class="block text-gray-700 font-medium mb-1">Email</label>
                    <input type="email" id="editEmail" name="email"
                      class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                  </div>
            
                  <!-- 🔒 Password (optional) -->
                  <div class="mb-6 text-left">
                    <label class="block text-gray-700 font-medium mb-1">Password <span class="text-sm text-gray-500">(leave blank to keep current)</span></label>
                    <input type="password" id="editPassword" name="password"
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
        
        
        <!-- ➕ ADD USER MODAL -->
        <div id="addUserModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div class="bg-white rounded-3xl p-8 max-w-lg w-11/12 mx-4 text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Add New User</h3>
            <p class="text-gray-600 mb-6">Fill out the details below.</p>
        
            <form id="addUserForm" class="text-left space-y-3">
              <div>
                <label class="block text-gray-700 font-medium mb-1">First Name</label>
                <input type="text" name="first_name" required
                  class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-teal-400">
              </div>
        
              <div>
                <label class="block text-gray-700 font-medium mb-1">Middle Name</label>
                <input type="text" name="middle_name"
                  class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-teal-400">
              </div>
        
              <div>
                <label class="block text-gray-700 font-medium mb-1">Last Name</label>
                <input type="text" name="last_name" required
                  class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-teal-400">
              </div>
        
              <div>
                <label class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" name="email" required
                  class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-teal-400">
              </div>
        
              <div>
                <label class="block text-gray-700 font-medium mb-1">Address</label>
                <input type="text" name="address"
                  class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-teal-400">
              </div>
        
              <div>
                <label class="block text-gray-700 font-medium mb-1">Birthday</label>
                <input type="date" name="birthday"
                  class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-teal-400">
              </div>
        
              <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeAddUserModal()" 
                        class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-2xl font-bold">
                  Cancel
                </button>
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-teal-500 to-green-500 text-white py-3 rounded-2xl font-bold shadow-lg">
                  Add User
                </button>
                
              </div>
            </form>
          </div>
        </div>
        
        <?php if ($_SESSION['role'] === 'superadmin'): ?>
        <!-- ⚙️ ADD ADMIN / SUPER ADMIN MODAL -->
        <div id="addAdminModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div class="bg-white rounded-3xl p-8 max-w-lg w-11/12 mx-4 text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Add Admin or Super Admin</h3>
            <p class="text-gray-600 mb-6">Create a new admin or super admin account.</p>
        
            <form id="addAdminForm" class="text-left space-y-3">
              <div>
                <label class="block text-gray-700 font-medium mb-1">Full Name</label>
                <input type="text" name="name" required
                  class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-orange-400">
              </div>
        
              <div>
                <label class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" name="email" required
                  class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-orange-400">
              </div>
        
              <div>
                <label class="block text-gray-700 font-medium mb-1">Password</label>
                <input type="password" name="password" required minlength="6"
                  class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-orange-400">
              </div>
        
              <div>
                <label class="block text-gray-700 font-medium mb-1">Role</label>
                <select name="role" required
                  class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-orange-400">
                  <option value="admin">Admin</option>
                  <option value="superadmin">Super Admin</option>
                </select>
              </div>
        
              <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeAddAdminModal()" 
                        class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-2xl font-bold">
                  Cancel
                </button>
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 text-white py-3 rounded-2xl font-bold shadow-lg">
                  Add
                </button>
              </div>
            </form>
          </div>
        </div>
        <?php endif; ?>

        
        


        
    <script>
        
    function logout() { document.getElementById('logoutModal').classList.remove('hidden'); }
    function closeLModal() { document.getElementById('logoutModal').classList.add('hidden'); }
    function confirmLogout() { window.location.href = 'admin_logout.php'; }
        
                  
        let selectedUserId = null; // store the current user ID globally
        
        // 🧩 Open Edit Modal + prefill form
        function openEditModal(id, name, email, role = '') {
            const currentUserRole = "<?php echo $_SESSION['role']; ?>";
        
            // Prevent Admins from editing Super Admin accounts
            if (role === 'superadmin' && currentUserRole !== 'superadmin') {
                alert("You are not allowed to edit a Super Admin account.");
                return;
            }
        
            // Populate the modal fields
            document.getElementById('editUserId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
        
            // Show modal
            document.getElementById('editModal').classList.remove('hidden');
        }
        
        // 🧩 Close Edit Modal
        function closeEditModal() {
          document.getElementById('editModal').classList.add('hidden');
          document.getElementById('editForm').reset();
        }
        
        // 🧩 Open Delete Modal
        function openDeleteModal(id, name, role = '') {
          const currentUserRole = "<?php echo $_SESSION['role']; ?>";
        
          if (role === 'superadmin' && currentUserRole !== 'superadmin') {
            alert("You are not allowed to delete a Super Admin account.");
            return;
          }
        
          selectedUserId = id; // keep track globally
          document.getElementById('deleteMessage').textContent = 
            `Are you sure you want to delete "${name}"?`;
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
        
        
        // 🟢 Open & Close Add User Modal
        function openAddUserModal() {
          document.getElementById('addUserModal').classList.remove('hidden');
        }
        function closeAddUserModal() {
          document.getElementById('addUserModal').classList.add('hidden');
          document.getElementById('addUserForm').reset();
        }
        
        // 🧾 Handle Add User Form Submit
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
          e.preventDefault();
          
          const formData = new FormData(this);
        
          fetch('add_user.php', {
            method: 'POST',
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              alert('✅ User added successfully!');
              closeAddUserModal();
        
              // Dynamically add new user to table
              const tbody = document.getElementById('userTableBody');
              const newRow = document.createElement('tr');
              newRow.className = 'border-b border-gray-100 hover:bg-gray-50 transition';
              newRow.setAttribute('data-id', data.user_id);
        
              newRow.innerHTML = `
                <td class='px-6 py-4 text-gray-800 user-name'>${data.name}</td>
                <td class='px-6 py-4 text-gray-600 user-email'>${data.email}</td>
                <td class='px-6 py-4'>
                  <div class='flex gap-2'>
                    <button onclick="openEditModal(${data.user_id}, '${data.name}', '${data.email}')"
                      class='p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition'>
                      <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2'
                              d='M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'/>
                      </svg>
                    </button>
                    <button onclick="openDeleteModal(${data.user_id}, '${data.name}')"
                      class='p-2 text-red-500 hover:bg-red-50 rounded-lg transition'>
                      <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2'
                              d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'/>
                      </svg>
                    </button>
                  </div>
                </td>`;
              
              tbody.prepend(newRow);
        
            } else {
              alert('❌ Error adding user: ' + data.message);
            }
          })
          .catch(err => console.error('Error:', err));
        });
            
            
        // 🟠 Open & Close Add Admin Modal
        function openAddAdminModal() {
          document.getElementById('addAdminModal').classList.remove('hidden');
        }
        function closeAddAdminModal() {
          document.getElementById('addAdminModal').classList.add('hidden');
          document.getElementById('addAdminForm').reset();
        }
        
        // 🧾 Handle Add Admin Form Submit
        document.getElementById('addAdminForm').addEventListener('submit', function(e) {
          e.preventDefault();
        
          const formData = new FormData(this);
        
          fetch('add_admin.php', {
            method: 'POST',
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              alert(`✅ ${data.role.charAt(0).toUpperCase() + data.role.slice(1)} added successfully!`);
              closeAddAdminModal();
            } else {
              alert('❌ Error adding user: ' + data.message);
            }
          })
          .catch(err => console.error('Error:', err));
        });


       
    </script>
</body>

</html>