<?php
session_start();
include "db_connect.php";
include "filter_input.php";
include "admin_session.php";

// ✅ Access control — allow only admin or superadmin
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')
) {
    header("Location: admin_login.php");
    exit();
}

$loggedInId = $_SESSION['user_id'];
$loggedInName = $_SESSION['name'] ?? 'Admin';
$loggedInRole = $_SESSION['role'];

// ✅ ROLE-BASED VIEW FILTER
if ($loggedInRole === 'superadmin') {
    // Show everyone except superadmins
    $roleCondition = "u.role != 'superadmin'";
} else {
    // Regular admin only sees students
    $roleCondition = "u.role = 'student'";
}

// ✅ Fetch user progress data based on role
$sql = "
    SELECT 
        u.user_id,
        u.name,
        u.email,
        u.status,
        u.role,
        up.progress_percent,
        up.missions_completed,
        up.lessons_completed
    FROM users u
    LEFT JOIN user_progress up ON u.user_id = up.user_id
    WHERE $roleCondition
";
$result = $conn->query($sql);

// ✅ Active user growth
$todayActiveQuery = $conn->query("
    SELECT COUNT(DISTINCT up.user_id) AS total
    FROM user_progress up
    JOIN users u ON u.user_id = up.user_id
    WHERE DATE(up.last_updated) = CURDATE() AND $roleCondition
");
$todayActive = $todayActiveQuery->fetch_assoc()['total'] ?? 0;

$yesterdayActiveQuery = $conn->query("
    SELECT COUNT(DISTINCT up.user_id) AS total
    FROM user_progress up
    JOIN users u ON u.user_id = up.user_id
    WHERE DATE(up.last_updated) = CURDATE() - INTERVAL 1 DAY AND $roleCondition
");
$yesterdayActive = $yesterdayActiveQuery->fetch_assoc()['total'] ?? 0;

$activeGrowth = $yesterdayActive > 0
    ? round((($todayActive - $yesterdayActive) / $yesterdayActive) * 100, 1)
    : 0;

// Optional placeholders for future metrics
$todayMissions = 0;
$missionGrowth = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - SMARTY PLAYGROUND</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <style>body { font-family: 'Inter', sans-serif; }</style>
</head>

<body class="bg-gray-100">
<div class="logout-container">
  <a href="admin_logout.php" title="Logout and end session">Logout <i class="fa-solid fa-power-off"></i></a>
</div>

<div class="flex h-screen">
  <!-- Sidebar -->
  <div id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-40">
    <div class="p-6">
      <div class="flex items-center gap-2 mb-8">
        <img src="logo.png">
      </div>
      <nav class="space-y-2">
        <a href="admin_dashboard.php" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">Dashboard</a>

        <div x-data="{ open: false }" class="space-y-1">
          <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
            <span class="font-medium">Content Management</span>
            <svg :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
          <div x-show="open" class="pl-6 space-y-1">
            <a href="admin_readinglessons.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition">Reading Lesson</a>
          </div>
        </div>

        <a href="admin_dailyprogress.php" class="flex items-center gap-3 px-4 py-3 bg-teal-500 text-white rounded-lg font-medium">Daily Progress</a>
        <a href="admin_userinfo.php" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">User Info</a>

        <!--<?php if ($loggedInRole === 'superadmin'): ?>-->
          <!-- 🟣 Superadmin-only link -->
        <!--  <a href="admin_accounts.php" class="flex items-center gap-3 px-4 py-3 text-purple-600 hover:bg-purple-50 rounded-lg transition font-medium">-->
        <!--    Manage Admins-->
        <!--  </a>-->
        <!--<?php endif; ?>-->
      </nav>
      <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

      <div class="absolute bottom-6 left-6 right-6">
        <a onclick="logout()" class="flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition">Logout</a>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="ml-64 flex-1">
    <div class="bg-gray border-b px-8 py-6">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Daily Progress</h1>
          <p class="text-gray-500 text-sm mt-1">
            Viewing data as <strong><?= ucfirst($loggedInRole) ?></strong>
          </p>
        </div>
        <div class="flex items-center gap-4">
          <div class="w-10 h-10 bg-purple-200 rounded-full flex items-center justify-center">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($loggedInName); ?>&background=random" class="w-10 h-10 rounded-full">
          </div>
        </div>
      </div>
    </div>

    <!-- Stats -->
    <div class="p-8">
      <div class="grid grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
          <div class="flex items-center justify-between mb-2">
            <span class="text-gray-600 text-sm">Total Active Today</span>
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm6 0h-6m0 0H9"></path>
              </svg>
            </div>
          </div>
          <div class="text-3xl font-bold text-gray-800"><?= $todayActive ?></div>
          <div class="text-xs <?= $activeGrowth >= 0 ? 'text-green-600' : 'text-red-600' ?> mt-1">
            <?= $activeGrowth >= 0 ? '+' : '' ?><?= $activeGrowth ?>% from yesterday
          </div>
        </div>
      </div>

      <!-- User Table -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-bold text-gray-800">User Progress Overview</h2>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">User Name</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Email</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Role</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Progress</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <?php
                    $progress = (int)($row['progress_percent'] ?? 0);
                    $color = $progress >= 80 ? 'bg-teal-500' : ($progress >= 50 ? 'bg-yellow-500' : 'bg-red-500');
                  ?>
                  <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-800"><?= htmlspecialchars($row['name']) ?></td>
                    <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($row['email']) ?></td>
                    <td class="px-6 py-4 text-gray-600"><?= ucfirst($row['role']) ?></td>
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-200 rounded-full h-2 max-w-[100px]">
                          <div class="<?= $color ?> h-2 rounded-full" style="width: <?= $progress ?>%"></div>
                        </div>
                        <span class="text-sm text-gray-600"><?= $progress ?>%</span>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <span class="px-3 py-1 rounded-full text-xs font-medium <?= $row['status'] === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' ?>">
                        <?= ucfirst($row['status']) ?>
                      </span>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="text-center py-6 text-gray-500">No user progress found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
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
      <button onclick="closeModal()" class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-2xl font-bold">Cancel</button>
      <button onclick="confirmLogout()" class="flex-1 bg-gradient-to-r from-orange-400 to-red-500 text-white py-3 rounded-2xl font-bold shadow-lg">Logout</button>
    </div>
  </div>
</div>

<script>
function logout() { document.getElementById('logoutModal').classList.remove('hidden'); }
function closeModal() { document.getElementById('logoutModal').classList.add('hidden'); }
function confirmLogout() { window.location.href = 'admin_logout.php'; }
</script>
</body>
</html>
