"use strict";

import { trainBookingData } from "./data.js";
import { loginData } from "./loginData.js";

document.addEventListener('DOMContentLoaded', function () {
    const trainSearchForm = document.getElementById('train-search-form');
    const paymentForm = document.getElementById('payment-form');
    const paymentMethod = document.getElementById('payment-method');
    const loginForm = document.getElementById('login-form');

    // Handle Train Search
    if (trainSearchForm) {
        trainSearchForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const departureLocation = document.getElementById('departure-location').value.trim();
            const destinationLocation = document.getElementById('destination-location').value.trim();
            const travelDate = document.getElementById('travel-date').value;

            if (!departureLocation || !destinationLocation || !travelDate) {
                displayError('Please fill in all fields.');
                return;
            }

            const trainResults = simulateTrainSearch(departureLocation, destinationLocation, travelDate);

            if (trainResults.length === 0) {
                displayError('No trains found for the selected criteria.');
            } else {
                displayResults(trainResults);
            }
        });
    }

    // Handle Payment
    if (paymentForm) {
        paymentForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const paymentMethodValue = document.getElementById('payment-method').value;
            const cardNumber = document.getElementById('card-number').value;
            const expiryDate = document.getElementById('expiry-date').value;
            const cvv = document.getElementById('cvv').value;
            const walletId = document.getElementById('wallet-id').value;

            if (paymentMethodValue === 'credit-card') {
                if (!validateCardDetails(cardNumber, expiryDate, cvv)) {
                    displayError('Please enter valid card details.');
                    return;
                }
            } else if (paymentMethodValue === 'digital-wallet' && !walletId) {
                displayError('Please enter your wallet ID.');
                return;
            }

            displayConfirmation('Payment successful! A receipt has been sent to your email.');
        });
    }

    // Handle Login
    if (loginForm) {
        loginForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();

            const user = loginData.find(user => user.username === username && user.password === password);

            if (user) {
                displayConfirmation('Login successful! Redirecting...');
                setTimeout(() => {
                    window.location.href = "dashboard.html"; // Replace with the dashboard or landing page URL
                }, 1000);
            } else {
                displayError('Invalid username or password. Please try again.');
            }
        });
    }

    // Handle Payment Method Change
    if (paymentMethod) {
        paymentMethod.addEventListener('change', function () {
            const cardDetails = document.getElementById('card-details');
            const walletDetails = document.getElementById('wallet-details');
            const paymentMethodValue = this.value;

            if (paymentMethodValue === 'credit-card') {
                cardDetails.style.display = 'block';
                walletDetails.style.display = 'none';
            } else if (paymentMethodValue === 'digital-wallet') {
                cardDetails.style.display = 'none';
                walletDetails.style.display = 'block';
            } else {
                cardDetails.style.display = 'none';
                walletDetails.style.display = 'none';
            }
        });
    }
});

// Simulate Train Search
function simulateTrainSearch(departure, destination, date) {
    return trainBookingData.availableTrains.filter(train =>
        train.from_station === departure &&
        train.to_station === destination &&
        train.travel_date === date
    );
}

// Display Train Results
function displayResults(trains) {
    const resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = '<h2>Available Trains:</h2>';
    resultsDiv.style.display = 'grid';
    resultsDiv.style.gridTemplateColumns = 'repeat(auto-fit, minmax(300px, 1fr))';
    resultsDiv.style.gap = '16px';

    trains.forEach(train => {
        const trainDiv = document.createElement('div');
        trainDiv.classList.add('train-result');

        trainDiv.innerHTML = `
            <div style="border: 1px solid #ccc; border-radius: 8px; padding: 16px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); background-color: #f9f9f9;">
                <h3 style="margin: 0 0 8px; color: #333;">Train Details</h3>
                <p><strong>Departure Time:</strong> ${train.departure_time}</p>
                <p><strong>Arrival Time:</strong> ${train.arrival_time}</p>
                <p><strong>Duration:</strong> ${train.duration}</p>
                <p><strong>Price:</strong> <span style="color: #28a745; font-weight: bold;">${train.price}</span></p>
            </div>
        `;

        resultsDiv.appendChild(trainDiv);
    });

    resultsDiv.style.display = 'block';
    document.getElementById('error-message').style.display = 'none';
}

// Display Error Message
function displayError(message) {
    const errorDiv = document.getElementById('error-message');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    document.getElementById('results').style.display = 'none';
}

// Display Confirmation Message
function displayConfirmation(message) {
    const confirmationDiv = document.getElementById('confirmation-message');
    confirmationDiv.textContent = message;
    confirmationDiv.style.display = 'block';
    document.getElementById('error-message').style.display = 'none';
}

// Validate Card Details
function validateCardDetails(cardNumber, expiryDate, cvv) {
    const cardNumberPattern = /^\d{16}$/;
    const expiryDatePattern = /^\d{4}-\d{2}$/; // Adjusted for `yyyy-mm`
    const cvvPattern = /^\d{3}$/;

    return cardNumberPattern.test(cardNumber) &&
        expiryDatePattern.test(expiryDate) &&
        cvvPattern.test(cvv);
}
