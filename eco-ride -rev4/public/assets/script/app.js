const navBtn = document.querySelector(".nav__btn")
const navMenu = document.querySelector(".nav-menu")
const nav = document.querySelector(".nav")




let navOpen = false;

navBtn.addEventListener("click",function (){
    if (navOpen){
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

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('login-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            console.log('Form is being submitted');
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

//********************** Handling Search and Displaying Results  ********************** //

document.querySelector('.search-btn').addEventListener('click', function (event) {
    event.preventDefault(); // Prevent default form behavior
    const depart = document.getElementById('depart').value;
    const destination = document.getElementById('destination').value;
    const date = document.getElementById('date').value; // Expecting format dd/mm/yyyy
    const passenger = document.getElementById('passenger').value;
    const carType = document.getElementById('carType').value;
    const time = document.getElementById('time').value;

    // Validate inputs
    if (!depart || !destination || !date || !time) {
        alert("Veuillez remplir tous les champs obligatoires !");
        return;
    }

    // Log the form data for debugging
    console.log({ depart, destination, date, passenger, carType, time });

    // Fetch and display results
    fetchResults(depart, destination, date);
});

function fetchResults(depart, destination, date) {
    const availableRides = [
        {
            driver: 'John Doe',
            photo: '../public/assets/images/image/driver1.jpg',
            rating: 4.5,
            remainingSeats: 3,
            price: '30€',
            date: '2025-01-20',
            departureTime: '09:00',
            arrivalTime: '11:00',
            ecoFriendly: true,
        },
        {
            driver: 'Jane Smith',
            photo: '../public/assets/images/image/driver2.jpg',
            rating: 4.7,
            remainingSeats: 0, // No available seats, should not be shown
            price: '25€',
            date: '2025-01-20',
            departureTime: '10:00',
            arrivalTime: '12:00',
            ecoFriendly: false,
        },
        {
            driver: 'Alice Johnson',
            photo: '../public/assets/images/image/driver3.jpg',
            rating: 4.5,
            remainingSeats: 2, // Available seats
            price: '20€',
            date: '2025-01-18',
            departureTime: '09:00',
            arrivalTime: '11:00',
            ecoFriendly: true, // Electric car
        },
        {
            driver: 'Michael Brown',
            photo: '../public/assets/images/image/driver4.jpg',
            rating: 4.2,
            remainingSeats: 1, // Available seat
            price: '18€',
            date: '2025-01-19',
            departureTime: '12:00',
            arrivalTime: '14:00',
            ecoFriendly: false, // Non-electric car
        },
        {
            driver: 'Sarah Williams',
            photo: '../public/assets/images/image/driver5.jpg',
            rating: 4.8,
            remainingSeats: 4, // Available seats
            price: '22€',
            date: '2025-01-19',
            departureTime: '15:00',
            arrivalTime: '17:00',
            ecoFriendly: true, // Electric car
        },
        {
            driver: 'David Lee',
            photo: '../public/assets/images/image/driver6.jpg',
            rating: 4.6,
            remainingSeats: 0, // No available seats, should not be shown
            price: '30€',
            date: '2025-01-20',
            departureTime: '18:00',
            arrivalTime: '20:00',
            ecoFriendly: true, // Electric car
        },
        {
            driver: 'Emma Taylor',
            photo: '../public/assets/images/image/driver7.jpg',
            rating: 4.3,
            remainingSeats: 2, // Available seats
            price: '16€',
            date: '2025-01-21',
            departureTime: '07:00',
            arrivalTime: '09:00',
            ecoFriendly: false, // Non-electric car
        }


    ];

    // Filter available rides based on user input
    const filteredRides = availableRides.filter(ride => {
        return (
            ride.remainingSeats > 0 &&
            ride.date === date // Match dd/mm/yyyy format
        );
    });

    // Display results
    const resultsContainer = document.getElementById('results-container');
    resultsContainer.innerHTML = '';

    if (filteredRides.length === 0) {
        resultsContainer.innerHTML = '<p>No rides available. Please adjust your search.</p>';
    } else {
        filteredRides.forEach(ride => {
            const rideElement = document.createElement('div');
            rideElement.classList.add('ride');

            rideElement.innerHTML = `
                <div class="ride-info">
                    <img src="${ride.photo}" alt="${ride.driver}" class="driver-photo">
                    <div class="ride-details">
                        <p>${ride.driver}</p>
                        <p>Rating: ${ride.rating} ⭐</p>
                        <p>Remaining Seats: ${ride.remainingSeats}</p>
                        <p>Price: ${ride.price}</p>
                        <p>Departure: ${ride.date} at ${ride.departureTime}</p>
                        <p>Arrival: ${ride.date} at ${ride.arrivalTime}</p>
                        <p>Eco-friendly: ${ride.ecoFriendly ? 'Yes' : 'No'}</p>
                        <button class="details-btn" data-id="${ride.driver}">Book Now</button>
                    </div>
                </div>
            `;

            resultsContainer.appendChild(rideElement);
        });
    }

    // Show the results section
    document.querySelector('.search-results').style.display = 'block';
}

// Show driver details modal
function showDriverDetailsModal(ride) {
    const modal = document.getElementById('driver-details');
    const modalContent = modal.querySelector('.modal-content');

    modalContent.innerHTML = `
        <span id="close-btn" class="close">&times;</span>
        <div class="driver-details-modal">
            <img src="${ride.photo}" alt="${ride.driver}" class="driver-photo">
            <p><strong>Driver:</strong> ${ride.driver}</p>
            <p><strong>Rating:</strong> ${ride.rating} ⭐</p>
            <p><strong>Price:</strong> ${ride.price}</p>
            <p><strong>Departure:</strong> ${ride.date} at ${ride.departureTime}</p>
            <p><strong>Arrival:</strong> ${ride.date} at ${ride.arrivalTime}</p>
            <p><strong>Eco-friendly:</strong> ${ride.ecoFriendly ? 'Yes' : 'No'}</p>
            <button class="book-now-btn">Book Now</button>
        </div>
    `;

    modal.style.display = 'block';

    // Close modal when clicking the close button
    const closeBtn = modal.querySelector('#close-btn');
    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // Close modal when clicking outside the modal content
    window.onclick = (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };

    // Function to handle booking when "Book Now" is clicked
    function handleBooking() {
        saveBookingData(ride);
        alert('Booking confirmed!');
        modal.style.display = 'none'; // Close the modal after booking
    }

    // Handle "Book Now" button click (only once)
    const bookNowBtn = modal.querySelector('.book-now-btn');
    bookNowBtn.removeEventListener('click', handleBooking); // Remove existing listener
    bookNowBtn.addEventListener('click', handleBooking);
}

// Save booking data to the backend
function saveBookingData(ride) {
    const userId = 1; // Replace with the logged-in user ID if applicable
    const status = 'confirmed'; // Booking status
    const creditsUsed = ride.ecoFriendly ? 5 : 1; // Eco-friendly cars use 5 credits, others 1

    // Simulate sending booking data to the backend
    fetch('/save-booking', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: userId,
            carpool_id: ride.id || 0, // Assuming each ride has a unique ID
            status: status,
            credits_used: creditsUsed,
        }),
    })
        .then(response => response.json())
        .then(data => {
            console.log('Booking saved successfully', data);
        })
        .catch(error => {
            console.error('Error saving booking', error);
        });
}

// Event delegation to handle clicks on "Book Now" buttons in dynamically generated content
document.getElementById('results-container').addEventListener('click', function (event) {
    if (event.target && event.target.classList.contains('details-btn')) {
        const rideId = event.target.getAttribute('data-id');
        const ride = availableRides.find(r => r.driver === rideId);
        if (ride) {
            showDriverDetailsModal(ride);
        }
    }
});
