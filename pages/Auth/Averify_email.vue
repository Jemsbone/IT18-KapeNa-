<template>
  <div class="page">
    <!-- Header section -->
    <header class="header">
      <div class="flex">
        <NuxtLink to="/" class="logo">
          Kape Na! <i class="fas fa-coffee"></i>
        </NuxtLink>
        <div class="admin-badge">
          <i class="fas fa-user-shield"></i>
          ADMIN
        </div>
      </div>
    </header>

    <main class="verify-main">
      <div class="verify-container">
        <div class="admin-indicator">ADMIN VERIFICATION</div>

        <div class="email-icon">
          <i class="fas fa-shield-alt"></i>
        </div>

        <h2>Verify Admin Email</h2>

        <div class="verify-info">
          <p>We've sent a 6-digit OTP code to</p>
          <p class="email">{{ userEmail }}</p>
          <p>Please enter the code below to verify your admin account.</p>
        </div>

        <!-- Display Error Messages -->
        <div v-if="errorMessage" class="error-message">
          <i class="fas fa-exclamation-circle"></i> {{ errorMessage }}
        </div>

        <div v-if="successMessage" class="success-message">
          <i class="fas fa-check-circle"></i> {{ successMessage }}
        </div>

        <!-- OTP Verification Form -->
        <form @submit.prevent="handleVerify">
          <div class="input-box">
            <label for="otp_code">Enter 6-Digit Code</label>
            <input
              v-model="otpCode"
              type="text"
              name="otp_code"
              id="otp_code"
              maxlength="6"
              pattern="[0-9]{6}"
              placeholder="000000"
              required
              autofocus
            />
          </div>
          <button type="submit" class="btn" :disabled="isSubmitting">
            {{ isSubmitting ? 'Verifying...' : 'Verify Admin Email' }}
          </button>
        </form>

        <!-- Resend OTP Section -->
        <div class="resend-section">
          <p>
            Didn't receive the code?
            <button
              type="button"
              @click="handleResend"
              :disabled="isResending"
            >
              {{ isResending ? 'Sending...' : 'Resend OTP' }}
            </button>
          </p>
        </div>

        <!-- Navigation Options -->
        <div class="navigation-options">
          <NuxtLink to="/">
            <i class="fas fa-home"></i>
            Back to Home
          </NuxtLink>
          <NuxtLink to="/Auth/Admin_login" class="nav-link">
            <i class="fas fa-sign-out-alt"></i>
            Switch Admin Account
          </NuxtLink>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useHead, useRoute, useRouter } from '#imports'

const route = useRoute()
const router = useRouter()
const { $axios } = useNuxtApp()

useHead({
  title: 'Admin Email Verification - Kape Na!',
  link: [
    {
      rel: 'icon',
      href:
        'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>☕</text></svg>',
    },
    {
      rel: 'stylesheet',
      href: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css',
    },
  ],
})

// State
const otpCode = ref('')
const userEmail = ref('')
const userId = ref(null)
const errorMessage = ref('')
const successMessage = ref('')
const isSubmitting = ref(false)
const isResending = ref(false)

// Get email and user_id from route query
onMounted(() => {
  // Get email from route query if available
  if (route.query.email) {
    userEmail.value = route.query.email
  } else {
    userEmail.value = 'your email address'
  }

  // Get user_id from route query (required for OTP verification)
  if (route.query.user_id) {
    userId.value = parseInt(route.query.user_id)
  }

  // Auto-format OTP input (numbers only)
  const otpInput = document.getElementById('otp_code')
  if (otpInput) {
    otpInput.addEventListener('input', function (e) {
      // Remove non-numeric characters
      this.value = this.value.replace(/[^0-9]/g, '')

      // Limit to 6 digits
      if (this.value.length > 6) {
        this.value = this.value.slice(0, 6)
      }
      otpCode.value = this.value
    })
  }

  // Check for success/error messages from route query
  if (route.query.success) {
    successMessage.value = route.query.success
    setTimeout(() => {
      successMessage.value = ''
    }, 5000)
  }

  if (route.query.error) {
    errorMessage.value = route.query.error
    setTimeout(() => {
      errorMessage.value = ''
    }, 5000)
  }

  // Redirect to registration if user_id is missing
  if (!userId.value && !route.query.user_id) {
    errorMessage.value = 'Invalid verification link. Please register again.'
    setTimeout(() => {
      router.push('/Auth/admin_register')
    }, 3000)
  }
})

// Handle OTP verification
const handleVerify = async () => {
  if (otpCode.value.length !== 6) {
    errorMessage.value = 'Please enter a valid 6-digit code'
    return
  }

  if (!userId.value) {
    errorMessage.value = 'User ID is missing. Please register again.'
    return
  }

  isSubmitting.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const response = await $axios.post('/admin/verify-otp', {
      otp_code: otpCode.value,
      user_id: userId.value,
    })

    // Store token if provided
    if (response.data.token) {
      // Token will be stored by axios plugin
    }

    successMessage.value =
      response.data.message || 'Email verified successfully!'
    setTimeout(() => {
      router.push('/Admin/adashboard')
    }, 2000)
  } catch (error) {
    console.error('Verification error:', error)
    errorMessage.value =
      error.response?.data?.message ||
      'Invalid verification code. Please try again.'
  } finally {
    isSubmitting.value = false
  }
}

// Handle resend OTP
const handleResend = async () => {
  if (!userId.value) {
    errorMessage.value = 'User ID is missing. Please register again.'
    return
  }

  isResending.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const response = await $axios.post('/admin/resend-otp', {
      user_id: userId.value,
    })

    successMessage.value =
      response.data.message || 'Verification code has been resent to your email!'
    otpCode.value = ''
  } catch (error) {
    console.error('Resend error:', error)
    errorMessage.value =
      error.response?.data?.message ||
      'Failed to resend code. Please try again.'
  } finally {
    isResending.value = false
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Anton&display=swap');

:root {
  --yellow: black;
  --red: #e74c3c;
  --white: #fff;
  --black: #222;
  --light-color: #777;
  --border: 0.2rem solid var(--black);
  --main-color: #d3ad7f;
  --admin-color: #3498db;
  --bg: #856731bd;
  --box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
}

.page {
  background: var(--bg);
  color: #fff;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  outline: none;
  border: none;
  text-decoration: none;
  font-family: 'Anton', sans-serif;
}

/* Header styles */
.header {
  position: sticky;
  top: 0;
  left: 0;
  right: 0;
  background-color: var(--black);
  z-index: 1000;
  padding: 1.5rem 2rem;
  box-shadow: var(--box-shadow);
}

.header .flex {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.header .logo {
  font-size: 2.5rem;
  color: var(--white);
  display: flex;
  align-items: center;
}

.header .logo i {
  color: var(--main-color);
  margin-left: 0.5rem;
}

.admin-badge {
  background: var(--admin-color);
  color: var(--white);
  padding: 0.5rem 1.5rem;
  border-radius: 0.5rem;
  font-size: 1.4rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: bold;
}

.verify-main {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.verify-container {
  background-color: var(--black);
  width: 100%;
  max-width: 45rem;
  padding: 3rem;
  border-radius: 1rem;
  box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.3);
  position: relative;
  border: 2px solid var(--admin-color);
}

.admin-indicator {
  position: absolute;
  top: -15px;
  left: 50%;
  transform: translateX(-50%);
  background: var(--admin-color);
  color: var(--white);
  padding: 0.5rem 2rem;
  border-radius: 2rem;
  font-size: 1.4rem;
  font-weight: bold;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.verify-container h2 {
  text-align: center;
  color: var(--white);
  font-size: 3.5rem;
  margin-bottom: 2.8rem;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.verify-info {
  text-align: center;
  color: var(--light-color);
  font-size: 1.6rem;
  margin-bottom: 3rem;
  line-height: 1.6;
}

.verify-info .email {
  color: var(--admin-color);
  font-weight: bold;
  font-size: 1.8rem;
}

.verify-container form {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
}

.verify-container form .input-box {
  position: relative;
}

.verify-container form .input-box input {
  width: 100%;
  padding: 2rem 1.5rem;
  font-size: 1.8rem;
  color: var(--white);
  background: transparent;
  border-bottom: 2px solid var(--admin-color);
  transition: border-color 0.3s;
  text-align: center;
  letter-spacing: 1rem;
}

.verify-container form .input-box input:focus {
  border-color: var(--white);
}

.verify-container form .input-box label {
  display: block;
  text-align: center;
  font-size: 1.6rem;
  color: var(--light-color);
  margin-bottom: 1rem;
}

.verify-container form .btn {
  width: 100%;
  padding: 1.5rem;
  background-color: var(--admin-color);
  color: var(--white);
  font-size: 2rem;
  font-weight: bold;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.3s;
  margin-top: 1.2rem;
}

.verify-container form .btn:hover:not(:disabled) {
  background-color: #2980b9;
  letter-spacing: 0.2rem;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
}

.verify-container form .btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.resend-section {
  text-align: center;
  margin-top: 2rem;
  font-size: 1.5rem;
  color: var(--light-color);
}

.resend-section p {
  line-height: 2;
}

.resend-section button {
  color: var(--admin-color);
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.5rem;
  text-decoration: underline;
  padding: 0;
  font-family: 'Anton', sans-serif;
  transition: color 0.3s;
}

.resend-section button:hover:not(:disabled) {
  color: var(--white);
}

.resend-section button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Navigation options styling */
.navigation-options {
  margin-top: 3rem;
  padding-top: 2rem;
  border-top: 1px solid var(--light-color);
  display: flex;
  justify-content: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.navigation-options a {
  color: var(--light-color);
  font-size: 1.5rem;
  padding: 1rem 2rem;
  border: 1px solid var(--light-color);
  border-radius: 0.5rem;
  transition: all 0.3s;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: transparent;
  cursor: pointer;
  font-family: 'Anton', sans-serif;
}

.navigation-options a:hover {
  color: var(--white);
  border-color: var(--white);
  transform: translateY(-2px);
}

/* Error message styling */
.error-message {
  color: var(--red);
  font-size: 1.6rem;
  margin-top: 0.5rem;
  text-align: center;
  padding: 1.2rem;
  background: rgba(231, 76, 60, 0.1);
  border-radius: 0.5rem;
  margin-bottom: 1.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.8rem;
}

/* Success message styling */
.success-message {
  color: #2ecc71;
  font-size: 1.6rem;
  margin-top: 0.5rem;
  text-align: center;
  padding: 1.2rem;
  background: rgba(46, 204, 113, 0.1);
  border-radius: 0.5rem;
  margin-bottom: 1.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.8rem;
}

.email-icon {
  text-align: center;
  margin-bottom: 2rem;
}

.email-icon i {
  font-size: 6rem;
  color: var(--admin-color);
}

/* Responsive styles */
@media (max-width: 768px) {
  .verify-container {
    padding: 3.5rem;
    max-width: 40rem;
  }

  .verify-container h2 {
    font-size: 3.2rem;
  }

  .navigation-options {
    flex-direction: column;
    gap: 1rem;
  }

  .navigation-options a {
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 450px) {
  .verify-container {
    padding: 2.5rem;
    max-width: 35rem;
  }

  .verify-container h2 {
    font-size: 3rem;
  }
}
</style>

