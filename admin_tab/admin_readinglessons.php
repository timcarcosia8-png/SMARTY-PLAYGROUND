<?php
session_start();
include "db_connect.php";
include "filter_input.php"; 
include "admin_session.php";
// include "user_session.php";
$query = "SELECT * FROM videos ORDER BY uploaded_at DESC";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? null;
    $range = $_POST['range'] ?? null;

    if (!empty($date)) {
        $query = "SELECT * FROM videos WHERE DATE(uploaded_at) = '$date'";
    }

    if (!empty($range)) {
        if ($range === 'Today') {
            $query = "SELECT * FROM videos WHERE DATE(uploaded_at) = CURDATE()";
        } elseif ($range === 'Last 7 Days') {
            $query = "SELECT * FROM videos WHERE uploaded_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        } elseif ($range === 'Last 30 Days') {
            $query = "SELECT * FROM videos WHERE uploaded_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($range === 'This Month') {
            $query = "SELECT * FROM videos WHERE MONTH(uploaded_at) = MONTH(CURDATE()) 
                      AND YEAR(uploaded_at) = YEAR(CURDATE())";
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
    <title>Dashboard - SMARTY PLAYGROUND</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            animation: slideDown 0.3s ease;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
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
                    <img src="logo.png">
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
                        class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">User
                        Info</a>
                </nav>
                <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
                <div class="absolute bottom-6 left-6 right-6">
                    <a onclick="logout()"
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
                    <!--<div class="flex items-center gap-4">-->
                    <!--    <button class="p-2 hover:bg-gray-100 rounded-lg transition">-->
                    <!--        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
                    <!--            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"-->
                    <!--                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>-->
                    <!--        </svg>-->
                    <!--    </button>-->
                    <!--    <button class="p-2 hover:bg-gray-100 rounded-lg transition relative">-->
                    <!--        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
                    <!--            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"-->
                    <!--                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">-->
                    <!--            </path>-->
                    <!--        </svg>-->
                    <!--        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>-->
                    <!--    </button>-->
                    <!--    <img src="https://ui-avatars.com/api/?name=User&background=random" alt="User"-->
                    <!--        class="w-10 h-10 rounded-full">-->
                    <!--</div>-->
                </div>

                <!-- Content Area -->
                <div class="p-8">
                    <div class="bg-white rounded-lg shadow-md border-4 border-white-500 p-6">
                        <!-- Header with Add Button -->
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-800">All Reading Lessons</h2>
                            <button id="openModalBtn"
                                class="flex items-center gap-2 bg-teal-500 text-white px-4 py-2 rounded-lg hover:bg-teal-600 transition font-medium">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Lesson
                            </button>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <!-- 🔍 Search bar -->
                            <div class="flex items-center gap-2 mb-4">
                                <input type="text" id="searchInput" placeholder="Search lesson..."
                                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-400">
                                <button id="searchBtn" class="p-2 hover:bg-gray-100 rounded-lg transition">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </div>

                            <!-- 📋 Table -->
                            <table class="w-full">
                                <thead class="border-b-2 border-gray-200">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Lesson Name
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Description
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Video</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="lessonTableBody">
                                    <?php
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
    </div>
    <!-- Edit User Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center p-6 border-b">
                <h3 class="text-2xl font-bold text-gray-800">Edit User</h3>
                <button id="closeEditModal" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId">

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">
                            Full Name<span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="editUserName" placeholder="Enter full name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">
                            Email<span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="editUserEmail" placeholder="Enter email address"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            required />
                    </div>

                    <button type="submit"
                        class="w-full bg-teal-500 text-white py-3 rounded-lg font-semibold hover:bg-teal-600 transition mt-4">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Delete User?</h3>
                    <p class="text-gray-600">Are you sure you want to delete <strong id="deleteUserName"></strong>? This
                        action cannot be undone.</p>
                </div>

                <div class="flex gap-3">
                    <button id="cancelDelete"
                        class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                        Cancel
                    </button>
                    <button id="confirmDelete"
                        class="flex-1 bg-red-500 text-white py-3 rounded-lg font-semibold hover:bg-red-600 transition">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Lesson Modal -->
    <div id="addLessonModal" class="modal">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-6 border-b">
                <h3 class="text-2xl font-bold text-gray-800">Add New Mission</h3>
                <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <form id="addLessonForm" action="upload_video.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">
                            Lesson Name<span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="lessonName" name="title" placeholder="Enter lesson name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            required />

                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">
                            Description
                        </label>
                        <textarea name="description" placeholder="Enter short description (optional)"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">
                            Upload Video<span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="video" accept="video/mp4,video/mov,video/avi"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg" required />
                    </div>

                    <button type="submit"
                        class="w-full bg-teal-500 text-white py-3 rounded-lg font-semibold hover:bg-teal-600 transition mt-4">
                        Add Lesson
                    </button>
                </form>

            </div>
        </div>
    </div>

    <!-- Edit Video Modal -->
    <div id="editVideoModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center p-6 border-b">
                <h3 class="text-2xl font-bold text-gray-800">Edit Lesson</h3>
                <button id="closeEditVideoBtn" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <form id="editVideoForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="editVideoId">

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">
                            Lesson Name<span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="editLessonName" name="title"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                            required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Description</label>
                        <textarea id="editDescription" name="description"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Replace Video (optional)</label>
                        <input type="file" name="video" accept="video/mp4,video/mov,video/avi"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg" />
                    </div>

                    <button type="submit"
                        class="w-full bg-teal-500 text-white py-3 rounded-lg font-semibold hover:bg-teal-600 transition mt-4">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>


    <div id="logoutModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-3xl p-8 max-w-sm w-11/12 mx-4 text-center">
            <div
                class="w-20 h-20 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-xl">
                <span class="text-5xl">👋</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Logout?</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to logout?</p>
            <div class="flex gap-3">
                <button onclick="closeModal()" class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-2xl font-bold">
                    Cancel
                </button>
                <button onclick="confirmLogout()"
                    class="flex-1 bg-gradient-to-r from-orange-400 to-red-500 text-white py-3 rounded-2xl font-bold shadow-lg">
                    Logout
                </button>
            </div>
        </div>

        <script>
            // Sidebar toggle
            const sidebar = document.getElementById("sidebar");
            const toggleBtn = document.getElementById("toggleSidebar");

            toggleBtn.addEventListener("click", () => {
                sidebar.classList.toggle("-translate-x-full");
            });

            // Modal functionality
            const modal = document.getElementById("addLessonModal");
            const openModalBtn = document.getElementById("openModalBtn");
            const closeModalBtn = document.getElementById("closeModalBtn");
            const addLessonForm = document.getElementById("addLessonForm");

            // Open modal
            openModalBtn.addEventListener("click", () => {
                modal.classList.add("active");
            });

            // Close modal
            closeModalBtn.addEventListener("click", () => {
                modal.classList.remove("active");
            });

            // Close modal when clicking outside
            modal.addEventListener("click", (e) => {
                if (e.target === modal) {
                    modal.classList.remove("active");
                }
            });

            // Handle form submission
            // Remove the JS preventDefault — let PHP handle the form upload
            document.getElementById("addLessonForm").addEventListener("submit", function (e) {
                e.preventDefault();

                const form = e.target;
                const formData = new FormData(form);

                fetch("upload_video.php", {
                    method: "POST",
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            alert("✅ " + data.message);
                            form.reset();
                            document.getElementById("addLessonModal").classList.remove("active");
                            // Optionally reload table content:
                            location.reload();
                        } else {
                            alert("⚠️ " + data.message);
                        }
                    })
                    .catch(err => {
                        console.error("Upload failed:", err);
                        alert("❌ An error occurred during upload.");
                    });
            });



            // Edit Video Modal Controls
            const editVideoModal = document.getElementById("editVideoModal");
            const closeEditVideoBtn = document.getElementById("closeEditVideoBtn");

            function editVideo(id, title, description) {
                document.getElementById("editVideoId").value = id;
                document.getElementById("editLessonName").value = title;
                document.getElementById("editDescription").value = description;

                editVideoModal.classList.add("active");
            }

            closeEditVideoBtn.addEventListener("click", () => {
                editVideoModal.classList.remove("active");
            });

            editVideoModal.addEventListener("click", (e) => {
                if (e.target === editVideoModal) {
                    editVideoModal.classList.remove("active");
                }
            });

            // Handle Edit Form Submission
            document.getElementById("editVideoForm").addEventListener("submit", function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch("update_video.php", {
                    method: "POST",
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            alert("✅ " + data.message);
                            location.reload();
                        } else {
                            alert("⚠️ " + data.message);
                        }
                    })
                    .catch(err => {
                        console.error("Update failed:", err);
                        alert("❌ An error occurred while updating.");
                    });
            });


            function deleteVideo(id) {
                if (confirm("Are you sure you want to delete this video?")) {
                    fetch("delete_video.php?id=" + id, { method: "GET" })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "success") {
                                alert("✅ " + data.message);
                                location.reload();
                            } else {
                                alert("⚠️ " + data.message);
                            }
                        })
                        .catch(err => {
                            console.error("Delete failed:", err);
                            alert("❌ An error occurred during delete.");
                        });
                }
            }




            function logout() {
                document.getElementById('logoutModal').classList.remove('hidden');
            }

            function closeModal() {
                document.getElementById('logoutModal').classList.add('hidden');
            }


            function confirmLogout() {
                // Redirect to login or home
                // alert('Logged out successfully!');
                window.location.href = 'admin_logout.php';
            }

            // Close modal with Escape key
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape" && modal.classList.contains("active")) {
                    modal.classList.remove("active");
                }
            });

            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            const lessonTableBody = document.getElementById('lessonTableBody');

            searchBtn.addEventListener('click', performSearch);
            searchInput.addEventListener('keyup', performSearch);

            function performSearch() {
                const query = searchInput.value.trim();

                fetch('search_lessons.php?q=' + encodeURIComponent(query))
                    .then(response => response.text())
                    .then(html => {
                        lessonTableBody.innerHTML = html;
                    })
                    .catch(err => {
                        console.error('Search failed:', err);
                        lessonTableBody.innerHTML = "<tr><td colspan='5' class='text-center py-4 text-red-500'>Error fetching results</td></tr>";
                    });
            }

        </script>
</body>

</html>