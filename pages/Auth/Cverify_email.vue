<template>
  <div class="page">
    <!-- Header section -->
    <header class="header">
      <div class="flex">
        <NuxtLink to="/home" class="logo">
          Kape Na! <i class="fas fa-coffee"></i>
        </NuxtLink>
      </div>
    </header>

    <main class="verify-main">
      <div class="verify-container">
        <div class="email-icon">
          <i class="fas fa-envelope"></i>
        </div>

        <h2>Verify Your Email</h2>

        <div class="verify-info">
          <p>We've sent a 6-digit OTP code to</p>
          <p class="email">{{ userEmail }}</p>
          <p>Please enter the code below to verify your account.</p>
        </div>

        <!-- Display Error Messages -->
        <div v-if="errorMessage" class="error-message">
          {{ errorMessage }}
        </div>

        <div v-if="successMessage" class="success-message">
          {{ successMessage }}
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
            {{ isSubmitting ? 'Verifying...' : 'Verify Email' }}
          </button>
        </form>

        <!-- Resend OTP Section -->
        <div class="resend-section">
          <p>
            Didn't receive the code?
            <button type="button" @click="handleResend" :disabled="isResending">
              {{ isResending ? 'Sending...' : 'Resend OTP' }}
            </button>
          </p>
        </div>

        <!-- Navigation Options -->
        <div class="navigation-section">
          <div class="nav-divider"></div>
          <div class="nav-links">
            <NuxtLink to="/Auth/login" class="nav-link">
              <i class="fas fa-sign-in-alt"></i>
              Log in another account
            </NuxtLink>
            <NuxtLink to="/home" class="nav-link">
              <i class="fas fa-home"></i>
              Back to Home
            </NuxtLink>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useHead, useRoute, useRouter } from '#imports'
import { useAuth } from '@/composables/useAuth'

const route = useRoute()
const router = useRouter()
const { $axios } = useNuxtApp()

// Load Font Awesome
useHead({
  title: 'Verify Email - Kape Na!',
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
const { fetchUser } = useAuth()

// Get email and user_id from route query or user data
onMounted(() => {
  // Get email and user_id from route query if available
  if (route.query.email) {
    userEmail.value = route.query.email
  } else {
    // Try to get from auth user
    const { user } = useAuth()
    if (user.value?.email) {
      userEmail.value = user.value.email
    } else {
      userEmail.value = 'your email address'
    }
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
      router.push('/Auth/register')
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
    const response = await $axios.post('/verify-otp', {
      otp_code: otpCode.value,
      user_id: userId.value
    })

    // Store token if provided
    if (response.data.token) {
      // Token will be stored by axios plugin
      // Fetch user data to update auth state
      await fetchUser()
    }

    successMessage.value = response.data.message || 'Email verified successfully!'
    setTimeout(() => {
      router.push('/Customer/Cdashboard')
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
    const response = await $axios.post('/resend-otp', {
      user_id: userId.value
    })

    successMessage.value = response.data.message || 'Verification code has been resent to your email!'
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

<style>
@import url('https://fonts.googleapis.com/css2?family=Anton&display=swap');

:root {
  --yellow: black;
  --red: #e74c3c;
  --white: #fff;
  --black: #222;
  --light-color: #777;
  --border: 0.2rem solid var(--black);
  --main-color: #d3ad7f;
  --bg: #856731bd;
  --box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
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

body {
  background: var(--bg);
  color: var(--white);
}

.page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
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

.flex {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.logo {
  font-size: 2rem;
  color: var(--white);
}

.logo i {
  color: var(--main-color);
  margin-right: 0.5rem;
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
  max-width: 50rem;
  padding: 4rem;
  border-radius: 1rem;
  box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.3);
  position: relative;
  border: var(--border);
}

.verify-container h2 {
  text-align: center;
  color: var(--white);
  font-size: 3rem;
  margin-bottom: 1rem;
  text-transform: uppercase;
  text-underline-offset: 1rem;
}

.verify-info {
  text-align: center;
  color: var(--light-color);
  font-size: 1.6rem;
  margin-bottom: 3rem;
  line-height: 2;
}

.verify-info .email {
  color: var(--main-color);
  font-weight: bold;
  font-size: 1.8rem;
}

.verify-container form {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.verify-container form .input-box {
  position: relative;
}

.verify-container form .input-box input {
  width: 100%;
  padding: 1.5rem 1rem;
  font-size: 2.5rem;
  color: var(--white);
  background: transparent;
  border-bottom: 2px solid var(--main-color);
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
  padding: 1rem 3rem;
  background-color: var(--yellow);
  color: var(--white);
  font-size: 1.6rem;
  font-weight: bold;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: 0.2s linear;
  margin-top: 1rem;
  text-transform: capitalize;
}

.verify-container form .btn:hover:not(:disabled) {
  letter-spacing: 0.2rem;
}

.verify-container form .btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.resend-section {
  text-align: center;
  margin-top: 2rem;
  font-size: 1.6rem;
  color: var(--light-color);
}

.resend-section p {
  line-height: 2;
}

.resend-section button {
  color: var(--main-color);
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.6rem;
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

/* Navigation section styling */
.navigation-section {
  margin-top: 3rem;
  padding-top: 2rem;
}

.nav-divider {
  height: 1px;
  background: var(--light-color);
  opacity: 0.3;
  margin-bottom: 2rem;
}

.nav-links {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  align-items: center;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  color: var(--light-color);
  font-size: 1.5rem;
  text-decoration: none;
  transition: all 0.3s;
  padding: 0.8rem 1.5rem;
  border-radius: 0.5rem;
  border: 1px solid transparent;
}

.nav-link:hover {
  color: var(--main-color);
  border-color: var(--main-color);
  transform: translateX(0.5rem);
}

.nav-link i {
  font-size: 1.4rem;
}

/* Error message styling */
.error-message {
  color: var(--red);
  font-size: 1.6rem;
  margin-top: 0.5rem;
  text-align: center;
  padding: 1rem;
  background: rgba(231, 76, 60, 0.1);
  border-radius: 0.5rem;
  margin-bottom: 1rem;
  border: 1px solid var(--red);
}

/* Success message styling */
.success-message {
  color: #2ecc71;
  font-size: 1.6rem;
  margin-top: 0.5rem;
  text-align: center;
  padding: 1rem;
  background: rgba(46, 204, 113, 0.1);
  border-radius: 0.5rem;
  margin-bottom: 1rem;
  border: 1px solid #2ecc71;
}

.email-icon {
  text-align: center;
  margin-bottom: 2rem;
}

.email-icon i {
  font-size: 6rem;
  color: var(--main-color);
}

/* Responsive styles */
@media (max-width: 991px) {
  html {
    font-size: 55%;
  }
}

@media (max-width: 768px) {
  .verify-container {
    padding: 3rem;
  }

  .verify-container h2 {
    font-size: 2.5rem;
  }
}

@media (max-width: 450px) {
  html {
    font-size: 50%;
  }

  .verify-container {
    padding: 2rem;
  }

  .verify-container h2 {
    font-size: 2rem;
  }

  .nav-links {
    gap: 1rem;
  }

  .nav-link {
    font-size: 1.4rem;
    padding: 0.6rem 1.2rem;
  }
}
</style>

