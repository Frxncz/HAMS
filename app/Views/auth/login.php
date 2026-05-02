<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/HAMS/public/css/login.css" />
  <title>Login</title>
</head>
<body>

  <!-- Back Button -->
  <a href="/HAMS/public/index.php" class="back-button">
    <img src="/HAMS/public/assets/icons/patientsDashboard/logout.svg" alt="back icon">
    Back
  </a>
  
  <div class="login-container">

    <!-- Form Section - Centered -->
    <div class="form-section">
      <div class="form-container">
        <!-- Logo -->
        <div class="logo">
          <img src="/HAMS/public/assets/images/landing-page_assests/Logo-removebg-preview.png" alt="Logo">
        </div>

        <!-- Welcome Text -->
        <div class="welcome-text">Welcome Back</div>
        <div class="subtitle">Sign in to continue to your dashboard</div>

        <form action="/HAMS/public/actions/auth/login.php" method="POST">
          
          <!-- Email Input -->
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <div class="input-wrapper">
              <img class="input-icon" src="/HAMS/public/assets/icons/adminDashboard/email.svg" alt="email icon">
              <input 
                type="email" 
                class="form-input" 
                name="email" 
                placeholder="Enter your email" 
                required>
            </div>
          </div>

          <!-- Password Input -->
          <div class="form-group">
            <label class="form-label">
              Password
              <a href="#" class="forgot-link">Forgot password?</a>
            </label>
            <div class="input-wrapper">
              <img class="input-icon" src="/HAMS/public/assets/icons/adminDashboard/lock.svg" alt="lock icon">
              <input 
                type="password" 
                class="form-input" 
                name="password" 
                placeholder="Enter your password" 
                required>
            </div>
          </div>

          <!-- Sign In Button -->
          <button type="submit" class="btn-signin">Sign In</button>
        </form>

        <!-- Divider -->
        <div class="divider">Don't have an account?</div>

        <!-- Create Account Button -->
        <button class="btn-create">
          <a href="/HAMS/public/signup.php">Create New Account</a>
        </button>
      </div>
    </div>
  </div>

</body>
</html>



