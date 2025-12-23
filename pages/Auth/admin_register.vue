<template>
  <div class="page">
    <!-- Header section -->
    <header class="header">
      <div class="flex">
        <NuxtLink to="/" class="logo">
          Kape Na!
        </NuxtLink>
        <nav class="navbar">
          <NuxtLink to="/home">Home</NuxtLink>
        </nav>
      </div>
    </header>

    <main class="register-main">
      <div class="register-container">
        <span class="admin-badge">
          ADMIN
        </span>

        <h2>
          ADMIN REGISTRATION
        </h2>

        <div class="info-box">
          Create an admin account for the Kape Na! system. After registration, you will receive a verification code via email to verify your account.
        </div>

        <!-- Display Messages -->
        <div v-if="errorMessage" class="error-message">
          {{ errorMessage }}
        </div>
        <div v-if="successMessage" class="success-message">
          {{ successMessage }}
        </div>

        <!-- Admin Registration Form -->
        <form id="registerForm" @submit.prevent="onSubmit">
          <div class="input-box" :class="{ focused: !!form.admin_name }">
            <input
              v-model="form.admin_name"
              type="text"
              id="admin_name"
              required
            />
            <label for="admin_name">Admin Name</label>
          </div>

          <div class="input-box" :class="{ focused: !!form.admin_email }">
            <input
              v-model="form.admin_email"
              type="email"
              id="admin_email"
              required
            />
            <label for="admin_email">Admin Email</label>
          </div>

          <div class="form-row">
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
          </div>

          <div class="password-requirements">
            <strong>Password Requirements:</strong>
            <ul>
              <li>At least 8 characters long</li>
              <li>Must match confirmation password</li>
              <li>Use a strong, unique password</li>
            </ul>
          </div>

          <button type="submit" class="btn" :disabled="loading">
            {{ loading ? 'Registering...' : 'Register Admin Account' }}
          </button>

          <div class="login-link">
            <p>
              Already have an admin account?
              <NuxtLink to="/Auth/Admin_login">Login here</NuxtLink>
            </p>
          </div>
        </form>

        <div class="back-home">
          <NuxtLink to="/">
            Back to Home
          </NuxtLink>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useHead, useRouter, useNuxtApp } from '#imports'

const router = useRouter()
const { $axios } = useNuxtApp()

useHead({
  title: 'Admin Registration - Kape Na!',
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
  admin_name: '',
  admin_email: '',
  password: '',
  password_confirmation: '',
})

const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const onSubmit = async () => {
  if (
    !form.admin_name ||
    !form.admin_email ||
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

  if (form.password.length < 8) {
    errorMessage.value = 'Password must be at least 8 characters long.'
    successMessage.value = ''
    return
  }

  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const response = await $axios.post('/admin/register', { ...form })
    // Check if OTP verification is required
    if (response?.data?.requires_verification) {
      successMessage.value =
        'Registration successful! Please check your email for the verification code.'
      // Redirect to verification page with admin info
      await router.push({
        path: '/Auth/Averify_email',
        query: {
          user_id: response.data.user_id,
          email: response.data.email,
          success:
            'Registration successful! Please check your email for the verification code.',
        },
      })
    } else {
      // Fallback: if no verification required, go to admin dashboard
      successMessage.value = 'Registration successful. You are now logged in!'
      await router.push({
        path: '/Admin/Adashboard',
        query: { success: 'Registration successful. You are now logged in!' },
      })
    }
  } catch (err) {
    console.error('Admin registration error:', err)
    console.error('Error response:', err?.response?.data)
    console.error('Error status:', err?.response?.status)

    // Handle network errors
    if (
      err?.code === 'ECONNREFUSED' ||
      err?.message === 'Network Error' ||
      err?.response?.data?.networkError
    ) {
      errorMessage.value =
        'Cannot connect to API server. Please make sure the Laravel API server is running. Run: php artisan serve'
    }
    // Handle validation errors (422 status)
    else if (err?.response?.status === 422) {
      if (err?.response?.data?.errors) {
        const errors = err.response.data.errors
        const firstErrorKey = Object.keys(errors)[0]
        const firstError = errors[firstErrorKey]?.[0]
        errorMessage.value =
          firstError || 'Validation failed. Please check your input.'
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
      errorMessage.value =
        'Registration failed. Please check your input. Check console for details.'
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
  max-width: 45rem;
  padding: 3rem;
  border-radius: 1rem;
  box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.3);
  position: relative;
  border-top: 4px solid var(--admin-color);
}

.admin-badge {
  position: absolute;
  top: -15px;
  right: 20px;
  background-color: var(--admin-color);
  color: var(--white);
  padding: 0.5rem 1.5rem;
  border-radius: 2rem;
  font-size: 1.4rem;
  font-weight: bold;
  letter-spacing: 0.1rem;
}

.register-container h2 {
  text-align: center;
  color: var(--white);
  font-size: 3.5rem;
  margin-bottom: 2.8rem;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.info-box {
  background: rgba(52, 152, 219, 0.1);
  border-left: 4px solid var(--admin-color);
  padding: 1.5rem;
  margin-bottom: 2rem;
  border-radius: 0.5rem;
  font-size: 1.5rem;
  color: var(--light-color);
  line-height: 1.6;
}

.register-container form {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.8rem;
}

.register-container form .input-box {
  position: relative;
}

.register-container form .input-box input {
  width: 100%;
  padding: 2rem 1.5rem;
  font-size: 1.8rem;
  color: var(--white);
  background: transparent;
  border-bottom: 2px solid var(--admin-color);
  transition: border-color 0.3s;
}

.register-container form .input-box input:focus {
  border-color: var(--white);
}

.register-container form .input-box label {
  position: absolute;
  top: 50%;
  left: 1.5rem;
  transform: translateY(-50%);
  font-size: 1.8rem;
  color: var(--light-color);
  pointer-events: none;
  transition: all 0.3s;
}

.register-container form .input-box.focused label,
.register-container form .input-box input:focus ~ label {
  top: -5px;
  left: 0;
  font-size: 1.5rem;
  color: var(--admin-color);
}

.password-requirements {
  background: rgba(52, 152, 219, 0.1);
  border-left: 4px solid var(--admin-color);
  padding: 1.5rem;
  margin: 1rem 0;
  border-radius: 0.5rem;
  font-size: 1.5rem;
  color: var(--light-color);
  line-height: 1.6;
}


.password-requirements ul {
  margin-left: 2rem;
  margin-top: 0.8rem;
  list-style: disc;
}

.password-requirements li {
  margin: 0.5rem 0;
}

.register-container form .btn {
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

.register-container form .btn:hover:not(:disabled) {
  background-color: #2980b9;
  letter-spacing: 0.2rem;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
}

.register-container form .btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.register-container .login-link {
  text-align: center;
  margin-top: 2.8rem;
  font-size: 1.5rem;
  color: var(--light-color);
}

.register-container .login-link a {
  color: var(--admin-color);
  text-decoration: none;
  font-weight: bold;
}

.register-container .login-link a:hover {
  text-decoration: underline;
}

.register-container .back-home {
  text-align: center;
  margin-top: 2.5rem;
  margin-bottom: 2.5rem;
}

.register-container .back-home a {
  color: var(--white);
  font-size: 1.5rem;
  text-decoration: none;
  transition: color 0.3s;
}

.register-container .back-home a:hover {
  color: var(--main-color);
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
}

/* Responsive styles */
@media (max-width: 768px) {
  .register-container {
    padding: 3.5rem;
    max-width: 40rem;
  }

  .register-container h2 {
    font-size: 3.2rem;
  }

  .form-row {
    grid-template-columns: 1fr;
    gap: 0.8rem;
  }

  .header .navbar {
    display: none;
  }
}

@media (max-width: 450px) {
  .register-container {
    padding: 2.5rem;
    max-width: 35rem;
  }

  .register-container h2 {
    font-size: 3rem;
  }
}
</style>

