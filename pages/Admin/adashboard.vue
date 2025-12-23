<template>
  <div class="page-shell">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo">
        <span>Kape Na!</span>
        <i class="fas fa-coffee"></i>
      </div>
      <nav class="nav-menu">
        <NuxtLink to="/Admin/adashboard" class="nav-item active">
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
        <button
          type="button"
          class="nav-item"
          @click="handleLogout"
        >
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Header -->
      <div class="header">
        <h1>Dashboard</h1>
        <div class="admin-info">
          <i class="fas fa-user-shield"></i>
          <span>{{ adminName }}</span>
        </div>
      </div>

      <!-- Success/Error Messages -->
      <div v-if="successMessage" class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ successMessage }}
      </div>

        <!-- First Stats Grid -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-info">
              <h3>₱{{ formatCurrency(totalPendingAmount) }}</h3>
              <p>total pendings ({{ totalPending }} orders)</p>
            </div>
            <i class="fas fa-clock stat-icon"></i>
          </div>

          <div class="stat-card">
            <div class="stat-info">
              <h3>₱{{ formatCurrency(totalCompletedAmount) }}</h3>
              <p>total completes ({{ totalCompleted }} orders)</p>
            </div>
            <i class="fas fa-check-circle stat-icon"></i>
          </div>

          <div class="stat-card">
            <div class="stat-info">
              <h3>₱{{ formatCurrency(totalOrdersAmount) }}</h3>
              <p>total orders ({{ totalOrders }} orders)</p>
            </div>
            <i class="fas fa-shopping-cart stat-icon"></i>
          </div>

          <NuxtLink
            to="/Admin/aproduct"
            style="text-decoration: none; color: inherit"
          >
            <div class="stat-card">
              <div class="stat-info">
                <h3>{{ totalProducts }}</h3>
                <p>products added</p>
              </div>
              <i class="fas fa-th-large stat-icon"></i>
            </div>
          </NuxtLink>
        </div>

        <!-- Second Stats Grid -->
        <div class="stats-grid">
          <NuxtLink
            to="/Admin/auser"
            style="text-decoration: none; color: inherit"
          >
            <div class="stat-card">
              <div class="stat-info">
                <h3>{{ totalUsers }}</h3>
                <p>users accounts</p>
              </div>
              <i class="fas fa-user-circle stat-icon"></i>
            </div>
          </NuxtLink>

          <NuxtLink
            to="/Admin/aadmins"
            style="text-decoration: none; color: inherit"
          >
            <div class="stat-card">
              <div class="stat-info">
                <h3>{{ totalAdmins }}</h3>
                <p>admin accounts</p>
              </div>
              <i class="fas fa-user-shield stat-icon"></i>
            </div>
          </NuxtLink>

          <NuxtLink
            to="/Admin/aemployee"
            style="text-decoration: none; color: inherit"
          >
            <div class="stat-card">
              <div class="stat-info">
                <h3>{{ totalEmployees }}</h3>
                <p>employee accounts</p>
              </div>
              <i class="fas fa-user-tie stat-icon"></i>
            </div>
          </NuxtLink>

          <NuxtLink
            to="/Admin/amessage"
            style="text-decoration: none; color: inherit"
          >
            <div class="stat-card">
              <div class="stat-info">
                <h3>{{ newMessages }}</h3>
                <p>new messages</p>
              </div>
              <i class="fas fa-envelope stat-icon"></i>
            </div>
          </NuxtLink>
        </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useNuxtApp } from '#app'

const router = useRouter()
const route = useRoute()
const { $axios } = useNuxtApp()

const adminName = ref('admin')
const successMessage = ref('')

// Stats data
const totalPendingAmount = ref(0)
const totalPending = ref(0)
const totalCompletedAmount = ref(0)
const totalCompleted = ref(0)
const totalOrdersAmount = ref(0)
const totalOrders = ref(0)
const totalProducts = ref(0)
const totalUsers = ref(0)
const totalAdmins = ref(0)
const totalEmployees = ref(0)
const newMessages = ref(0)

// Lifecycle Hooks
onMounted(() => {
  // Check for success message from route query
  if (route.query.success) {
    successMessage.value = route.query.success
    setTimeout(() => {
      successMessage.value = ''
    }, 5000)
  }

  // Fetch admin data
  fetchAdminData()
  fetchDashboardStats()
})

// Methods
const fetchAdminData = async () => {
  try {
    const response = await $axios.get('/admin/user')
    if (response.data.success && response.data.admin_name) {
      adminName.value = response.data.admin_name
    }
  } catch (error) {
    console.error('Failed to fetch admin data:', error)
    adminName.value = 'Admin'
  }
}

const fetchDashboardStats = async () => {
  try {
    const response = await $axios.get('/admin/dashboard/stats')
    if (response.data.success && response.data.data) {
      const stats = response.data.data
      totalPendingAmount.value = stats.totalPendingAmount || 0
      totalPending.value = stats.totalPending || 0
      totalCompletedAmount.value = stats.totalCompletedAmount || 0
      totalCompleted.value = stats.totalCompleted || 0
      totalOrdersAmount.value = stats.totalOrdersAmount || 0
      totalOrders.value = stats.totalOrders || 0
      totalProducts.value = stats.totalProducts || 0
      totalUsers.value = stats.totalUsers || 0
      totalAdmins.value = stats.totalAdmins || 0
      totalEmployees.value = stats.totalEmployees || 0
      newMessages.value = stats.newMessages || 0
    }
  } catch (error) {
    console.error('Failed to fetch dashboard stats:', error)
  }
}

const formatCurrency = (amount) => {
  return parseFloat(amount || 0)
    .toFixed(2)
    .replace(/\d(?=(\d{3})+\.)/g, '$&,')
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
</script>

<style>
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

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: var(--white);
  padding: 2rem;
  border-radius: 0.6rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: var(--box-shadow);
  transition: transform 0.3s, box-shadow 0.3s;
  border: var(--border);
}

.stat-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}

.stat-card .stat-info h3 {
  font-size: 2rem;
  font-weight: 700;
  color: var(--black);
  margin-bottom: 0.5rem;
}

.stat-card .stat-info p {
  font-size: 1rem;
  color: var(--light-color);
  font-weight: 400;
  text-transform: capitalize;
}

.stat-card .stat-icon {
  font-size: 3rem;
  color: var(--light-color);
  opacity: 0.3;
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

  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>

