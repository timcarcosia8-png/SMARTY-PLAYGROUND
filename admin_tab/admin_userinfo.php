<?php
include "../filter_input.php";
include "../database/db_connect.php";// connection file


if (!isset($_SESSION['user_id'])) {
    // Not logged in — redirect to login page
    header("Location: admin_login.php");
    exit();
}

$loggedInId = $_SESSION['user_id'];

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
                    <img src="logo.png" alt="Logo">
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
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">Daily
                        Progress</a>
                    <a href="admin_userinfo.php"
                        class="flex items-center gap-3 px-4 py-3 bg-teal-500 text-white rounded-lg font-medium">User
                        Info</a>
                </nav>
                <div class="absolute bottom-6 left-6 right-6">
                    <a href="admin_logout.php"
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

                <div class="flex gap-6">
                    <!-- 🧾 USER INFO TABLE -->
                    <div class="flex-1 bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">User Info</h2>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="border-b-2 border-gray-200">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">User Name
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Email</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Action</th>
                                    </tr>
                                </thead>
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
                    <div class="w-96 bg-white rounded-lg shadow-md border-4 border-teal-500 p-6">
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

</body>

</html>