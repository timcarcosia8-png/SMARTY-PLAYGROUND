<?php
include "../database/db_connect.php"; // adjust path if needed

// Default query (fetch all lessons)
$query = "SELECT * FROM lessons ORDER BY created_at DESC";

// If filter form was submitted (via POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? null;
    $range = $_POST['range'] ?? null;

    if (!empty($date)) {
        // Filter by specific date
        $query = "SELECT * FROM lessons WHERE DATE(created_at) = '$date'";
    }

    if (!empty($range)) {
        // Filter by range
        if ($range === 'Today') {
            $query = "SELECT * FROM lessons WHERE DATE(created_at) = CURDATE()";
        } elseif ($range === 'Last 7 Days') {
            $query = "SELECT * FROM lessons WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        } elseif ($range === 'Last 30 Days') {
            $query = "SELECT * FROM lessons WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($range === 'This Month') {
            $query = "SELECT * FROM lessons WHERE MONTH(created_at) = MONTH(CURDATE()) 
                      AND YEAR(created_at) = YEAR(CURDATE())";
        }
    }
}

$result = mysqli_query($conn, $query);
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
                        class="flex items-center gap-3 px-4 py-3 bg-teal-500 text-white rounded-lg font-medium">Reading
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
        <!-- Toggle Button -->
        <button id="toggleSidebar" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-gray-800 text-white rounded">
            ☰
        </button>



        <!-- Main Content Area -->
        <div id="mainContent" class="flex-1 ml-0 md:ml-64 transition-all duration-300">
            <div class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Reading Lessons</h1>

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
                        <img src="https://ui-avatars.com/api/?name=User&background=random" alt="User"
                            class="w-10 h-10 rounded-full">
                    </div>
                </div>

                <!-- Content Area -->
                <div class="p-8">
                    <div class="bg-white rounded-lg shadow-md border-4 border-teal-500 p-6">
                        <!-- Header with Add Button -->
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-800">All Reading Lessons</h2>
                            <button
                                class="flex items-center gap-2 bg-teal-500 text-white px-4 py-2 rounded-lg hover:bg-teal-600 transition font-medium">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Lesson
                            </button>
                        </div>

                        <!-- Filter Section -->
                        <div class="p-8">
                            <h2 class="text-2xl font-bold mb-6 text-gray-800">Lesson Filter</h2>

                            <!-- 🟢 Replace old filter block with this form -->
                            <form method="POST" class="flex items-center gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                                    <input type="date" name="date" id="filterDate"
                                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                                    <select name="range" id="filterRange"
                                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                        <option value="">Select</option>
                                        <option>Today</option>
                                        <option>Last 7 Days</option>
                                        <option>Last 30 Days</option>
                                        <option>This Month</option>
                                    </select>
                                </div>
                                <div class="mt-6">
                                    <button type="submit"
                                        class="px-6 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition font-medium">
                                        Apply Filter
                                    </button>
                                </div>
                            </form>

                            <!-- 🟡 Your lesson table should follow below -->
                            <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">
                                <table class="w-full">
                                    <!-- table headers -->
                                    <!-- table body (Step 2 PHP loop goes here) -->
                                </table>
                            </div>
                        </div>



                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="border-b-2 border-gray-200">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Lesson Title</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Topic</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Difficulty</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Points Reward</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Level Required</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Created At</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "
                                            <tr class='border-b border-gray-100 hover:bg-gray-50 transition'>
                                                <td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['lesson_title']) . "</td>
                                                <td class='px-6 py-4 text-gray-600'>" . htmlspecialchars($row['topic'] ?? '—') . "</td>
                                                <td class='px-6 py-4 text-gray-600'>" . htmlspecialchars($row['difficulty'] ?? '—') . "</td>
                                                <td class='px-6 py-4 text-gray-600'>" . htmlspecialchars($row['points_reward']) . "</td>
                                                <td class='px-6 py-4 text-gray-600'>" . htmlspecialchars($row['level_required']) . "</td>
                                                <td class='px-6 py-4 text-" . ($row['status'] == 'active' ? 'teal' : 'red') . "-600 font-medium'>" . ucfirst($row['status']) . "</td>
                                                <td class='px-6 py-4 text-gray-600'>" . date('Y-m-d', strtotime($row['created_at'])) . "</td>
                                            </tr>";
                                                                            }
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center py-4 text-gray-500'>No lessons found.</td></tr>";
                                    }
                                    ?>
                                </tbody>


                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="flex justify-between items-center mt-6 pt-4 border-t">
                            <span class="text-sm text-gray-600">01 page of 100</span>
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
                                    class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-gray-700">
                                    Next
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            const sidebar = document.getElementById("sidebar");
            const toggleBtn = document.getElementById("toggleSidebar");

            toggleBtn.addEventListener("click", () => {
                sidebar.classList.toggle("-translate-x-full");
            });
        </script>
        <script>
            // ===== Auto-fill today's date =====
            const dateInput = document.getElementById('filterDate');
            const rangeSelect = document.getElementById('filterRange');
            const applyBtn = document.getElementById('applyFilter');

            const today = new Date().toISOString().split('T')[0];
            dateInput.value = today;

            // ===== Apply Filter functionality =====
            applyBtn.addEventListener('click', () => {
                const selectedDate = dateInput.value;
                const selectedRange = rangeSelect.value;

                console.log("Selected Date:", selectedDate);
                console.log("Date Range:", selectedRange);

                // Example: Send to PHP to filter lessons dynamically
                fetch('filter_lessons.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `date=${selectedDate}&range=${selectedRange}`
                })
                    .then(response => response.text())
                    .then(data => {
                        // Replace your table rows dynamically
                        document.querySelector('tbody').innerHTML = data;
                    });
            });
        </script>

</body>

</html>