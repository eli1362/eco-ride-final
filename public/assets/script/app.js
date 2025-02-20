const navBtn = document.querySelector(".nav__btn")
const navMenu = document.querySelector(".nav-menu")
const nav = document.querySelector(".nav")


let navOpen = false;

navBtn.addEventListener("click", function () {
    if (navOpen) {
        navBtn.classList.remove("nav__btn--open")
        navMenu.classList.remove("nav-menu--open")
        nav.classList.remove("nav--change-color")
        navOpen = false;
    } else {
        navBtn.classList.add("nav__btn--open")
        navMenu.classList.add("nav-menu--open")
        nav.classList.add("nav--change-color")
        navOpen = true
    }
})


//********************** for the login menu  ********************** //


document.getElementById("toggleDropdown").addEventListener("click", function () {
    const menu = document.getElementById("menu");
    const icon = document.getElementById("toggleIcon");

    // Toggle the dropdown menu
    if (menu.style.display === "block") {
        menu.style.display = "none"; // Hide menu
        icon.classList.remove("fa-minus"); // Remove minus icon
        icon.classList.add("fa-plus"); // Restore plus icon
    } else {
        menu.style.display = "block"; // Show menu
        icon.classList.remove("fa-plus"); // Remove plus icon
        icon.classList.add("fa-minus"); // Change to minus icon
    }
});


//********************** for registration  ********************** //

function validateForm() {
    let isValid = true;

    // Clear previous error messages
    document.getElementById("full_name_error").innerText = "";
    document.getElementById("email_error").innerText = "";
    document.getElementById("password_error").innerText = "";
    document.getElementById("confirm_password_error").innerText = "";

    // Get form values
    const fullName = document.getElementById("full_name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const confirmPassword = document.getElementById("confirm-password").value.trim();

    // Validate Full Name
    if (fullName.length < 3 || /[^a-zA-Z\s]/.test(fullName)) {
        document.getElementById("full_name_error").innerText = "Full name must be at least 3 characters and contain only letters and spaces.";
        isValid = false;
    }

    // Validate Email
    if (!/\S+@\S+\.\S+/.test(email)) {
        document.getElementById("email_error").innerText = "Please enter a valid email address.";
        isValid = false;
    }

    // Validate Password
    if (password.length < 8 || !/[A-Za-z]/.test(password) || !/[0-9]/.test(password) || !/[\W_]/.test(password)) {
        document.getElementById("password_error").innerText = "Password must be at least 8 characters and contain at least one letter, one number, and one special character.";
        isValid = false;
    }

    // Validate Confirm Password
    if (password !== confirmPassword) {
        document.getElementById("confirm_password_error").innerText = "Passwords do not match.";
        isValid = false;
    }

    return isValid;
}

//********************** login  ********************** //

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('login-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            let isValid = true;
            const emailField = document.getElementById('email');
            const emailError = document.getElementById('email-error');
            if (emailField.value === '') {
                emailError.textContent = 'Email is required';
                isValid = false;
            } else {
                emailError.textContent = '';
            }

            const passwordField = document.getElementById('password');
            const passwordError = document.getElementById('password-error');
            if (passwordField.value === '') {
                passwordError.textContent = 'Password is required';
                isValid = false;
            } else {
                passwordError.textContent = '';
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    } else {
        console.error('Form not found');
    }
});

//********************** content.js - Debugging Issue  ********************** //

document.addEventListener('DOMContentLoaded', function () {
    const element = document.querySelector('.some-element');

    if (element) {
        console.log(element.tagName); // Access tagName only if element exists
    } else {
        console.log('Element not found');
    }
});

//********************** showing date message  ********************** //



