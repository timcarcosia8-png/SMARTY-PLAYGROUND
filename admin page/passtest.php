<?php
// $enteredPassword = 'admin123'; // the password you use to log in
// $storedHash = '$2y$10$8.KR3z7G6UhpQSTUeE.5GuKmQe7wZ7yTSCmMbtT5.C8HksIh2H5xu'; // copy exactly what’s in your DB

// if (password_verify($enteredPassword, $storedHash)) {
//     echo "✅ Password works!";
// } else {
//     echo "❌ Invalid password!";
// }

echo password_hash("admin123", PASSWORD_DEFAULT);


?>


