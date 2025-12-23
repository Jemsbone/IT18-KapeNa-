<template>
  <div class="page">
    <!-- Header section (same style as login) -->
    <header class="header">
      <div class="flex">
        <NuxtLink to="/" class="logo">
          Kape Na! <i class="fas fa-coffee"></i>
        </NuxtLink>
        <nav class="navbar">
          <NuxtLink to="/home">Home</NuxtLink>
        </nav>
      </div>
    </header>

    <main class="register-main">
      <div class="register-container">
        <h2>REGISTER</h2>

        <!-- Display Messages -->
        <div v-if="errorMessage" class="error-message">
          {{ errorMessage }}
        </div>
        <div v-if="successMessage" class="success-message">
          {{ successMessage }}
        </div>

        <!-- Registration Form -->
        <form id="registerForm" @submit.prevent="onSubmit">
          <div class="input-box" :class="{ focused: !!form.name }">
            <input
              v-model="form.name"
              type="text"
              id="name"
              required
            />
            <label for="name">Full Name</label>
          </div>

          <div class="input-box" :class="{ focused: !!form.email }">
            <input
              v-model="form.email"
              type="email"
              id="email"
              required
            />
            <label for="email">Email</label>
          </div>

          <div class="input-box" :class="{ focused: !!form.phone }">
            <input
              v-model="form.phone"
              type="tel"
              id="phone"
              required
            />
            <label for="phone">Phone Number</label>
          </div>

          <div class="input-box" :class="{ focused: !!form.password }">
            <input
              v-model="form.password"
              type="password"
              id="password"
              required
            />
            <label for="password">Password</label>
          </div>

          <div
            class="input-box"
            :class="{ focused: !!form.password_confirmation }"
          >
            <input
              v-model="form.password_confirmation"
              type="password"
              id="password_confirmation"
              required
            />
            <label for="password_confirmation">Confirm Password</label>
          </div>

          <div class="input-box" :class="{ focused: !!form.address }">
            <input
              v-model="form.address"
              type="text"
              id="address"
              required
            />
            <label for="address">Full Address</label>
          </div>

          <button type="submit" class="btn" :disabled="loading">
            {{ loading ? 'Registering...' : 'Register' }}
          </button>

          <div class="login-link">
            <p>
              Already have an account?
              <NuxtLink to="/Auth/login">Login</NuxtLink>
            </p>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useHead } from '#imports'
import { useAuth } from '@/composables/useAuth'

const { register } = useAuth()

useHead({
  title: 'Register - Kape Na!',
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

const form = reactive({
  name: '',
  email: '',
  phone: '',
  address: '',
  password: '',
  password_confirmation: '',
})

const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const onSubmit = async () => {
  if (
    !form.name ||
    !form.email ||
    !form.phone ||
    !form.address ||
    !form.password ||
    !form.password_confirmation
  ) {
    errorMessage.value = 'Please fill in all fields.'
    successMessage.value = ''
    return
  }

  if (form.password !== form.password_confirmation) {
    errorMessage.value = 'Passwords do not match.'
    successMessage.value = ''
    return
  }

  const phoneRegex = /^\d{10,15}$/
  if (!phoneRegex.test(form.phone)) {
    errorMessage.value =
      'Please enter a valid phone number (10–15 digits).'
    successMessage.value = ''
    return
  }

  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const response = await register({ ...form })
    // Check if OTP verification is required
    if (response?.requires_verification) {
      successMessage.value = 'Registration successful! Please check your email for the verification code.'
      // Redirect to verification page with user info
      await navigateTo({
        path: '/Auth/Cverify_email',
        query: {
          user_id: response.user_id,
          email: response.email,
          success: 'Registration successful! Please check your email for the verification code.'
        }
      })
    } else {
      // Fallback: if no verification required, go to dashboard
      successMessage.value = 'Registration successful. You are now logged in!'
      await navigateTo({
        path: '/Customer/Cdashboard',
        query: { success: 'Registration successful. You are now logged in!' }
      })
    }
  } catch (err) {
    console.error('Registration error:', err)
    console.error('Error response:', err?.response?.data)
    console.error('Error status:', err?.response?.status)
    console.error('Error code:', err?.code)
    
    // Handle network errors
    if (err?.code === 'ECONNREFUSED' || err?.message === 'Network Error' || err?.response?.data?.networkError) {
      errorMessage.value = 'Cannot connect to API server. Please make sure the Laravel API server is running. Run: php artisan serve'
    }
    // Handle validation errors (422 status)
    else if (err?.response?.status === 422) {
      if (err?.response?.data?.errors) {
        const errors = err.response.data.errors
        const firstErrorKey = Object.keys(errors)[0]
        const firstError = errors[firstErrorKey]?.[0]
        errorMessage.value = firstError || 'Validation failed. Please check your input.'
      } else if (err?.response?.data?.message) {
        errorMessage.value = err.response.data.message
      } else {
        errorMessage.value = 'Validation failed. Please check your input.'
      }
    } else if (err?.response?.data?.message) {
      errorMessage.value = err.response.data.message
    } else if (err?.message) {
      errorMessage.value = err.message
    } else {
      errorMessage.value = 'Registration failed. Please check your input. Check console for details.'
    }
  } finally {
    loading.value = false
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
  --main-color: rgb(211, 173, 127);
  --bg: #b88c3cbd;
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

/* Header styles (same as login) */
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

.header .navbar {
  display: flex;
}

.header .navbar a {
  margin: 0 1rem;
  font-size: 2rem;
  color: var(--white);
}

.header .navbar a:hover {
  color: var(--main-color);
}

.register-main {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.register-container {
  background-color: var(--black);
  width: 100%;
  max-width: 35rem;
  padding: 2.4rem;
  border-radius: 1rem;
  box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.3);
}

.register-container h2 {
  text-align: center;
  color: var(--white);
  font-size: 2.6rem;
  margin-bottom: 2.2rem;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.register-container form {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
}

.register-container form .input-box {
  position: relative;
}

.register-container form .input-box input {
  width: 100%;
  padding: 1.5rem 1rem;
  font-size: 1.4rem;
  color: var(--white);
  background: transparent;
  border-bottom: 2px solid var(--main-color);
  transition: border-color 0.3s;
}

.register-container form .input-box input:focus {
  border-color: var(--white);
}

.register-container form .input-box label {
  position: absolute;
  top: 50%;
  left: 1rem;
  transform: translateY(-50%);
  font-size: 1.4rem;
  color: var(--light-color);
  pointer-events: none;
  transition: all 0.3s;
}

.register-container form .input-box.focused label,
.register-container form .input-box input:focus ~ label {
  top: -5px;
  left: 0;
  font-size: 1.2rem;
  color: var(--main-color);
}

.register-container form .btn {
  width: 100%;
  padding: 1.2rem;
  background-color: var(--main-color);
  color: var(--black);
  font-size: 1.6rem;
  font-weight: bold;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 1rem;
}

.register-container form .btn:hover:not(:disabled) {
  background-color: var(--white);
  letter-spacing: 0.2rem;
}

.register-container form .btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.register-container .login-link {
  text-align: center;
  margin-top: 2.2rem;
  font-size: 1.2rem;
  color: var(--light-color);
}

.login-link a {
  color: var(--main-color);
  text-decoration: none;
  font-weight: bold;
}

.login-link a:hover {
  text-decoration: underline;
}

.error-message {
  color: var(--red);
  font-size: 1.3rem;
  margin-top: 0.5rem;
  text-align: center;
  padding: 1rem;
  background: rgba(231, 76, 60, 0.1);
  border-radius: 0.5rem;
  margin-bottom: 1rem;
}

.success-message {
  color: #2ecc71;
  font-size: 1.3rem;
  text-align: center;
  padding: 1rem;
  background: rgba(46, 204, 113, 0.1);
  border-radius: 0.5rem;
  margin-bottom: 1rem;
}

@media (max-width: 768px) {
  .register-container {
    padding: 3rem;
  }

  .register-container h2 {
    font-size: 3rem;
  }

  .header .navbar {
    display: none;
  }
}

@media (max-width: 450px) {
  .register-container {
    padding: 2rem;
  }

  .register-container h2 {
    font-size: 2.5rem;
  }
}
</style>
