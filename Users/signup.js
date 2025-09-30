// Mobile-only check
if(!/Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent)){
  alert("This app is only for mobile phones.");
  document.body.innerHTML = "<h2>Please open on a mobile device.</h2>";
}

// Sign Up button functionality
document.getElementById('signup-btn').addEventListener('click', () => {
  const email = document.getElementById('signup-email').value;
  const password = document.getElementById('signup-password').value;

  if(email && password){
    alert(`Account created for: ${email}`);
    // After signup, redirect to login page
    window.location.href = "index.html";
  } else {
    alert("Please enter email and password.");
  }
});

// Redirect text link
document.getElementById('redirect-login').addEventListener('click', () => {
  window.location.href = "index.html";
});
