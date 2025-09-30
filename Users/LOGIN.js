// Mobile-only check
if(!/Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent)){
  alert("This app is only for mobile phones.");
  document.body.innerHTML = "<h2>Please open on a mobile device.</h2>";
}
// Redirect to Sign Up page
document.getElementById('redirect-signup').addEventListener('click', () => {
  window.location.href = "signup.html";
});

// Login button functionality
document.getElementById('login-btn').addEventListener('click', () => {
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;

  if(email && password){
    alert(`Logged in with: ${email}`);
    // Add next page logic here
  } else {
    alert("Please enter email and password.");
  }
});
