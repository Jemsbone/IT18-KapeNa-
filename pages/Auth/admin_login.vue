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

    <main class="login-main">
      <div class="login-container">

        <h2>
          ADMIN LOGIN
        </h2>

        <div class="info-box">
          This is the admin login portal. Enter your credentials to access the admin panel.
        </div>

        <!-- Display Messages -->
        <div v-if="errorMessage" class="error-message">
          {{ errorMessage }}
        </div>
        <div v-if="successMessage" class="success-message">
          {{ successMessage }}
        </div>

        <!-- Admin Login Form -->
        <form id="loginForm" @submit.prevent="onSubmit">
          <div class="input-box" :class="{ focused: !!email }">
            <input
              v-model="email"
              type="email"
              id="email"
              required
            />
            <label for="email">Admin Email</label>
          </div>

          <div class="input-box" :class="{ focused: !!password }">
            <input
              v-model="password"
              type="password"
              id="password"
              required
            />
            <label for="password">Password</label>
          </div>

          <div class="remember-forgot">
            <label>
              <input v-model="remember" type="checkbox" />
              Remember me
            </label>
          </div>

          <button type="submit" class="btn" :disabled="loading">
            {{ loading ? 'Logging in...' : 'Login' }}
          </button>
        </form>

        <div class="register-link">
          <p>
            Don't have an admin account?
            <NuxtLink to="/Auth/admin_register">Register here</NuxtLink>
          </p>
        </div>

        <div class="back-home">
          <NuxtLink to="/home">
            Back to Home
          </NuxtLink>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useHead, useRouter, useNuxtApp } from '#imports'

const email = ref('')
const password = ref('')
const remember = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const loading = ref(false)

const router = useRouter()
const { $axios } = useNuxtApp()

useHead({
  title: 'Admin Login - Kape Na!',
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

const onSubmit = async () => {
  if (!email.value || !password.value) {
    errorMessage.value = 'Please fill in all fields'
    successMessage.value = ''
    return
  }

  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const response = await $axios.post('/admin/login', {
      email: email.value,
      password: password.value,
      remember: remember.value,
    })

    // Store admin token if provided
    if (response.data.token) {
      localStorage.setItem('admin_token', response.data.token)
      console.log('Admin token stored successfully')
    }

    successMessage.value = 'Logged in successfully.'
    await router.push({
      path: '/Admin/adashboard',
      query: { success: 'Logged in successfully!' }
    })
  } catch (err) {
    console.error('Admin login error:', err)
    errorMessage.value = err?.response?.data?.message || 'Login failed. Please check your credentials.'
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

.login-main {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.login-container {
  background-color: var(--black);
  width: 100%;
  max-width: 45rem;
  padding: 3rem;
  border-radius: 1rem;
  box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.3);
  position: relative;
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
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.login-container h2 {
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

.login-container form {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
}

.login-container form .input-box {
  position: relative;
}

.login-container form .input-box input {
  width: 100%;
  padding: 2rem 1.5rem;
  font-size: 1.8rem;
  color: var(--white);
  background: transparent;
  border-bottom: 2px solid var(--main-color);
  transition: border-color 0.3s;
}

.login-container form .input-box input:focus {
  border-color: var(--white);
}

.login-container form .input-box label {
  position: absolute;
  top: 50%;
  left: 1.5rem;
  transform: translateY(-50%);
  font-size: 1.8rem;
  color: var(--light-color);
  pointer-events: none;
  transition: all 0.3s;
}

.login-container form .input-box.focused label,
.login-container form .input-box input:focus ~ label {
  top: -5px;
  left: 0;
  font-size: 1.5rem;
  color: var(--main-color);
}

.login-container form .remember-forgot {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 1.5rem;
  margin: 0.8rem 0 1rem;
}

.login-container form .remember-forgot label {
  display: flex;
  align-items: center;
  color: var(--light-color);
  cursor: pointer;
}

.login-container form .remember-forgot input {
  margin-right: 0.5rem;
  width: 1.6rem;
  height: 1.6rem;
}

.login-container form .btn {
  width: 100%;
  padding: 1.5rem;
  background-color: var(--main-color);
  color: var(--black);
  font-size: 2rem;
  font-weight: bold;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 1.2rem;
}

.login-container form .btn:hover:not(:disabled) {
  background-color: var(--white);
  letter-spacing: 0.2rem;
}

.login-container form .btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.login-container .register-link {
  text-align: center;
  margin-top: 2.8rem;
  font-size: 1.5rem;
  color: var(--light-color);
}

.login-container .register-link a {
  color: var(--main-color);
  text-decoration: none;
  font-weight: bold;
}

.login-container .register-link a:hover {
  text-decoration: underline;
}

.login-container .back-home {
  text-align: center;
  margin-top: 2.5rem;
  margin-bottom: 2.5rem;
}

.login-container .back-home a {
  color: var(--white);
  font-size: 1.5rem;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  transition: color 0.3s;
}

.login-container .back-home a:hover {
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
  .login-container {
    padding: 3.5rem;
    max-width: 40rem;
  }

  .login-container h2 {
    font-size: 3.2rem;
  }

  .header .navbar {
    display: none;
  }
}

@media (max-width: 450px) {
  .login-container {
    padding: 2.5rem;
    max-width: 35rem;
  }

  .login-container h2 {
    font-size: 3rem;
  }

  .login-container form .remember-forgot {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
}
</style>

