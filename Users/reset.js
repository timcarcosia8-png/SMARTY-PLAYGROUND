document.addEventListener("DOMContentLoaded", () => {
  const resetBtn = document.querySelector(".reset-btn");

  if (resetBtn) {
    resetBtn.addEventListener("click", () => {
      // Simulate sending email code
      alert("A 5-digit reset code has been sent to your email.");
      
      // Redirect to verification page
      window.location.href = "verify.html";
    });
  }
});
