<template>
  <div class="page-shell">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo">
        <span>Kape Na!</span>
        <i class="fas fa-coffee"></i>
      </div>
      <nav class="nav-menu">
        <NuxtLink to="/Admin/adashboard" class="nav-item">
          <i class="fas fa-home"></i>
          <span>Dashboard</span>
        </NuxtLink>
        <NuxtLink to="/Admin/aproduct" class="nav-item">
          <i class="fas fa-box"></i>
          <span>Products</span>
        </NuxtLink>
        <NuxtLink to="/Admin/aorders" class="nav-item">
          <i class="fas fa-shopping-cart"></i>
          <span>Orders</span>
        </NuxtLink>
        <NuxtLink to="/Admin/aadmins" class="nav-item">
          <i class="fas fa-user-shield"></i>
          <span>Admins</span>
        </NuxtLink>
        <NuxtLink to="/Admin/auser" class="nav-item">
          <i class="fas fa-users"></i>
          <span>Users</span>
        </NuxtLink>
        <NuxtLink to="/Admin/aemployee" class="nav-item active">
          <i class="fas fa-user-tie"></i>
          <span>Employees</span>
        </NuxtLink>
        <NuxtLink to="/Admin/amessage" class="nav-item">
          <i class="fas fa-envelope"></i>
          <span>Messages</span>
        </NuxtLink>
      </nav>
      <div class="logout-item">
        <button type="button" class="nav-item" @click="handleLogout">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Header -->
      <div class="header">
        <h1>Register Employee</h1>
        <div class="admin-info">
          <i class="fas fa-user-shield"></i>
          <span>Admin</span>
        </div>
      </div>

      <!-- Success/Error Messages -->
      <div v-if="successMessage" class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ successMessage }}
        <div v-if="defaultPassword" class="password-info">
          <strong>Default Login Credentials:</strong><br>
          Email: {{ createdEmail }}<br>
          Password: <code>{{ defaultPassword }}</code><br>
          <small>(Please provide these credentials to the employee)</small>
        </div>
      </div>
      <div v-if="errors.length" class="alert alert-error">
            <ul>
              <li v-for="error in errors" :key="error">{{ error }}</li>
            </ul>
      </div>

      <!-- Registration Form Section -->
      <div class="registration-container">
        <h2 class="form-title">Employee Management</h2>

        <form @submit.prevent="handleSubmit">
            <div class="form-group">
              <input
                v-model="form.name"
                type="text"
                name="name"
                class="form-input"
                placeholder="enter your username"
                required
              />
            </div>

            <div class="form-group">
              <label for="birthday" style="display: block; margin-bottom: 0.5rem; color: var(--black); font-weight: 500;">Birthday</label>
              <input
                id="birthday"
                v-model="form.birthday"
                type="date"
                name="birthday"
                class="form-input"
                :max="maxDate"
                required
              />
            </div>

            <div class="form-group">
              <select
                v-model="form.sex"
                name="sex"
                class="form-input"
                required
              >
                <option value="">select your gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
              </select>
            </div>

            <div class="form-group">
              <input
                v-model="form.email"
                type="email"
                name="email"
                class="form-input"
                placeholder="enter your email"
                required
              />
            </div>

            <div class="form-group">
              <input
                v-model="form.phone"
                type="tel"
                name="phone"
                class="form-input"
                placeholder="enter your phone number"
                pattern="[0-9]{10,15}"
                required
              />
            </div>

            <div class="form-group">
              <textarea
                v-model="form.address"
                name="address"
                class="form-input"
                placeholder="enter your address"
                rows="3"
                required
              />
            </div>


            <button type="submit" class="submit-btn" :disabled="isLoading">
              {{ isLoading ? 'Registering...' : 'Register Now' }}
            </button>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
// Imports
import { reactive, ref, computed } from 'vue'
import { useHead } from '#imports'
import { useRouter } from 'vue-router'
import { useNuxtApp } from '#app'

// Page Head Configuration
useHead({
  title: 'Register Employee - Kape Na!',
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
  name: '',
  birthday: '',
  sex: '',
  email: '',
  phone: '',
  address: '',
})

const errors = ref([])
const successMessage = ref('')
const isLoading = ref(false)
const defaultPassword = ref('')
const createdEmail = ref('')

// Computed: Maximum date (today) for birthday input
const maxDate = computed(() => {
  const today = new Date()
  return today.toISOString().split('T')[0]
})

// Methods
const validate = () => {
  const newErrors = []
  
  if (form.birthday) {
    const birthday = new Date(form.birthday)
    const today = new Date()
    const age = today.getFullYear() - birthday.getFullYear()
    const monthDiff = today.getMonth() - birthday.getMonth()
    
    // Adjust age if birthday hasn't occurred this year
    const actualAge = monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthday.getDate()) 
      ? age - 1 
      : age
    
    if (actualAge < 18) {
      newErrors.push('Employee must be at least 18 years old.')
    }
    if (actualAge > 100) {
      newErrors.push('Please enter a valid birthday.')
    }
  }
  
  return newErrors
}

const handleLogout = async () => {
  try {
    await $axios.post(
      '/admin/logout',
      {},
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('admin_token')}`,
        },
      }
    )
  } catch (error) {
    console.error('Logout error:', error)
  } finally {
    localStorage.removeItem('admin_token')
    router.push('/Auth/admin_login')
  }
}

const handleSubmit = async () => {
  errors.value = validate()
  if (errors.value.length) return
  
  isLoading.value = true
  errors.value = []
  successMessage.value = ''
  
  try {
    const response = await $axios.post('/admin/employees', {
      name: form.name,
      birthday: form.birthday,
      sex: form.sex,
      email: form.email || null,
      phone: form.phone || null,
      address: form.address || null,
    })
    
    if (response.data.success) {
      successMessage.value = 'Employee registered successfully!'
      
      // Store default password and email to display to admin
      defaultPassword.value = response.data.default_password || ''
      createdEmail.value = form.email
      
      // Reset form fields except the success message
      const savedEmail = form.email
      form.name = ''
      form.birthday = ''
      form.sex = ''
      form.email = ''
      form.phone = ''
      form.address = ''
      
      // Redirect to employee list after 8 seconds (give admin time to note password)
      setTimeout(() => {
        router.push('/Admin/aemployee')
      }, 8000)
    }
  } catch (error) {
    console.error('Error creating employee:', error)
    if (error.response?.data?.errors) {
      // Handle validation errors
      const validationErrors = error.response.data.errors
      errors.value = Object.values(validationErrors).flat()
    } else {
      errors.value = [error.response?.data?.message || 'Failed to create employee. Please try again.']
    }
  } finally {
    isLoading.value = false
  }
}
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
  --admin-color: #d3ad7f;
  --bg: #c9b382;
  --sidebar-bg: #2c2c2c;
  --box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
  --btn-color: #6366f1;
}

/* Base Styles */
html {
  font-size: 18.4px;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  outline: none;
  border: none;
  text-decoration: none;
  font-family: 'Poppins', sans-serif;
  font-size: 18.4px;
}

/* Page Shell */
.page-shell {
  background: var(--bg);
  color: var(--black);
  min-height: 100vh;
  display: flex;
}

/* Sidebar Styles */
.sidebar {
  width: 180px;
  background: var(--black);
  min-height: 100vh;
  position: fixed;
  left: 0;
  top: 0;
  padding: 0;
  z-index: 1000;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
}

/* Logo */
.logo {
  padding: 1rem 1rem;
  font-size: 1.4rem;
  color: var(--white);
  display: flex;
  align-items: center;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.logo i {
  color: var(--main-color);
  margin-left: 0.3rem;
  font-size: 1.2rem;
}

/* Navigation Menu */
.nav-menu {
  padding: 1rem 0;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 0.8rem 1.2rem;
  color: var(--white);
  font-size: 1.1rem;
  transition: all 0.3s;
  cursor: pointer;
}

.nav-item:hover,
.nav-item.active {
  background: var(--main-color);
  color: var(--black);
  border-left: 3px solid #8b6f47;
}

.nav-item i {
  margin-right: 0.6rem;
  font-size: 1.2rem;
  width: 18px;
}

.nav-item span {
  margin-left: 0.4rem;
}

/* Logout Item */
.logout-item {
  position: absolute;
  bottom: 1rem;
  width: 100%;
}

.logout-item .nav-item {
  width: 100%;
  background: none;
}

/* Main Content Styles */
.main-content {
  margin-left: 180px;
  flex: 1;
  padding: 2rem;
  display: flex;
  flex-direction: column;
}

/* Header */
.header {
  background: var(--white);
  padding: 1.5rem 2rem;
  border-radius: 8px;
  box-shadow: var(--box-shadow);
  margin-bottom: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header h1 {
  font-size: 2rem;
  color: var(--black);
}

.admin-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.admin-info i {
  font-size: 2.5rem;
  color: var(--main-color);
}

.admin-info span {
  font-size: 1.1rem;
  color: var(--light-color);
}

/* Registration Form Styles */
.registration-container {
  background: var(--white);
  border-radius: 0.8rem;
  box-shadow: var(--box-shadow);
  padding: 3rem;
  width: 100%;
  max-width: 500px;
  margin: 0 auto;
}

/* Form Title */
.form-title {
  font-size: 1.8rem;
  font-weight: 600;
  color: var(--black);
  margin-bottom: 2rem;
  text-align: center;
  letter-spacing: 1px;
}

/* Form Group */
.form-group {
  margin-bottom: 1.5rem;
}

/* Form Input */
.form-input {
  width: 100%;
  padding: 0.9rem 1rem;
  border: 2px solid #e0e0e0;
  border-radius: 0.5rem;
  font-size: 1rem;
  color: var(--black);
  background: #f5f5f5;
  transition: all 0.3s;
}

.form-input:focus {
  border-color: var(--main-color);
  background: var(--white);
}

.form-input::placeholder {
  color: var(--light-color);
}

textarea.form-input {
  resize: vertical;
  min-height: 80px;
  font-family: 'Poppins', sans-serif;
}

select.form-input {
  cursor: pointer;
}

/* Submit Button */
.submit-btn {
  width: 100%;
  padding: 1rem;
  background: var(--btn-color);
  color: var(--black);
  border-radius: 0.5rem;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  margin-top: 1rem;
}

.submit-btn:hover {
  background: #4f46e5;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

/* Success/Error Messages */
.alert {
  padding: 1rem 1.5rem;
  border-radius: 6px;
  margin-bottom: 1.5rem;
  font-size: 1rem;
}

.alert-success {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.alert-error {
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.alert-error ul {
  margin: 0;
  padding-left: 1.2rem;
}

.password-info {
  margin-top: 1rem;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 6px;
  color: var(--black);
  line-height: 1.8;
}

.password-info code {
  background: #f0f0f0;
  padding: 0.2rem 0.6rem;
  border-radius: 4px;
  font-family: 'Courier New', monospace;
  font-weight: 600;
  color: #c7254e;
  font-size: 1.1rem;
}

.password-info small {
  color: var(--light-color);
  font-style: italic;
}

/* Responsive Design */
@media (max-width: 768px) {
  .sidebar {
    width: 60px;
  }

  .sidebar .logo span,
  .sidebar .nav-item span {
    display: none;
  }

  .main-content {
    margin-left: 60px;
  }

  .header {
    flex-direction: column;
    gap: 1rem;
  }

  .registration-container {
    padding: 2rem;
    margin: 1rem;
  }
}
</style>
