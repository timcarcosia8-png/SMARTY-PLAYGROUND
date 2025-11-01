<?php
include '../database/db_connect.php'; // Adjust the path if needed

// Default query
$query = "SELECT * FROM missions";

// Handle filter form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filterDate = $_POST['date'] ?? '';
    $filterRange = $_POST['range'] ?? '';

    if (!empty($filterDate)) {
        $query = "SELECT * FROM missions WHERE DATE(created_at) = '$filterDate'";
    } elseif (!empty($filterRange)) {
        if ($filterRange == 'Today') {
            $query = "SELECT * FROM missions WHERE DATE(created_at) = CURDATE()";
        } elseif ($filterRange == 'Last 7 Days') {
            $query = "SELECT * FROM missions WHERE created_at >= CURDATE() - INTERVAL 7 DAY";
        } elseif ($filterRange == 'Last 30 Days') {
            $query = "SELECT * FROM missions WHERE created_at >= CURDATE() - INTERVAL 30 DAY";
        } elseif ($filterRange == 'This Month') {
            $query = "SELECT * FROM missions WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
        }
    }
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>READING MISSION - SMARTY PLAYGROUND</title>
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
                        class="flex items-center gap-3 px-4 py-3 bg-teal-500 text-white rounded-lg font-medium">Reading
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
        <!-- Toggle Button -->
        <button id="toggleSidebar" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-gray-800 text-white rounded">
            ☰
        </button>



        <!-- Main Content Area -->
        <div id="mainContent" class="flex-1 ml-0 md:ml-64 transition-all duration-300">
            <div class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Reading Mission</h1>

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
                            <h2 class="text-2xl font-bold text-gray-800">All Reading Missions</h2>
                            <form method="POST" class="flex items-center gap-4">
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">Select Date</label>
                                    <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>"
                                        class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">Date Range</label>
                                    <select name="range"
                                        class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                        <option value="">Select</option>
                                        <option>Today</option>
                                        <option>Last 7 Days</option>
                                        <option>Last 30 Days</option>
                                        <option>This Month</option>
                                    </select>
                                </div>
                                <div class="mt-6">
                                    <button type="submit"
                                        class="px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition font-medium">
                                        Apply Filter
                                    </button>
                                </div>
                            </form>
                        </div>


                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="border-b-2 border-gray-200">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Mission Name
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Points
                                            Reward</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Level
                                            Required</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Created At
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "
              <tr class='border-b border-gray-100 hover:bg-gray-50 transition'>
                <td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['mission_name']) . "</td>
                <td class='px-6 py-4 text-gray-600'>" . htmlspecialchars($row['points_reward']) . "</td>
                <td class='px-6 py-4 text-gray-600'>" . htmlspecialchars($row['level_required']) . "</td>
                <td class='px-6 py-4'>
                  <span class='text-" . ($row['status'] == 'active' ? 'teal' : 'red') . "-600 font-medium'>" . ucfirst($row['status']) . "</span>
                </td>
                <td class='px-6 py-4 text-gray-600'>" . htmlspecialchars($row['created_at']) . "</td>
                <td class='px-6 py-4'>
                  <div class='flex gap-2'>
                    <button class='p-1 text-gray-400 hover:text-gray-600'>
                      <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z'/>
                      </svg>
                    </button>
                    <button class='p-1 text-red-400 hover:text-red-600'>
                      <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='px-6 py-4 text-center text-gray-500'>No missions found.</td></tr>";
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
</body>

</html>