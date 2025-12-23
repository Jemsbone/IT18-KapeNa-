<template>
  <div class="page-shell">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo">
        <span>Kape Na!</span>
        <i class="fas fa-coffee"></i>
      </div>
      <nav class="nav-menu">
        <NuxtLink to="/Employee/edashboard" class="nav-item">
          <i class="fas fa-home"></i>
          <span>Dashboard</span>
        </NuxtLink>
        <NuxtLink to="/Employee/eorders" class="nav-item active">
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
        <h1>Orders Management</h1>
        <div class="employee-info">
          <i class="fas fa-user-tie"></i>
          <span>Employee</span>
        </div>
      </div>

      <!-- Success/Error Messages -->
      <div v-if="successMessage" class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ successMessage }}
      </div>
      <div v-if="errorMessage" class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> {{ errorMessage }}
      </div>

      <!-- Actions Bar -->
      <div class="actions-bar">
        <NuxtLink to="/Employee/edashboard" class="back-btn-small">
          <i class="fas fa-arrow-left"></i>
          Back
        </NuxtLink>
        <div class="search-container">
          <input
            v-model="searchTerm"
            type="text"
            class="search-input"
            placeholder="Search by order number, name, or email"
          />
          <button class="search-btn" @click="loadOrders">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>

      <!-- Order Details Section -->
      <h2 class="section-title">Order Details</h2>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Order Number</th>
              <th>Date</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Address</th>
              <th>Products</th>
              <th>Price</th>
              <th>Payment Type</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in filteredOrders" :key="order.id">
              <td>{{ order.order_number || order.id }}</td>
              <td>{{ order.date || (order.created_at ? new Date(order.created_at).toLocaleDateString() : 'N/A') }}</td>
              <td>{{ order.name }}</td>
              <td>{{ order.email }}</td>
              <td>{{ order.phone }}</td>
              <td>{{ order.address }}</td>
              <td class="products-cell">
                <div class="product-item" v-html="order.products"></div>
              </td>
              <td>₱{{ formatPrice(order.total_price) }}</td>
              <td>{{ order.payment_type }}</td>
              <td>
                <form class="action-form" @submit.prevent="handleStatusUpdate(order)">
                  <div class="status-dropdowns">
                    <div class="dropdown-group">
                      <label class="dropdown-label">Payment Status:</label>
                      <select
                        v-model="order.payment_status"
                        name="payment_status"
                        class="status-select"
                      >
                        <option value="pending">Pending</option>
                        <option value="pending_verification">Pending Verification</option>
                        <option value="paid">Paid</option>
                        <option value="failed">Failed</option>
                      </select>
                    </div>
                    <div class="dropdown-group">
                      <label class="dropdown-label">Order Status:</label>
                      <select
                        v-model="order.status"
                        name="status"
                        class="status-select"
                      >
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="processing">Processing</option>
                        <option value="ready_for_pickup">Ready for Pickup</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                      </select>
                    </div>
                  </div>
                  <div class="action-buttons" style="margin-top: 0.5rem;">
                    <button type="submit" class="btn-update">Update</button>
                    <button
                      type="button"
                      class="btn-delete"
                      @click="deleteOrder(order.id)"
                    >
                      Delete
                    </button>
                  </div>
                </form>
              </td>
            </tr>
            <tr v-if="isLoading">
              <td colspan="10" class="empty-row">Loading orders...</td>
            </tr>
            <tr v-else-if="!filteredOrders.length">
              <td colspan="10" class="empty-row">No orders found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useHead } from '#imports'
import { useRouter } from 'vue-router'
import { useNuxtApp } from '#app'

// Page Head Configuration
useHead({
  title: 'Orders - Kape Na!',
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
const successMessage = ref('')
const errorMessage = ref('')
const searchTerm = ref('')
const isLoading = ref(false)
const orders = ref([])

// Computed Properties
const filteredOrders = computed(() => {
  const term = searchTerm.value.trim().toLowerCase()
  if (!term) return orders.value
  return orders.value.filter((o) => 
    o.order_number?.toLowerCase().includes(term) ||
    o.id?.toString().includes(term) ||
    o.name?.toLowerCase().includes(term) ||
    o.email?.toLowerCase().includes(term)
  )
})

// Methods
const formatPrice = (value) =>
  Number(value || 0)
    .toFixed(2)
    .replace(/\d(?=(\d{3})+\.)/g, '$&,')

// Load orders from API
const loadOrders = async () => {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await $axios.get('/admin/orders')
    if (response.data.success && response.data.data) {
      orders.value = response.data.data
      console.log('Orders loaded:', orders.value.length)
    } else {
      orders.value = []
    }
  } catch (error) {
    console.error('Error loading orders:', error)
    errorMessage.value = 'Failed to load orders. Please try again.'
    orders.value = []
  } finally {
    isLoading.value = false
  }
}

// Update order status
const updateOrderStatus = async (order) => {
  try {
    const response = await $axios.put(`/admin/orders/${order.id}/status`, {
      status: order.status,
      payment_status: order.payment_status || 'pending'
    })
    
    if (response.data.success) {
      successMessage.value = 'Order status updated successfully!'
      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
      // Reload orders to get latest data
      await loadOrders()
    }
  } catch (error) {
    console.error('Error updating order status:', error)
    errorMessage.value = error.response?.data?.message || 'Failed to update order status.'
    setTimeout(() => {
      errorMessage.value = ''
    }, 5000)
  }
}

// Delete order
const deleteOrder = async (id) => {
  if (!confirm('Are you sure you want to delete this order?')) {
    return
  }

  try {
    const response = await $axios.delete(`/admin/orders/${id}`)
    
    if (response.data.success) {
      successMessage.value = 'Order deleted successfully!'
      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
      // Remove from local array
      orders.value = orders.value.filter((o) => o.id !== id)
    }
  } catch (error) {
    console.error('Error deleting order:', error)
    errorMessage.value = error.response?.data?.message || 'Failed to delete order.'
    setTimeout(() => {
      errorMessage.value = ''
    }, 5000)
  }
}

// Handle form submission for status update
const handleStatusUpdate = async (order) => {
  await updateOrderStatus(order)
}

// Handle logout
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

// Load orders on component mount
onMounted(() => {
  const token = localStorage.getItem('employee_token')
  if (!token) {
    window.location.href = '/Auth/employee_login'
  } else {
    loadOrders()
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
  --admin-color: #d3ad7f;
  --bg: #c9b382;
  --sidebar-bg: #2c2c2c;
  --box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
  --delete-btn: #e74c3c;
  --update-btn: #6366f1;
}

/* Base Styles */
html {
  font-size: 12px;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  outline: none;
  border: none;
  text-decoration: none;
  font-family: 'Poppins', sans-serif;
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
  width: 160px;
  background: var(--black);
  min-height: 100vh;
  position: fixed;
  left: 0;
  top: 0;
  padding: 0;
  z-index: 1000;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
}

.logo {
  padding: 0.8rem;
  font-size: 1.2rem;
  color: var(--white);
  display: flex;
  align-items: center;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.logo i {
  color: var(--main-color);
  margin-left: 0.3rem;
  font-size: 1rem;
}

.nav-menu {
  padding: 0.5rem 0;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 0.6rem 1rem;
  color: var(--white);
  font-size: 0.95rem;
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
  margin-right: 0.5rem;
  font-size: 1rem;
  width: 16px;
}

.nav-item span {
  margin-left: 0.3rem;
}

.logout-item {
  position: absolute;
  bottom: 0.5rem;
  width: 100%;
}

.logout-item .nav-item {
  width: 100%;
  background: none;
  padding: 0.6rem 1rem;
}

/* Main Content */
.main-content {
  margin-left: 160px;
  flex: 1;
  padding: 1rem;
}

.header {
  background: var(--white);
  padding: 1rem 1.5rem;
  border-radius: 6px;
  box-shadow: var(--box-shadow);
  margin-bottom: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header h1 {
  font-size: 1.5rem;
  color: var(--black);
}

.employee-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.employee-info i {
  font-size: 1.5rem;
  color: var(--main-color);
}

.employee-info span {
  font-size: 0.95rem;
  color: var(--light-color);
}

.section-title {
  font-size: 1.3rem;
  font-weight: 600;
  color: var(--black);
  margin-bottom: 0.8rem;
}

/* Actions Bar */
.actions-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.back-btn-small {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.4rem 0.8rem;
  background: var(--main-color);
  color: var(--black);
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 500;
  transition: all 0.3s;
}

.back-btn-small:hover {
  background: #8b6f47;
  transform: translateY(-1px);
}

.back-btn-small i {
  font-size: 0.7rem;
}

/* Search Bar */
.search-container {
  display: flex;
  gap: 0.5rem;
}

.search-input {
  padding: 0.4rem 0.8rem;
  border: 1px solid var(--main-color);
  border-radius: 0.3rem;
  font-size: 0.85rem;
  width: 200px;
}

.search-btn {
  padding: 0.4rem 1rem;
  background: var(--main-color);
  color: var(--black);
  border-radius: 0.3rem;
  font-size: 0.85rem;
  cursor: pointer;
  transition: all 0.3s;
}

.search-btn:hover {
  background: #b8956a;
}

/* Table Styles */
.table-container {
  background: var(--white);
  border-radius: 0.3rem;
  overflow-x: auto;
  box-shadow: var(--box-shadow);
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.75rem;
}

thead {
  background: var(--main-color);
  color: var(--white);
}

thead th {
  padding: 0.6rem 0.5rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.75rem;
  white-space: nowrap;
}

tbody tr {
  border-bottom: 1px solid #e0e0e0;
}

tbody tr:hover {
  background: #f5f5f5;
}

tbody td {
  padding: 0.6rem 0.5rem;
  font-size: 0.7rem;
  color: var(--black);
  vertical-align: top;
  max-width: 150px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

tbody td.products-cell {
  white-space: normal;
  max-width: 200px;
}

.products-cell {
  max-width: 200px;
  line-height: 1.4;
  font-weight: 500;
  font-size: 0.7rem;
}

.products-cell .product-item {
  color: var(--black);
}

.products-cell .quantity {
  color: var(--main-color);
  font-weight: 600;
}

/* Status Dropdowns */
.status-dropdowns {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.dropdown-group {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}

.dropdown-label {
  font-size: 0.65rem;
  font-weight: 600;
  color: var(--black);
  margin-bottom: 0.1rem;
}

.status-select {
  padding: 0.3rem 0.4rem;
  border: 1px solid var(--main-color);
  border-radius: 0.2rem;
  font-size: 0.7rem;
  background: var(--white);
  cursor: pointer;
  width: 100%;
}

/* Action Form */
.action-form {
  min-width: 140px;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  gap: 0.3rem;
  margin-top: 0.3rem !important;
}

.btn-update {
  padding: 0.4rem 0.8rem;
  background: var(--update-btn);
  color: var(--black);
  border-radius: 0.2rem;
  cursor: pointer;
  font-size: 0.7rem;
  font-weight: 500;
  transition: all 0.3s;
}

.btn-update:hover {
  background: #4f46e5;
  color: var(--white);
}

.btn-delete {
  padding: 0.4rem 0.8rem;
  background: var(--delete-btn);
  color: var(--black);
  border-radius: 0.2rem;
  cursor: pointer;
  font-size: 0.7rem;
  font-weight: 500;
  transition: all 0.3s;
}

.btn-delete:hover {
  background: #c0392b;
  color: var(--white);
}

.empty-row {
  text-align: center;
  padding: 1rem;
  color: var(--light-color);
  font-size: 0.85rem;
}

/* Success/Error Messages */
.alert {
  padding: 0.6rem 1rem;
  border-radius: 4px;
  margin-bottom: 1rem;
  font-size: 0.8rem;
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
    width: 50px;
  }

  .sidebar .logo span,
  .sidebar .nav-item span {
    display: none;
  }

  .main-content {
    margin-left: 50px;
    padding: 0.5rem;
  }

  .header {
    flex-direction: column;
    gap: 0.5rem;
    padding: 0.8rem;
  }

  .header h1 {
    font-size: 1.2rem;
  }

  .actions-bar {
    flex-direction: column;
    gap: 0.5rem;
  }

  .search-container {
    width: 100%;
  }

  .search-input {
    width: 100%;
  }

  table {
    font-size: 0.65rem;
  }

  thead th,
  tbody td {
    padding: 0.4rem 0.3rem;
  }

  .action-buttons {
    flex-direction: column;
  }
}
</style>

