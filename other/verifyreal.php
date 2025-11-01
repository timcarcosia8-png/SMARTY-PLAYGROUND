<?php
include 'database/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $code = $_POST['code'];

    $stmt = $conn->prepare("SELECT user_id, verification_code FROM users WHERE email = ? AND is_verified = 0");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['verification_code'] === $code) {
            $update = $conn->prepare("UPDATE users SET is_verified = 1, status = 'active', verification_code = NULL WHERE user_id = ?");
            $update->bind_param("i", $row['user_id']);
            $update->execute();

            echo "<script>alert('Email verified successfully! You can now log in.'); window.location.href='login.html';</script>";
        } else {
            echo "<script>alert('Incorrect verification code. Please try again.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Email not found or already verified.'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Verify Email</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-purple-900 text-white flex items-center justify-center min-h-screen">
  <form method="POST" class="bg-white text-gray-900 p-8 rounded-2xl shadow-lg w-80">
    <h1 class="text-2xl font-bold mb-4 text-center text-purple-800">Email Verification</h1>
    <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>">
    <input type="text" name="code" maxlength="4" placeholder="Enter 4-digit code" class="w-full p-3 border rounded-lg mb-4" required>
    <button type="submit" class="w-full bg-purple-700 text-white py-3 rounded-lg font-semibold hover:bg-purple-800">Verify</button>
  </form>
</body>
</html>
