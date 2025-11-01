<?php
include "../filter_input.php";
include "../database/db_connect.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

$loggedInId = $_SESSION['user_id'];
$loggedInName = $_SESSION['name'] ?? 'Admin';

// ---------- FETCH DASHBOARD STATS ----------
$userQuery = $conn->query("SELECT COUNT(*) AS total_users FROM users WHERE role != 'admin'");
$userTotal = $userQuery->fetch_assoc()['total_users'] ?? 0;

$activeQuery = $conn->query("SELECT COUNT(*) AS active_users FROM users WHERE status='active' AND role != 'admin'");
$activeUsers = $activeQuery->fetch_assoc()['active_users'] ?? 0;

$missionQuery = $conn->query("SELECT COUNT(*) AS total_missions FROM missions WHERE status='completed'");
$totalMissions = $missionQuery->fetch_assoc()['total_missions'] ?? 0;

$lessonQuery = $conn->query("SELECT COUNT(*) AS total_lessons FROM lessons");
$totalLessons = $lessonQuery->fetch_assoc()['total_lessons'] ?? 0;

$progressQuery = $conn->query("
    SELECT AVG(user_progress.progress_percent) AS avg_progress
    FROM user_progress
    INNER JOIN users ON user_progress.user_id = users.user_id
    WHERE users.role != 'admin'
");
$avgProgress = round($progressQuery->fetch_assoc()['avg_progress'] ?? 0, 2);

$pointsQuery = $conn->query("
    SELECT SUM(user_progress.points) AS total_points
    FROM user_progress
    INNER JOIN users ON user_progress.user_id = users.user_id
    WHERE users.role != 'admin'
");
$totalPoints = $pointsQuery->fetch_assoc()['total_points'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SMARTY PLAYGROUND</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="logout-container">
            <a href="../adminpage/admin_logout.php" title="Logout and end session">
                Logout
                <i class="fa-solid fa-power-off"></i>
            </a>
        </div>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar"
            class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg transition-transform duration-300 z-40">
            <div class="p-6">
                <div class="flex items-center gap-2 mb-8">
                    <img src="logo.png" alt="Logo">
                </div>
                <nav class="space-y-2">
                    <a href="admin_dashboard.php"
                        class="flex items-center gap-3 px-4 py-3 bg-teal-500 text-white rounded-lg font-medium">Dashboard</a>
                    <a href="admin_readingmissions.php"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">Reading
                        Mission</a>
                    <a href="admin_readinglessons.php"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">Reading
                        Lesson</a>
                    <a href="admin_dailyprogress.php"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">Daily
                        Progress</a>
                    <a href="admin_userinfo.php"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">User
                        Info</a>
                </nav>
                <div class="absolute bottom-6 left-6 right-6">
                    <a href="admin_logout.php"
                        class="flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition">Logout</a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="ml-64 flex-1">
            <!-- Header -->
            <div class="bg-white border-b px-8 py-6 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        Welcome, <?php echo htmlspecialchars($loggedInName); ?> 👋
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">Overview of system activity</p>
                </div>
                <div class="w-10 h-10 bg-teal-200 rounded-full flex items-center justify-center">
                    <span class="text-teal-700 font-semibold text-sm">AD</span>
                </div>
            </div>

            <!-- Dashboard Stats -->
            <div class="p-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl p-6 shadow-sm border">
                    <h3 class="text-gray-600 text-sm mb-1">Total Users</h3>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $userTotal; ?></p>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-sm border">
                    <h3 class="text-gray-600 text-sm mb-1">Active Users</h3>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $activeUsers; ?></p>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-sm border">
                    <h3 class="text-gray-600 text-sm mb-1">Completed Missions</h3>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $totalMissions; ?></p>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-sm border">
                    <h3 class="text-gray-600 text-sm mb-1">Total Lessons</h3>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $totalLessons; ?></p>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-sm border">
                    <h3 class="text-gray-600 text-sm mb-1">Average Progress</h3>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $avgProgress; ?>%</p>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-sm border">
                    <h3 class="text-gray-600 text-sm mb-1">Total Points</h3>
                    <p class="text-3xl font-bold text-gray-800"><?php echo number_format($totalPoints); ?></p>
                </div>
            </div>

            <!-- Table: User Progress -->
            <div class="px-8 pb-8">
                <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">
                    <div class="p-6 border-b flex justify-between items-center">
                        <h2 class="text-xl font-bold text-gray-800">User Progress Overview</h2>
                    </div>
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">User</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Email</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Progress</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Points</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php
                            $usersResult = $conn->query("
                            SELECT users.name, users.email, users.status, 
                                user_progress.points, user_progress.level, 
                                user_progress.missions_completed, user_progress.lessons_completed,
                                user_progress.progress_percent
                            FROM users
                            LEFT JOIN user_progress ON users.user_id = user_progress.user_id
                            WHERE users.role != 'admin'
                            ORDER BY users.name ASC
                            ");


                            if ($usersResult && $usersResult->num_rows > 0) {
                                while ($row = $usersResult->fetch_assoc()) {
                                    echo "
                    <tr class='hover:bg-gray-50 transition'>
                      <td class='px-6 py-4 font-medium text-gray-800'>" . htmlspecialchars($row['name']) . "</td>
                      <td class='px-6 py-4 text-gray-600'>" . htmlspecialchars($row['email']) . "</td>
                      <td class='px-6 py-4 text-gray-700'>" . ($row['progress_percent'] ?? 0) . "%</td>
                      <td class='px-6 py-4 text-gray-700'>" . ($row['points'] ?? 0) . "</td>
                      <td class='px-6 py-4'>
                        <span class='px-3 py-1 rounded-full text-xs " .
                                        ($row['status'] === 'active' ? "bg-green-100 text-green-700" : "bg-red-100 text-red-700") .
                                        "'>" . ucfirst($row['status']) . "</span>
                      </td>
                    </tr>
                  ";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-6 text-gray-500'>No user data available.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>