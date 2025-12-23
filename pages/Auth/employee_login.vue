<template>
  <div class="login-container">
    <!-- Background Decorations -->
    <div class="decoration-circles">
      <div class="circle circle-1"></div>
      <div class="circle circle-2"></div>
      <div class="circle circle-3"></div>
    </div>

    <!-- Login Card -->
    <div class="login-card">
      <!-- Logo Section -->
      <div class="logo-section">
        <div class="logo-icon">
          <i class="fas fa-coffee"></i>
        </div>
        <h1 class="brand-name">Kape Na!</h1>
        <p class="subtitle">Employee Portal</p>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="login-form">
        <h2 class="form-title">Employee Login</h2>
        <p class="form-subtitle">Sign in to access your dashboard</p>

        <!-- Error Message -->
        <div v-if="errorMessage" class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i>
          <span>{{ errorMessage }}</span>
        </div>

        <!-- Email Input -->
        <div class="form-group">
          <label for="email" class="form-label">
            <i class="fas fa-envelope"></i>
            Email Address
          </label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            class="form-input"
            placeholder="Enter your email"
            required
            autocomplete="email"
            :disabled="isLoading"
          />
        </div>

        <!-- Password Input -->
        <div class="form-group">
          <label for="password" class="form-label">
            <i class="fas fa-lock"></i>
            Password
          </label>
          <div class="password-wrapper">
            <input
              id="password"
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              class="form-input"
              placeholder="Enter your password"
              required
              autocomplete="current-password"
              :disabled="isLoading"
            />
            <button
              type="button"
              class="toggle-password"
              @click="togglePassword"
              :disabled="isLoading"
              aria-label="Toggle password visibility"
            >
              <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
          </div>
        </div>

        <!-- Remember Me Checkbox -->
        <div class="form-options">
          <label class="checkbox-label">
            <input
              v-model="form.remember"
              type="checkbox"
              class="checkbox-input"
              :disabled="isLoading"
            />
            <span class="checkbox-text">Remember me</span>
          </label>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          class="submit-btn"
          :disabled="isLoading"
          :class="{ loading: isLoading }"
        >
          <span v-if="!isLoading">
            <i class="fas fa-sign-in-alt"></i>
            Sign In
          </span>
          <span v-else>
            <i class="fas fa-spinner fa-spin"></i>
            Signing In...
          </span>
        </button>

        <!-- Additional Links -->
        <div class="form-footer">
          <p class="footer-text">
            Need help?
            <NuxtLink to="/contact" class="footer-link">Contact Support</NuxtLink>
          </p>
        </div>
      </form>

      <!-- Back to Home Link -->
      <div class="back-link">
        <NuxtLink to="/home" class="back-btn">
          <i class="fas fa-arrow-left"></i>
          Back to Home
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
// Imports
import { reactive, ref } from 'vue'
import { useHead } from '#imports'
import { useRouter } from 'vue-router'
import { useNuxtApp } from '#app'

// Page Head Configuration
useHead({
  title: 'Employee Login - Kape Na!',
  link: [
    {
      rel: 'icon',
      href: 'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>☕</text></svg>',
      type: 'image/svg+xml',
    },
    {
      rel: 'stylesheet',
      href: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css',
    },
  ],
})

// Router and Axios
const router = useRouter()
const { $axios } = useNuxtApp()

// Reactive Data
const form = reactive({
  email: '',
  password: '',
  remember: false,
})

const errorMessage = ref('')
const isLoading = ref(false)
const showPassword = ref(false)

// Methods
const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const validateForm = () => {
  if (!form.email || !form.password) {
    errorMessage.value = 'Please fill in all required fields.'
    return false
  }

  // Basic email validation
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(form.email)) {
    errorMessage.value = 'Please enter a valid email address.'
    return false
  }

  return true
}

const handleLogin = async () => {
  // Clear previous errors
  errorMessage.value = ''

  // Validate form
  if (!validateForm()) {
    return
  }

  isLoading.value = true

  try {
    // Make login request to the backend
    const response = await $axios.post('/employee/login', {
      email: form.email,
      password: form.password,
      remember: form.remember,
    })

    // Check if login was successful
    if (response.data.success && response.data.token) {
      // Store the employee token
      localStorage.setItem('employee_token', response.data.token)
      
      // Store employee data if provided
      if (response.data.employee) {
        localStorage.setItem('employee_data', JSON.stringify(response.data.employee))
      }

      // Redirect to employee dashboard
      window.location.href = '/Employee/edashboard'
    } else {
      errorMessage.value = response.data.message || 'Login failed. Please try again.'
    }
  } catch (error) {
    console.error('Login error:', error)
    
    // Handle different error scenarios
    if (error.response) {
      // The request was made and the server responded with a status code
      // that falls out of the range of 2xx
      if (error.response.status === 401) {
        errorMessage.value = 'Invalid email or password. Please try again.'
      } else if (error.response.status === 403) {
        errorMessage.value = 'Your account is inactive. Please contact administrator.'
      } else if (error.response.data?.message) {
        errorMessage.value = error.response.data.message
      } else {
        errorMessage.value = 'An error occurred during login. Please try again.'
      }
    } else if (error.request) {
      // The request was made but no response was received
      errorMessage.value = 'Unable to connect to server. Please check your connection.'
    } else {
      // Something happened in setting up the request
      errorMessage.value = 'An unexpected error occurred. Please try again.'
    }
  } finally {
    isLoading.value = false
  }
}

// Check if already logged in
onMounted(() => {
  const token = localStorage.getItem('employee_token')
  if (token) {
    // Optionally verify token with backend before redirecting
    window.location.href = '/Employee/edashboard'
  }
})
</script>

<style scoped>
/* Google Fonts Import */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

/* CSS Variables */
:root {
  --white: #fff;
  --black: #2c2c2c;
  --light-color: #666;
  --main-color: #d3ad7f;
  --bg: #c9b382;
  --box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
  --btn-color: #6366f1;
  --error-color: #ef4444;
  --success-color: #10b981;
}

/* Base Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  outline: none;
  border: none;
  text-decoration: none;
  font-family: 'Poppins', sans-serif;
}

html {
  font-size: 18.4px;
}

/* Login Container */
.login-container {
  min-height: 100vh;
  background: linear-gradient(135deg, var(--bg) 0%, #b89968 100%);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
  position: relative;
  overflow: hidden;
}

/* Decorative Circles */
.decoration-circles {
  position: absolute;
  width: 100%;
  height: 100%;
  overflow: hidden;
  z-index: 0;
}

.circle {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
}

.circle-1 {
  width: 300px;
  height: 300px;
  top: -100px;
  right: -100px;
}

.circle-2 {
  width: 200px;
  height: 200px;
  bottom: -50px;
  left: -50px;
}

.circle-3 {
  width: 150px;
  height: 150px;
  top: 50%;
  right: 10%;
}

/* Login Card */
.login-card {
  background: var(--white);
  border-radius: 1.5rem;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  width: 100%;
  max-width: 450px;
  padding: 3rem;
  position: relative;
  z-index: 1;
  animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Logo Section */
.logo-section {
  text-align: center;
  margin-bottom: 2.5rem;
}

.logo-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, var(--main-color), #8b6f47);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
  box-shadow: 0 8px 20px rgba(211, 173, 127, 0.4);
}

.logo-icon i {
  font-size: 2.5rem;
  color: var(--white);
}

.brand-name {
  font-size: 2rem;
  font-weight: 700;
  color: var(--black);
  margin-bottom: 0.3rem;
}

.subtitle {
  font-size: 1rem;
  color: var(--light-color);
  font-weight: 400;
}

/* Login Form */
.login-form {
  width: 100%;
}

.form-title {
  font-size: 1.8rem;
  font-weight: 600;
  color: var(--black);
  margin-bottom: 0.5rem;
  text-align: center;
}

.form-subtitle {
  font-size: 0.95rem;
  color: var(--light-color);
  text-align: center;
  margin-bottom: 2rem;
}

/* Form Group */
.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.95rem;
  font-weight: 500;
  color: var(--black);
  margin-bottom: 0.5rem;
}

.form-label i {
  color: var(--main-color);
  font-size: 1rem;
}

/* Form Input */
.form-input {
  width: 100%;
  padding: 0.9rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.6rem;
  font-size: 1rem;
  color: var(--black);
  background: #f9fafb;
  transition: all 0.3s ease;
}

.form-input:focus {
  border-color: var(--main-color);
  background: var(--white);
  box-shadow: 0 0 0 3px rgba(211, 173, 127, 0.1);
}

.form-input:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.form-input::placeholder {
  color: #9ca3af;
}

/* Password Wrapper */
.password-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.password-wrapper .form-input {
  padding-right: 3rem;
}

.toggle-password {
  position: absolute;
  right: 1rem;
  background: none;
  border: none;
  color: var(--light-color);
  cursor: pointer;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.3s ease;
}

.toggle-password:hover {
  color: var(--main-color);
}

.toggle-password:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.toggle-password i {
  font-size: 1rem;
}

/* Form Options */
.form-options {
  margin-bottom: 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  user-select: none;
}

.checkbox-input {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: var(--main-color);
}

.checkbox-text {
  font-size: 0.9rem;
  color: var(--light-color);
}

/* Submit Button */
.submit-btn {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, var(--btn-color), #4f46e5);
  color: var(--white);
  border-radius: 0.6rem;
  font-size: 1.05rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.submit-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #4f46e5, #4338ca);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
}

.submit-btn:active:not(:disabled) {
  transform: translateY(0);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.submit-btn.loading {
  background: linear-gradient(135deg, #818cf8, #6366f1);
}

/* Alert Messages */
.alert {
  padding: 1rem;
  border-radius: 0.6rem;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.8rem;
  font-size: 0.95rem;
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.alert-error {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

.alert-error i {
  color: var(--error-color);
  font-size: 1.1rem;
}

/* Form Footer */
.form-footer {
  margin-top: 1.5rem;
  text-align: center;
}

.footer-text {
  font-size: 0.9rem;
  color: var(--light-color);
}

.footer-link {
  color: var(--btn-color);
  font-weight: 500;
  transition: color 0.3s ease;
}

.footer-link:hover {
  color: var(--main-color);
  text-decoration: underline;
}

/* Back Link */
.back-link {
  margin-top: 1.5rem;
  text-align: center;
}

.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--light-color);
  font-size: 0.95rem;
  font-weight: 500;
  padding: 0.6rem 1.2rem;
  border-radius: 0.5rem;
  transition: all 0.3s ease;
}

.back-btn:hover {
  color: var(--main-color);
  background: rgba(211, 173, 127, 0.1);
}

.back-btn i {
  font-size: 0.9rem;
}

/* Responsive Design */
@media (max-width: 576px) {
  html {
    font-size: 16px;
  }

  .login-container {
    padding: 1rem;
  }

  .login-card {
    padding: 2rem 1.5rem;
  }

  .logo-icon {
    width: 70px;
    height: 70px;
  }

  .logo-icon i {
    font-size: 2rem;
  }

  .brand-name {
    font-size: 1.8rem;
  }

  .form-title {
    font-size: 1.5rem;
  }

  .circle-1,
  .circle-2,
  .circle-3 {
    display: none;
  }
}

@media (max-width: 400px) {
  .login-card {
    padding: 1.5rem 1rem;
  }
}
</style>


