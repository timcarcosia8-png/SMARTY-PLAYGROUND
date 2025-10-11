<?php
include "../database/db_connect.php"; // Adjust this path if needed

// Fetch all user progress data (excluding admins)
$sql = "
    SELECT 
        u.user_id,
        u.name,
        u.email,
        u.status,
        up.points,
        up.progress_percent,
        up.missions_completed,
        up.lessons_completed
    FROM users u
    LEFT JOIN user_progress up ON u.user_id = up.user_id
    WHERE u.role != 'admin'
    ORDER BY up.points DESC
";
$result = $conn->query($sql);
$todayActiveQuery = $conn->query("
    SELECT COUNT(DISTINCT user_id) AS total
    FROM user_progress
    WHERE DATE(last_updated) = CURDATE()
");
$todayActive = $todayActiveQuery->fetch_assoc()['total'] ?? 0;

$yesterdayActiveQuery = $conn->query("
    SELECT COUNT(DISTINCT user_id) AS total
    FROM user_progress
    WHERE DATE(last_updated) = CURDATE() - INTERVAL 1 DAY
");
$yesterdayActive = $yesterdayActiveQuery->fetch_assoc()['total'] ?? 0;

$activeGrowth = $yesterdayActive > 0
    ? round((($todayActive - $yesterdayActive) / $yesterdayActive) * 100, 1)
    : 0;


/* ================================
   2️⃣ TOTAL MISSIONS COMPLETED
================================ */
$todayMissionsQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM user_missions
    WHERE DATE(completed_at) = CURDATE()
");
$todayMissions = $todayMissionsQuery->fetch_assoc()['total'] ?? 0;

$yesterdayMissionsQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM user_missions
    WHERE DATE(completed_at) = CURDATE() - INTERVAL 1 DAY
");
$yesterdayMissions = $yesterdayMissionsQuery->fetch_assoc()['total'] ?? 0;

$missionGrowth = $yesterdayMissions > 0
    ? round((($todayMissions - $yesterdayMissions) / $yesterdayMissions) * 100, 1)
    : 0;


/* ================================
   3️⃣ AVERAGE TIME SPENT
================================ */
// If you don’t have time tracking, we’ll simulate
// Replace this later with your real table (like user_sessions)
$averageTimeSpent = 24; // minutes today (sample)
$yesterdayTimeSpent = 22; // sample yesterday
$timeGrowth = round((($averageTimeSpent - $yesterdayTimeSpent) / $yesterdayTimeSpent) * 100, 1);


/* ================================
   4️⃣ TOTAL POINTS EARNED
================================ */
$todayPointsQuery = $conn->query("
    SELECT SUM(points) AS total
    FROM user_progress
    WHERE DATE(last_updated) = CURDATE()
");
$todayPoints = $todayPointsQuery->fetch_assoc()['total'] ?? 0;

$yesterdayPointsQuery = $conn->query("
    SELECT SUM(points) AS total
    FROM user_progress
    WHERE DATE(last_updated) = CURDATE() - INTERVAL 1 DAY
");
$yesterdayPoints = $yesterdayPointsQuery->fetch_assoc()['total'] ?? 0;

$pointsGrowth = $yesterdayPoints > 0
    ? round((($todayPoints - $yesterdayPoints) / $yesterdayPoints) * 100, 1)
    : 0;

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
    
    <div class="logout-container">
            <a href="../adminpage/admin_logout.php" title="Logout and end session">
                Logout
                <i class="fa-solid fa-power-off"></i>
            </a>
        </div>

    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar"
            class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-40">
            <div class="p-6">
                <div class="flex items-center gap-2 mb-8">
                    <img src="logo.png">
                </div>
                <nav class="space-y-2">
                    <a href="admin_dashboard.php"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">Dashboard</a>
                    <a href="admin_readingmissions.php"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">Reading
                        Mission</a>
                    <a href="admin_readinglessons.php"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">Reading
                        Lesson</a>
                    <a href="admin_dailyprogress.php"
                        class="flex items-center gap-3 px-4 py-3 bg-teal-500 text-white rounded-lg font-medium">Daily
                        Progress</a>
                    <a href="admin_userinfo.php"
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">User
                        Info</a>
                </nav>


                <div class="absolute bottom-6 left-6 right-6">
                    <a href="#"
                        class="flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition">
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="ml-64 flex-1">
            <!-- Header -->
            <div class="bg-white border-b px-8 py-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Daily Progress</h1>
                        <p class="text-gray-500 text-sm mt-1">Track user activity and progress</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <button class="p-2 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                        <button class="p-2 hover:bg-gray-100 rounded-lg transition relative">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <div class="w-10 h-10 bg-purple-200 rounded-full flex items-center justify-center">
                            <span class="text-purple-700 font-semibold text-sm">US</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-8">
                <!-- Date Filter -->
                <div class="mb-6 flex gap-4 items-center">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                        <input type="date" id="filterDate"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                            value="2024-10-06">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                        <select id = "filterRange"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option>Today</option>
                            <option>Last 7 Days</option>
                            <option>Last 30 Days</option>
                            <option>This Month</option>
                        </select>
                    </div>
                    <div class="mt-6">
                        <button id = "applyFilter"
                            class="px-6 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition font-medium">
                            Apply Filter
                        </button>
                    </div>
                </div>

                <!-- Stats Overview -->
                <div class="grid grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 text-sm">Total Active Today</span>
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-800"><?= $todayActive ?></div>
                        <div class="text-xs <?= $activeGrowth >= 0 ? 'text-green-600' : 'text-red-600' ?> mt-1">
                            <?= $activeGrowth >= 0 ? '+' : '' ?><?= $activeGrowth ?>% from yesterday
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 text-sm">Missions Completed</span>
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="text-3xl font-bold text-gray-800"><?= $todayMissions ?></div>
                        <div class="text-xs <?= $missionGrowth >= 0 ? 'text-green-600' : 'text-red-600' ?> mt-1">
                            <?= $missionGrowth >= 0 ? '+' : '' ?><?= $missionGrowth ?>% from yesterday
                        </div>
                    </div>


                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                        <!-- Avg. Time Spent -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 text-sm">Avg. Time Spent</span>
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-800"><?= $averageTimeSpent ?>
                        </div>
                        <div class="text-xs <?= $timeGrowth >= 0 ? 'text-green-600' : 'text-red-600' ?> mt-1">
                            <?= $timeGrowth >= 0 ? '+' : '' ?><?= $timeGrowth ?>% from yesterday
                        </div>

                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                        <!-- Points Earned -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 text-sm">Points Earned</span>
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-800"><?= number_format($todayPoints) ?></div>
                        <div class="text-xs <?= $pointsGrowth >= 0 ? 'text-green-600' : 'text-red-600' ?> mt-1">
                            <?= $pointsGrowth >= 0 ? '+' : '' ?><?= $pointsGrowth ?>% from yesterday
                        </div>
                    </div>

                </div>

                <!-- User Progress Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold text-gray-800">User Progress Today</h2>
                            <button
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                                Export Data
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">User Name</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Missions</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Lessons</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Time Spent</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Points</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Progress</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <?php
                                        $initials = strtoupper(substr($row['name'], 0, 1));
                                        $progress = (int) $row['progress_percent'];
                                        $color = $progress >= 80 ? 'bg-teal-500' : ($progress >= 50 ? 'bg-yellow-500' : 'bg-red-500');
                                        ?>
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-10 h-10 bg-teal-200 rounded-full flex items-center justify-center">
                                                        <span
                                                            class="text-teal-700 font-semibold text-sm"><?= $initials ?></span>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-gray-800">
                                                            <?= htmlspecialchars($row['name']) ?>
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            <?= htmlspecialchars($row['email']) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-700"><?= $row['missions_completed'] ?></td>
                                            <td class="px-6 py-4 text-gray-700"><?= $row['lessons_completed'] ?></td>
                                            <td class="px-6 py-4 text-gray-700">--</td>
                                            <td class="px-6 py-4 text-gray-700"><?= $row['points'] ?></td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1 bg-gray-200 rounded-full h-2 max-w-[100px]">
                                                        <div class="<?= $color ?> h-2 rounded-full"
                                                            style="width: <?= $progress ?>%"></div>
                                                    </div>
                                                    <span class="text-sm text-gray-600"><?= $progress ?>%</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php if ($row['status'] == 'active'): ?>
                                                    <span
                                                        class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Active</span>
                                                <?php else: ?>
                                                    <span
                                                        class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-gray-500">No user progress found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>

                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-between items-center p-6 border-t border-gray-200">
                        <span class="text-sm text-gray-600">Showing 1-5 of 85 users</span>
                        <div class="flex gap-2">
                            <button
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">Previous</button>
                            <button class="px-4 py-2 bg-teal-500 text-white rounded-lg text-sm font-medium">1</button>
                            <button
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">2</button>
                            <button
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">3</button>
                            <button
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">Next</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
  // Auto-fill today's date in the date input
  const dateInput = document.getElementById('filterDate');
  const rangeSelect = document.getElementById('filterRange');
  const applyBtn = document.getElementById('applyFilter');

  // Set today's date as default
  const today = new Date().toISOString().split('T')[0];
  dateInput.value = today;

  // Handle Apply Filter button click
  applyBtn.addEventListener('click', () => {
    const selectedDate = dateInput.value;
    const selectedRange = rangeSelect.value;

    console.log("Selected Date:", selectedDate);
    console.log("Date Range:", selectedRange);

    // OPTIONAL: Send to PHP to refresh lessons table dynamically
    fetch('filter_lessons.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `date=${selectedDate}&range=${selectedRange}`
    })
    .then(response => response.text())
    .then(data => {
      document.getElementById('lessonTable').innerHTML = data;
    });
  });
</script>

</body>

</html>