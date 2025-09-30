document.addEventListener("DOMContentLoaded", () => {
  const verifyBtn = document.querySelector(".verify-btn");
  const inputs = document.querySelectorAll(".otp-inputs input");

  // Auto-focus next input
  inputs.forEach((input, index) => {
    input.addEventListener("input", () => {
      if (input.value && index < inputs.length - 1) {
        inputs[index + 1].focus();
      }
    });
  });

  verifyBtn.addEventListener("click", () => {
    let code = "";
    inputs.forEach(input => code += input.value);

    if (code === "12345") {  // fake OTP for now
      alert("✅ Code verified! Password reset successful.");
      window.location.href = "index.html"; // redirect to login
    } else {
      alert("❌ Invalid code. Try again.");
    }
  });
});
