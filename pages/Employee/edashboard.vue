<template>
  <div class="page-shell">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo">
        <span>Kape Na!</span>
        <i class="fas fa-coffee"></i>
      </div>
      <nav class="nav-menu">
        <NuxtLink to="/Employee/edashboard" class="nav-item active">
          <i class="fas fa-home"></i>
          <span>Dashboard</span>
        </NuxtLink>
        <NuxtLink to="/Employee/eorders" class="nav-item">
          <i class="fas fa-shopping-cart"></i>
          <span>Orders</span>
        </NuxtLink>
        <NuxtLink to="/Employee/emessages" class="nav-item">
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
        <h1>Employee Dashboard</h1>
        <div class="employee-info">
          <i class="fas fa-user-tie"></i>
          <span>{{ employeeName }}</span>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-info">
            <h3>{{ stats.pendingOrders }}</h3>
            <p>pending orders</p>
          </div>
          <i class="fas fa-clock stat-icon"></i>
        </div>

        <div class="stat-card">
          <div class="stat-info">
            <h3>{{ stats.completedToday }}</h3>
            <p>completed today</p>
          </div>
          <i class="fas fa-check-circle stat-icon"></i>
        </div>

        <div class="stat-card">
          <div class="stat-info">
            <h3>{{ stats.totalProducts }}</h3>
            <p>total products</p>
          </div>
          <i class="fas fa-box stat-icon"></i>
        </div>

        <div class="stat-card">
          <div class="stat-info">
            <h3>₱{{ formatCurrency(stats.todayRevenue) }}</h3>
            <p>today's revenue</p>
          </div>
          <i class="fas fa-dollar-sign stat-icon"></i>
        </div>
      </div>

      <!-- Recent Orders Section -->
      <div class="section-card">
        <div class="section-header">
          <h2>
            <i class="fas fa-shopping-cart"></i>
            Recent Orders
          </h2>
          <NuxtLink to="/Employee/eorders" class="view-all-link">
            View All <i class="fas fa-arrow-right"></i>
          </NuxtLink>
        </div>

        <div v-if="loading" class="loading-state">
          <i class="fas fa-spinner fa-spin"></i>
          <p>Loading orders...</p>
        </div>

        <div v-else-if="recentOrders.length === 0" class="empty-state">
          <i class="fas fa-inbox"></i>
          <p>No recent orders</p>
        </div>

        <div v-else class="orders-table">
          <table>
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in recentOrders" :key="order.id">
                <td>
                  <span class="order-id">#{{ order.id }}</span>
                </td>
                <td>{{ order.customer_name }}</td>
                <td>{{ order.items_count }} items</td>
                <td class="amount">₱{{ formatCurrency(order.total) }}</td>
                <td>
                  <span class="status-badge" :class="order.status">
                    {{ formatStatus(order.status) }}
                  </span>
                </td>
                <td>{{ formatDate(order.created_at) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="actions-grid">
          <NuxtLink to="/Employee/eorders" class="action-card">
            <i class="fas fa-shopping-cart"></i>
            <span>View Orders</span>
          </NuxtLink>
          <NuxtLink to="/Employee/emessages" class="action-card">
            <i class="fas fa-envelope"></i>
            <span>Messages</span>
          </NuxtLink>
          <button type="button" class="action-card" @click="handleLogout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
          </button>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
// Imports
import { ref, onMounted, computed } from 'vue'
import { useHead } from '#imports'
import { useRouter } from 'vue-router'
import { useNuxtApp } from '#app'

// Page Head Configuration
useHead({
  title: 'Employee Dashboard - Kape Na!',
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
const employeeName = ref('Employee')
const loading = ref(true)
const stats = ref({
  pendingOrders: 0,
  completedToday: 0,
  totalProducts: 0,
  todayRevenue: 0,
})
const recentOrders = ref([])

// Methods
const formatCurrency = (amount) => {
  return parseFloat(amount || 0)
    .toFixed(2)
    .replace(/\d(?=(\d{3})+\.)/g, '$&,')
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
}

const formatStatus = (status) => {
  const statusMap = {
    pending: 'Pending',
    processing: 'Processing',
    completed: 'Completed',
    cancelled: 'Cancelled',
  }
  return statusMap[status] || status
}

const handleLogout = async () => {
  try {
    await $axios.post(
      '/employee/logout',
      {},
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('employee_token')}`,
        },
      }
    )
  } catch (error) {
    console.error('Logout error:', error)
  } finally {
    localStorage.removeItem('employee_token')
    localStorage.removeItem('employee_data')
    window.location.href = '/Auth/employee_login'
  }
}

const loadEmployeeData = () => {
  const employeeData = localStorage.getItem('employee_data')
  if (employeeData) {
    try {
      const data = JSON.parse(employeeData)
      employeeName.value = data.name || 'Employee'
    } catch (error) {
      console.error('Error parsing employee data:', error)
    }
  }
}

const fetchDashboardData = async () => {
  try {
    const token = localStorage.getItem('employee_token')
    if (!token) {
      window.location.href = '/Auth/employee_login'
      return
    }

    const response = await $axios.get('/employee/dashboard', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })

    if (response.data.success) {
      stats.value = response.data.stats || stats.value
      recentOrders.value = response.data.recent_orders || []
    }
  } catch (error) {
    console.error('Error fetching dashboard data:', error)
    if (error.response?.status === 401) {
      // Token expired or invalid
      localStorage.removeItem('employee_token')
      localStorage.removeItem('employee_data')
      window.location.href = '/Auth/employee_login'
    }
  } finally {
    loading.value = false
  }
}

// Lifecycle
onMounted(() => {
  // Check if user is logged in
  const token = localStorage.getItem('employee_token')
  if (!token) {
    window.location.href = '/Auth/employee_login'
    return
  }

  loadEmployeeData()
  fetchDashboardData()
})
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

.employee-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.employee-info i {
  font-size: 2.5rem;
  color: var(--main-color);
}

.employee-info span {
  font-size: 1.1rem;
  color: var(--light-color);
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

/* Section Card */
.section-card {
  background: var(--white);
  padding: 2rem;
  border-radius: 0.6rem;
  box-shadow: var(--box-shadow);
  margin-bottom: 2rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.section-header h2 {
  font-size: 1.5rem;
  color: var(--black);
  display: flex;
  align-items: center;
  gap: 0.8rem;
  font-weight: 600;
}

.section-header h2 i {
  color: var(--main-color);
}

.view-all-link {
  color: #6366f1;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s;
}

.view-all-link:hover {
  color: var(--main-color);
  gap: 0.8rem;
}

/* Loading and Empty States */
.loading-state,
.empty-state {
  text-align: center;
  padding: 3rem;
  color: var(--light-color);
}

.loading-state i,
.empty-state i {
  font-size: 3rem;
  margin-bottom: 1rem;
  color: var(--main-color);
  opacity: 0.5;
}

.loading-state p,
.empty-state p {
  font-size: 1.1rem;
}

/* Orders Table */
.orders-table {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: #f9fafb;
}

th {
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  color: var(--black);
  font-size: 0.95rem;
  border-bottom: 2px solid #e5e7eb;
}

td {
  padding: 1rem;
  color: var(--light-color);
  border-bottom: 1px solid #f3f4f6;
}

.order-id {
  font-weight: 600;
  color: #6366f1;
}

.amount {
  font-weight: 600;
  color: var(--black);
}

.status-badge {
  padding: 0.4rem 0.8rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
  text-transform: capitalize;
}

.status-badge.pending {
  background: rgba(245, 158, 11, 0.1);
  color: #f59e0b;
}

.status-badge.processing {
  background: rgba(99, 102, 241, 0.1);
  color: #6366f1;
}

.status-badge.completed {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

.status-badge.cancelled {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
}

/* Quick Actions */
.quick-actions {
  margin-bottom: 2rem;
}

.quick-actions h2 {
  font-size: 1.5rem;
  color: var(--black);
  margin-bottom: 1rem;
  font-weight: 600;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1.5rem;
}

.action-card {
  background: var(--white);
  padding: 2rem;
  border-radius: 0.6rem;
  box-shadow: var(--box-shadow);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.8rem;
  color: var(--black);
  transition: transform 0.3s, box-shadow 0.3s;
  text-align: center;
  cursor: pointer;
  width: 100%;
}

.action-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}

.action-card i {
  font-size: 3rem;
  color: var(--light-color);
  opacity: 0.3;
}

.action-card span {
  font-weight: 500;
  font-size: 1rem;
  color: var(--black);
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

  .orders-table {
    font-size: 0.85rem;
  }

  th,
  td {
    padding: 0.7rem 0.5rem;
  }
}

@media (max-width: 576px) {
  .actions-grid {
    grid-template-columns: 1fr;
  }
}
</style>

