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
        <NuxtLink to="/Admin/aadmins" class="nav-item active">
          <i class="fas fa-user-shield"></i>
          <span>Admins</span>
        </NuxtLink>
        <NuxtLink to="/Admin/auser" class="nav-item">
          <i class="fas fa-users"></i>
          <span>Users</span>
        </NuxtLink>
        <NuxtLink to="/Admin/aemployee" class="nav-item">
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
        <h1>Admin Management</h1>
        <div class="admin-info">
          <i class="fas fa-user-shield"></i>
          <span>Admin</span>
        </div>
      </div>

      <!-- Success/Error Messages -->
      <div v-if="successMessage" class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ successMessage }}
      </div>
      <div v-if="errorMessage" class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> {{ errorMessage }}
      </div>
      <!-- Search Bar -->
      <div class="search-container">
          <input
            v-model="searchTerm"
            type="text"
            class="search-input"
            placeholder="Search by name, email, or ID"
          />
          <button class="search-btn" @click="loadAdmins">search</button>
        </div>

      <!-- Admin Details Section -->
      <h2 class="section-title">Admin Details</h2>
      <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Email Status</th>
                <th>Created At</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoading">
                <td colspan="6" class="empty-row">Loading admins...</td>
              </tr>
              <template v-else>
                <tr v-for="admin in filteredAdmins" :key="admin.admin_id">
                  <td>{{ admin.admin_id }}</td>
                  <td>{{ admin.admin_name }}</td>
                  <td>{{ admin.admin_email }}</td>
                  <td>
                    <span
                      v-if="admin.email_verified_at"
                      class="badge badge-verified"
                    >
                      <i class="fas fa-check-circle"></i> Verified
                    </span>
                    <span v-else class="badge badge-unverified">
                      <i class="fas fa-times-circle"></i> Unverified
                    </span>
                  </td>
                  <td>{{ admin.created_at || 'N/A' }}</td>
                  <td>
                    <button class="icon-btn" type="button" @click="deleteAdmin(admin.admin_id)">
                      <i class="fas fa-trash delete-icon"></i>
                    </button>
                  </td>
                </tr>
                <tr v-if="!filteredAdmins.length">
                  <td colspan="6" class="empty-row">No admins found</td>
                </tr>
              </template>
            </tbody>
          </table>
      </div>
    </main>
  </div>
</template>

<script setup>
// Imports
import { computed, ref, onMounted } from 'vue'
import { useHead } from '#imports'
import { useRouter } from 'vue-router'
import { useNuxtApp } from '#app'

// Page Head Configuration
useHead({
  title: 'Admin Management - Kape Na!',
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
const searchTerm = ref('')
const successMessage = ref('')
const errorMessage = ref('')
const isLoading = ref(false)
const admins = ref([])

// Computed Properties
const filteredAdmins = computed(() => {
  const term = searchTerm.value.trim().toLowerCase()
  if (!term) return admins.value
  return admins.value.filter(
    (admin) =>
      admin.admin_name?.toLowerCase().includes(term) ||
      admin.admin_email?.toLowerCase().includes(term) ||
      admin.admin_id?.toString().includes(term)
  )
})

// Methods
// Load admins from API
const loadAdmins = async () => {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await $axios.get('/admin/admins')
    if (response.data.success && response.data.data) {
      admins.value = response.data.data
      console.log('Admins loaded:', admins.value.length)
    } else {
      admins.value = []
    }
  } catch (error) {
    console.error('Error loading admins:', error)
    errorMessage.value = 'Failed to load admins. Please try again.'
    admins.value = []
  } finally {
    isLoading.value = false
  }
}

// Delete admin
const deleteAdmin = async (id) => {
  if (!confirm('Are you sure you want to delete this admin?')) {
    return
  }

  try {
    const response = await $axios.delete(`/admin/admins/${id}`)
    
    if (response.data.success) {
      successMessage.value = 'Admin deleted successfully!'
      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
      // Remove from local array
      admins.value = admins.value.filter((a) => a.admin_id !== id)
    }
  } catch (error) {
    console.error('Error deleting admin:', error)
    errorMessage.value = error.response?.data?.message || 'Failed to delete admin.'
    setTimeout(() => {
      errorMessage.value = ''
    }, 5000)
  }
}

// Load admins on component mount
onMounted(() => {
  loadAdmins()
})

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
  --delete-btn: #e74c3c;
  --add-btn: #00bcd4;
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
  display: flex;
  flex-direction: column;
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
  flex: 1;
  overflow-y: auto;
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

/* Main Content */
.main-content {
  margin-left: 180px;
  flex: 1;
  padding: 2rem;
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

/* Section Title */
.section-title {
  font-size: 1.8rem;
  font-weight: 600;
  color: var(--black);
  margin-bottom: 1rem;
}

/* Search Bar */
.search-container {
  display: flex;
  justify-content: flex-end;
  gap: 0.8rem;
  margin-bottom: 1.5rem;
}

.search-input {
  padding: 0.6rem 1rem;
  border: 2px solid var(--main-color);
  border-radius: 0.5rem;
  font-size: 1rem;
  width: 250px;
}

.search-btn {
  padding: 0.6rem 1.5rem;
  background: var(--main-color);
  color: var(--white);
  border-radius: 0.5rem;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s;
}

.search-btn:hover {
  background: #b8956a;
}

/* Table Styles */
.table-container {
  background: var(--white);
  border-radius: 0.5rem;
  overflow-x: auto;
  box-shadow: var(--box-shadow);
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: var(--add-btn);
  color: var(--white);
}

th {
  padding: 1rem;
  text-align: center;
  font-weight: 600;
  font-size: 0.95rem;
}

tbody tr {
  border-bottom: 1px solid #e0e0e0;
}

tbody tr:hover {
  background: #f5f5f5;
}

td {
  padding: 1rem;
  font-size: 0.9rem;
  color: var(--black);
  text-align: center;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 150px;
}

td:nth-child(3) {
  max-width: 250px;
}

/* Badge Styles */
.badge {
  padding: 0.3rem 0.8rem;
  border-radius: 1rem;
  font-size: 0.8rem;
  font-weight: 500;
}

.badge-verified {
  background: #d4edda;
  color: #155724;
}

.badge-unverified {
  background: #f8d7da;
  color: #721c24;
}

/* Delete Icon */
.delete-icon {
  color: var(--black);
  font-size: 1.2rem;
  cursor: pointer;
  transition: color 0.3s;
}

.delete-icon:hover {
  color: var(--delete-btn);
}

.icon-btn {
  background: none;
  border: none;
  cursor: pointer;
}

/* Empty Row */
.empty-row {
  text-align: center;
  padding: 2rem;
  color: var(--light-color);
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

  .search-container {
    flex-direction: column;
  }

  .search-input {
    width: 100%;
  }

  table {
    font-size: 0.8rem;
  }
}
</style>
