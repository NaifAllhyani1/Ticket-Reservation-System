document.getElementById('train-search-form').addEventListener('submit', function(event) {
    event.preventDefault();
  
    const departureLocation = document.getElementById('departure-location').value.trim();
    const destinationLocation = document.getElementById('destination-location').value.trim();
    const travelDate = document.getElementById('travel-date').value;
  
    if (!departureLocation || !destinationLocation || !travelDate) {
      displayError('Please fill in all fields.');
      return;
    }
  
    // Simulate an API call for train results (replace this with actual API logic)
    const trainResults = simulateTrainSearch(departureLocation, destinationLocation, travelDate);
  
    if (trainResults.length === 0) {
      displayError('No trains found for the selected criteria.');
    } else {
      displayResults(trainResults);
    }
  });
  
  function simulateTrainSearch(departure, destination, date) {
    // For simulation purposes, we'll return some mock train data
    return [
      {
        departureTime: '08:00 AM',
        arrivalTime: '10:30 AM',
        duration: '2h 30m',
        price: '$30'
      },
      {
        departureTime: '02:00 PM',
        arrivalTime: '04:30 PM',
        duration: '2h 30m',
        price: '$35'
      }
    ];
  }
  
  function displayResults(trains) {
    const resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = '<h2>Available Trains:</h2>';
    
    trains.forEach(train => {
      const trainDiv = document.createElement('div');
      trainDiv.classList.add('train-result');
      
      trainDiv.innerHTML = `
        <p><strong>Departure Time:</strong> ${train.departureTime}</p>
        <p><strong>Arrival Time:</strong> ${train.arrivalTime}</p>
        <p><strong>Duration:</strong> ${train.duration}</p>
        <p><strong>Price:</strong> ${train.price}</p>
      `;
      
      resultsDiv.appendChild(trainDiv);
    });
    
    resultsDiv.style.display = 'block';
    document.getElementById('error-message').style.display = 'none';
  }
  
  function displayError(message) {
    const errorDiv = document.getElementById('error-message');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    document.getElementById('results').style.display = 'none';
  }
  
  document.getElementById('payment-form').addEventListener('submit', function(event) {
    event.preventDefault();
  
    const paymentMethod = document.getElementById('payment-method').value;
    const cardNumber = document.getElementById('card-number').value;
    const expiryDate = document.getElementById('expiry-date').value;
    const cvv = document.getElementById('cvv').value;
    const walletId = document.getElementById('wallet-id').value;
  
    // Validate payment details
    if (paymentMethod === 'credit-card') {
      if (!validateCardDetails(cardNumber, expiryDate, cvv)) {
        displayError('Please enter valid card details.');
        return;
      }
    } else if (paymentMethod === 'digital-wallet' && !walletId) {
      displayError('Please enter your wallet ID.');
      return;
    }
  
    // Simulate successful payment transaction
    displayConfirmation('Payment successful! A receipt has been sent to your email.');
  });
  
  function validateCardDetails(cardNumber, expiryDate, cvv) {
    // Simple validation for card details (to be replaced with more thorough validation)
    const cardNumberPattern = /^\d{16}$/;
    const cvvPattern = /^\d{3}$/;
    const expiryDatePattern = /^\d{2}\/\d{2}$/;
  
    return cardNumberPattern.test(cardNumber) && expiryDatePattern.test(expiryDate) && cvvPattern.test(cvv);
  }
  
  function displayError(message) {
    const errorDiv = document.getElementById('error-message');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    document.getElementById('confirmation-message').style.display = 'none';
  }
  
  function displayConfirmation(message) {
    const confirmationDiv = document.getElementById('confirmation-message');
    confirmationDiv.textContent = message;
    confirmationDiv.style.display = 'block';
    document.getElementById('error-message').style.display = 'none';
  }
  
  // Cancel Payment
  function cancelPayment() {
    if (confirm('Are you sure you want to cancel the payment?')) {
      window.location.reload(); // Refresh to reset the form
    }
  }
  
  // Show relevant payment details based on the selected payment method
  document.getElementById('payment-method').addEventListener('change', function() {
    const paymentMethod = this.value;
    const cardDetails = document.getElementById('card-details');
    const walletDetails = document.getElementById('wallet-details');
  
    if (paymentMethod === 'credit-card') {
      cardDetails.style.display = 'block';
      walletDetails.style.display = 'none';
    } else if (paymentMethod === 'digital-wallet') {
      cardDetails.style.display = 'none';
      walletDetails.style.display = 'block';
    } else {
      cardDetails.style.display = 'none';
      walletDetails.style.display = 'none';
    }
  });
  