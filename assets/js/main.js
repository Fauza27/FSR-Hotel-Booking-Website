document.addEventListener("DOMContentLoaded", function () {
  // Mobile menu toggle
  const menuToggle = document.querySelector(".menu-toggle");
  const navLinks = document.querySelector(".nav-links");

  if (menuToggle) {
    menuToggle.addEventListener("click", function () {
      navLinks.classList.toggle("active");
    });
  }

  // Auto close alerts after 5 seconds
  const alerts = document.querySelectorAll(".alert");
  if (alerts.length > 0) {
    setTimeout(function () {
      alerts.forEach((alert) => {
        alert.style.opacity = "0";
        alert.style.transition = "opacity 0.5s ease";

        setTimeout(function () {
          alert.style.display = "none";
        }, 500);
      });
    }, 5000);
  }

  // Room search form validation
  const searchForm = document.querySelector('form[action*="/rooms"]');
  if (searchForm) {
    searchForm.addEventListener("submit", function (e) {
      const checkIn = document.getElementById("check_in");
      const checkOut = document.getElementById("check_out");

      if (checkIn && checkOut) {
        const checkInDate = new Date(checkIn.value);
        const checkOutDate = new Date(checkOut.value);

        if (checkOutDate <= checkInDate) {
          e.preventDefault();
          alert("Check-out date must be after check-in date");
        }
      }
    });

    // Update check-out min date when check-in changes
    const checkInInput = document.getElementById("check_in");
    const checkOutInput = document.getElementById("check_out");

    if (checkInInput && checkOutInput) {
      checkInInput.addEventListener("change", function () {
        const checkInDate = new Date(this.value);
        const nextDay = new Date(checkInDate);
        nextDay.setDate(nextDay.getDate() + 1);

        const year = nextDay.getFullYear();
        const month = String(nextDay.getMonth() + 1).padStart(2, "0");
        const day = String(nextDay.getDate()).padStart(2, "0");

        checkOutInput.min = `${year}-${month}-${day}`;

        // If check-out is before new check-in + 1, update it
        if (new Date(checkOutInput.value) <= checkInDate) {
          checkOutInput.value = `${year}-${month}-${day}`;
        }
      });
    }
  }

  // Image gallery on room detail page
  const thumbnails = document.querySelectorAll(".thumbnail");
  const mainImage = document.getElementById("main-room-image");

  if (thumbnails.length > 0 && mainImage) {
    thumbnails.forEach((thumbnail) => {
      thumbnail.addEventListener("click", function () {
        // Remove active class from all thumbnails
        thumbnails.forEach((thumb) => thumb.classList.remove("active"));

        // Add active class to clicked thumbnail
        this.classList.add("active");

        // Update main image
        mainImage.src = this.getAttribute("data-image");
      });
    });
  }

  // Password visibility toggle
  const passwordToggles = document.querySelectorAll(".password-toggle");

  if (passwordToggles.length > 0) {
    passwordToggles.forEach((toggle) => {
      toggle.addEventListener("click", function () {
        const passwordField = document.getElementById(this.getAttribute("data-target"));

        if (passwordField.type === "password") {
          passwordField.type = "text";
          this.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
          passwordField.type = "password";
          this.innerHTML = '<i class="fas fa-eye"></i>';
        }
      });
    });
  }

  // Form validation for registration
  const registerForm = document.getElementById("register-form");

  if (registerForm) {
    registerForm.addEventListener("submit", function (e) {
      const password = document.getElementById("password");
      const confirmPassword = document.getElementById("confirm_password");

      if (password.value !== confirmPassword.value) {
        e.preventDefault();
        alert("Passwords do not match");
      }
    });
  }

  // Date formatting for display
  const formatDates = document.querySelectorAll(".format-date");

  if (formatDates.length > 0) {
    formatDates.forEach((date) => {
      const originalDate = date.getAttribute("data-date");
      const dateObj = new Date(originalDate);

      const options = { year: "numeric", month: "short", day: "numeric" };
      date.textContent = dateObj.toLocaleDateString("en-US", options);
    });
  }
});
