<template>
  <div class="page">
    <!-- Updated Header section -->
    <header class="header">
      <div class="header-container">
        <!-- Left section with logo -->
        <div class="header-left">
          <NuxtLink to="/Customer/Cdashboard" class="logo">
            <span class="logo-text">Kape</span>
            <span class="logo-accent">Na!</span>
            <i class="fas fa-coffee"></i>
          </NuxtLink>
        </div>

        <!-- Center section with navigation -->
        <div class="header-center">
          <nav class="navbar">
            <NuxtLink to="/Customer/Cdashboard" class="nav-link">Home</NuxtLink>
            <NuxtLink to="/Customer/Cmenue" class="nav-link">Menu</NuxtLink>
            <NuxtLink to="/Customer/Ccart" class="nav-link">Cart</NuxtLink>
            <NuxtLink to="/Customer/Cabout" class="nav-link">About</NuxtLink>
          </nav>
        </div>

        <!-- Right section with auth/user -->
        <div class="header-right">
          <template v-if="isAuthenticated">
            <!-- User Profile when logged in -->
            <div class="user-profile">
              <div class="dropdown">
                <div class="user-info user-name">
                  <i class="fas fa-user-circle user-icon"></i>
                  <span>{{ user?.name }}</span>
                  <i
                    class="fas fa-chevron-down"
                    style="font-size: 1.2rem; margin-left: 0.5rem"
                  ></i>
                </div>
                <div class="dropdown-content">
                  <NuxtLink to="/Customer/Cprofile">
                    <i class="fas fa-user"></i> My Profile
                  </NuxtLink>
                  <NuxtLink to="/Customer/Ccart">
                    <i class="fas fa-shopping-cart"></i> Cart
                  </NuxtLink>
                  <NuxtLink to="/Customer/Csettings">
                    <i class="fas fa-cog"></i> Settings
                  </NuxtLink>
                  <a href="#" @click.prevent="handleLogout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                  </a>
                </div>
              </div>
            </div>
          </template>
          <template v-else>
            <!-- Login/Register buttons when not logged in -->
            <div class="auth-buttons">
              <NuxtLink to="/Auth/login" class="auth-btn">Login</NuxtLink>
              <NuxtLink to="/Auth/register" class="auth-btn register">Register</NuxtLink>
            </div>
          </template>
        </div>
      </div>
    </header>

    <!-- Success Messages -->
    <div v-if="successMessage" class="success-message">
      {{ successMessage }}
    </div>

    <!-- Error Messages -->
    <div v-if="errorMessage" class="error-message">
      {{ errorMessage }}
    </div>

    <!-- Profile Section -->
    <section class="profile-section">
      <h1 class="title">My Profile</h1>

      <div v-if="loading" class="loading-message">Loading profile...</div>

      <div v-else-if="user" class="profile-container">
        <!-- Personal Information Card -->
        <div class="info-card">
          <h2><i class="fas fa-user-circle"></i> Personal Information</h2>

          <div class="info-grid">
            <div class="info-item">
              <label><i class="fas fa-user"></i> Full Name</label>
              <div class="value">{{ user.name }}</div>
            </div>

            <div class="info-item">
              <label><i class="fas fa-envelope"></i> Email Address</label>
              <div class="value">{{ user.email }}</div>
            </div>

            <div class="info-item">
              <label><i class="fas fa-phone"></i> Phone Number</label>
              <div class="value">{{ user.phone || 'Not provided' }}</div>
            </div>

            <div class="info-item">
              <label><i class="fas fa-map-marker-alt"></i> Address</label>
              <div class="value">{{ user.address || 'Not provided' }}</div>
            </div>

            <div class="info-item">
              <label><i class="fas fa-calendar-alt"></i> Member Since</label>
              <div class="value">{{ formatDate(user.created_at) }}</div>
            </div>

            <div class="info-item">
              <label><i class="fas fa-check-circle"></i> Email Verified</label>
              <div class="value">
                <span v-if="user.email_verified_at" style="color: #2ecc71">
                  ✓ Verified
                </span>
                <span v-else style="color: #e74c3c">✗ Not Verified</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Order History Section -->
      <div class="orders-section">
        <h2><i class="fas fa-shopping-bag"></i> Order History</h2>

        <div v-if="ordersLoading" class="loading-message">Loading orders...</div>

        <template v-else-if="orders && orders.length > 0">
          <div v-for="order in orders" :key="order.id" class="order-card">
            <div class="order-header">
              <span class="order-number">
                <i class="fas fa-receipt"></i> Order #{{ order.order_number || order.id }}
              </span>
              <span
                class="order-status"
                :class="getStatusClass(order.status)"
              >
                {{ formatStatus(order.status) }}
              </span>
            </div>

            <!-- Order Items -->
            <div v-if="order.cart_data && order.cart_data.length > 0" class="order-items">
              <h4 class="items-title">Items:</h4>
              <div class="items-list">
                <div v-for="(item, index) in order.cart_data" :key="index" class="order-item">
                  <span class="item-name">{{ item.name }}</span>
                  <span class="item-quantity">x{{ item.quantity }}</span>
                  <span class="item-price">₱{{ formatPrice(item.price * item.quantity) }}</span>
                </div>
              </div>
            </div>

            <div class="order-details">
              <div class="order-detail-item">
                <strong><i class="fas fa-credit-card"></i> Payment Method:</strong>
                {{ formatPaymentMethod(order.payment_method) }}
                <span v-if="order.payment_details?.phone_number" class="payment-detail">
                  ({{ order.payment_details.phone_number }})
                </span>
                <span v-if="order.payment_details?.bank_account" class="payment-detail">
                  ({{ order.payment_details.bank_account }})
                </span>
              </div>

              <div class="order-detail-item">
                <strong><i class="fas fa-calendar"></i> Order Date:</strong>
                {{ formatOrderDate(order.created_at) }}
              </div>

              <div v-if="order.order_summary" class="order-summary">
                <div class="summary-row">
                  <span>Subtotal:</span>
                  <span>₱{{ formatPrice(order.order_summary.subtotal || 0) }}</span>
                </div>
                <div class="summary-row">
                  <span>Service Fee:</span>
                  <span>₱{{ formatPrice(order.order_summary.serviceFee || 0) }}</span>
                </div>
                <div class="summary-row">
                  <span>Tax:</span>
                  <span>₱{{ formatPrice(order.order_summary.tax || 0) }}</span>
                </div>
              </div>

              <div class="order-detail-item total-item">
                <strong><i class="fas fa-dollar-sign"></i> Total Amount:</strong>
                <span class="highlight">
                  ₱{{ formatPrice(order.total_amount || 0) }}
                </span>
              </div>
            </div>
          </div>
        </template>

        <div v-else class="empty-orders">
          <i class="fas fa-shopping-bag empty-orders-icon"></i>
          <h3>No Orders Yet</h3>
          <p>You haven't placed any orders yet. Start shopping now!</p>
          <NuxtLink to="/Customer/Cdashboard" class="btn">Browse Menu</NuxtLink>
        </div>
      </div>
    </section>

    <!-- Footer section -->
    <footer class="footer">
      <section class="grid">
        <div class="box">
          <h3>Cafe Shop <i class="fas fa-shopping-basket"></i></h3>
          <div class="share">
            <a
              href="https://www.facebook.com/jemhail"
              class="fab fa-facebook-f"
            ></a>
            <a
              href="https://www.instagram.com/jembo.magbanua/"
              class="fab fa-instagram"
            ></a>
            <a
              href="http://linkedin.com/in/jembo-magbanua-b49b6b2b2/"
              class="fab fa-linkedin"
            ></a>
          </div>
        </div>
        <div class="box">
          <h3>Opening Hours</h3>
          <div class="dateinfo">
            <p>MONDAY</p>
            <samp>2:00PM - 2:00AM</samp>
          </div>
          <div class="dateinfo">
            <p>TUESDAY</p>
            <samp>2:00PM - 2:00AM</samp>
          </div>
          <div class="dateinfo">
            <p>WEDNESDAY</p>
            <samp>2:00PM - 2:00AM</samp>
          </div>
          <div class="dateinfo">
            <p>THURSDAY</p>
            <samp>2:00PM - 2:00AM</samp>
          </div>
          <div class="dateinfo">
            <p>FRIDAY</p>
            <samp>2:00PM - 2:00AM</samp>
          </div>
          <div class="dateinfo">
            <p>SATURDAY</p>
            <samp>2:00PM - 2:00AM</samp>
          </div>
          <div class="dateinfo">
            <p>SUNDAY</p>
            <samp>CLOSE!</samp>
          </div>
        </div>
        <div class="box">
          <h3>Contact Info</h3>
          <div class="phone">
            <p><i class="fas fa-phone"></i> +639914677225</p>
            <p><i class="fas fa-phone"></i> +639512592678</p>
            <p><i class="fas fa-envelope"></i> magbanuajembo17@gmail.com</p>
          </div>
          <div class="phone1">
            <h3>Branch Location</h3>
            <p>
              <i class="fas fa-map-marker-alt"></i> Caraga State University -
              Main Campus
            </p>
            <p><i class="fas fa-envelope"></i> magbanuajembo17@gmail.com</p>
          </div>
        </div>
      </section>
      <div class="credit">
        &copy; copyright @ {{ new Date().getFullYear() }} by
        <span>Cafe Shop</span> | <a>Magbanua, Jembo</a> | all rights reserved!
      </div>
    </footer>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useHead } from '#imports'

// Load Font Awesome
useHead({
  link: [
    {
      rel: 'stylesheet',
      href: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css',
    },
  ],
})

// Authentication
const { user, isAuthenticated, fetchUser, logout } = useAuth()
const router = useRouter()
const { $axios } = useNuxtApp()

// State
const loading = ref(false)
const ordersLoading = ref(false)
const orders = ref([])
const successMessage = ref('')
const errorMessage = ref('')

// Redirect if not authenticated
onMounted(async () => {
  if (!isAuthenticated.value) {
    await fetchUser()
  }
  
  if (!isAuthenticated.value) {
    await router.push('/Auth/login')
    return
  }

  await loadProfileData()
})

// Load profile data
const loadProfileData = async () => {
  loading.value = true
  try {
    await fetchUser()
    await loadOrders()
  } catch (error) {
    console.error('Error loading profile:', error)
    errorMessage.value = 'Failed to load profile data'
  } finally {
    loading.value = false
  }
}

// Load orders from API
const loadOrders = async () => {
  ordersLoading.value = true
  try {
    const response = await $axios.get('/orders')
    
    if (response.data.success) {
      orders.value = response.data.data.map(order => ({
        id: order.id,
        order_number: order.order_number,
        cart_data: order.cart_data || [],
        order_summary: order.order_summary || {},
        payment_method: order.payment_method,
        payment_details: order.payment_details || {},
        status: order.status || 'pending',
        total_amount: order.total_amount || 0,
        created_at: order.created_at,
        updated_at: order.updated_at,
      }))
    } else {
      orders.value = []
    }
  } catch (error) {
    console.error('Error loading orders:', error)
    // Don't show error for orders if endpoint doesn't exist yet
    orders.value = []
  } finally {
    ordersLoading.value = false
  }
}

// Handle logout
const handleLogout = async () => {
  try {
    await logout()
  } catch (error) {
    console.error('Logout error:', error)
  }
}

// Scroll to about section
const scrollToAbout = () => {
  if (process.client) {
    const aboutSection = document.querySelector('.about')
    if (aboutSection) {
      aboutSection.scrollIntoView({ behavior: 'smooth' })
    } else {
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }
  }
}

// Format date
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  const options = { year: 'numeric', month: 'long', day: 'numeric' }
  return date.toLocaleDateString('en-US', options)
}

// Format order date
const formatOrderDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  const options = {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: true,
  }
  return date.toLocaleDateString('en-US', options)
}

// Format payment method
const formatPaymentMethod = (method) => {
  if (!method) return 'N/A'
  return method.charAt(0).toUpperCase() + method.slice(1).toLowerCase()
}

// Format price
const formatPrice = (price) => {
  return parseFloat(price).toFixed(2)
}

// Get status class
const getStatusClass = (status) => {
  if (!status) return 'pending'
  const lowerStatus = status.toLowerCase()
  if (lowerStatus === 'completed' || lowerStatus === 'confirmed') {
    return 'completed'
  }
  if (lowerStatus === 'processing') {
    return 'processing'
  }
  if (lowerStatus === 'cancelled') {
    return 'cancelled'
  }
  return 'pending'
}

// Format status
const formatStatus = (status) => {
  if (!status) return 'Pending'
  return status.charAt(0).toUpperCase() + status.slice(1).toLowerCase()
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

section {
  margin: 0 auto;
  max-width: 1200px;
  padding: 2rem;
}

.title {
  text-align: center;
  margin-bottom: 2rem;
  font-size: 3rem;
  color: var(--white);
  text-transform: uppercase;
  text-underline-offset: 1rem;
}

.btn {
  margin-top: 1rem;
  display: inline-block;
  font-size: 1.6rem;
  padding: 0.8rem 2.4rem;
  cursor: pointer;
  text-transform: capitalize;
  transition: 0.2s linear;
  background-color: var(--yellow);
  color: var(--white);
}

.btn:hover {
  letter-spacing: 0.2rem;
}

/* Header styles */
/* ===== UPDATED HEADER STYLES ===== */
.header {
  background-color: var(--black);
  padding: 1.5rem 2rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
  position: sticky;
  top: 0;
  z-index: 1000;
  width: 100%;
}

.header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
}

/* Left Section - Logo */
.header-left {
  flex: 0 0 auto;
}

.logo {
  display: flex;
  align-items: center;
  font-size: 2.8rem;
  font-weight: bold;
  color: var(--white);
  text-decoration: none;
}

.logo-text {
  color: var(--white);
}

.logo-accent {
  color: var(--main-color);
  margin-left: 0.2rem;
}

.logo i {
  color: var(--main-color);
  margin-left: 0.5rem;
  font-size: 2.5rem;
}

/* Center Section - Navigation */
.header-center {
  flex: 1;
  display: flex;
  justify-content: center;
}

.navbar {
  display: flex;
  gap: 3rem;
  align-items: center;
}

.nav-link {
  color: var(--white);
  font-size: 2rem;
  text-decoration: none;
  padding: 0.5rem 1rem;
  position: relative;
  transition: color 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.nav-link:hover {
  color: var(--main-color);
}

.nav-link.active {
  color: var(--main-color);
  font-weight: bold;
}

.nav-link.active::after {
  content: '';
  position: absolute;
  bottom: -5px;
  left: 50%;
  transform: translateX(-50%);
  width: 60%;
  height: 2px;
  background-color: var(--main-color);
  border-radius: 1px;
}

/* Right Section - Auth/User */
.header-right {
  flex: 0 0 auto;
  display: flex;
  align-items: center;
}

/* User Profile */
.user-profile {
  position: relative;
}

.user-dropdown-btn {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  background: none;
  border: 1px solid var(--main-color);
  padding: 0.8rem 1.5rem;
  border-radius: 4px;
  cursor: pointer;
  color: var(--white);
  font-size: 1.6rem;
  transition: all 0.3s ease;
}

.user-dropdown-btn:hover {
  background-color: rgba(211, 173, 127, 0.1);
  transform: translateY(-2px);
}

.user-icon {
  font-size: 2rem;
  color: var(--main-color);
}

.dropdown-icon {
  font-size: 1.2rem;
  transition: transform 0.3s ease;
}

.user-dropdown-btn:hover .dropdown-icon {
  transform: rotate(180deg);
}

/* Dropdown Menu */
.dropdown-content {
  position: absolute;
  top: 100%;
  right: 0;
  background-color: var(--black);
  min-width: 220px;
  border: 1px solid var(--main-color);
  border-radius: 4px;
  padding: 0.5rem 0;
  display: none;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
  z-index: 1001;
}

.user-profile:hover .dropdown-content {
  display: block;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.5rem;
  color: var(--white);
  text-decoration: none;
  font-size: 1.6rem;
  transition: all 0.3s ease;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.dropdown-item:hover {
  background-color: rgba(211, 173, 127, 0.1);
  color: var(--main-color);
  padding-left: 2rem;
}

.dropdown-item i {
  width: 20px;
  text-align: center;
}

.dropdown-item.logout {
  color: #ff6b6b;
}

.dropdown-item.logout:hover {
  background-color: rgba(255, 107, 107, 0.1);
}

/* Auth Buttons */
.auth-buttons {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.auth-btn {
  padding: 0.8rem 2rem;
  font-size: 1.6rem;
  text-decoration: none;
  border-radius: 4px;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.auth-btn:not(.register) {
  color: var(--white);
  border: 1px solid var(--white);
}

.auth-btn:not(.register):hover {
  background-color: var(--white);
  color: var(--black);
}

.auth-btn.register {
  background-color: var(--main-color);
  color: var(--black);
  border: 1px solid var(--main-color);
}

.auth-btn.register:hover {
  background-color: transparent;
  color: var(--main-color);
}

.success-message {
  background-color: rgba(46, 204, 113, 0.2);
  color: #2ecc71;
  padding: 1rem 2rem;
  margin: 1rem auto;
  max-width: 1200px;
  border-radius: 0.5rem;
  font-size: 1.6rem;
  text-align: center;
  border: 1px solid #2ecc71;
}

.error-message {
  background-color: rgba(231, 76, 60, 0.2);
  color: #e74c3c;
  padding: 1rem 2rem;
  margin: 1rem auto;
  max-width: 1200px;
  border-radius: 0.5rem;
  font-size: 1.6rem;
  text-align: center;
  border: 1px solid #e74c3c;
}

.loading-message {
  text-align: center;
  padding: 2rem;
  font-size: 1.6rem;
  color: var(--light-color);
}

/* Profile Section Styles */
.profile-section {
  min-height: 70vh;
  padding: 2rem;
}

.profile-container {
  display: grid;
  grid-template-columns: 1fr;
  gap: 3rem;
  margin-top: 2rem;
}

/* Personal Information Card */
.info-card {
  background-color: var(--black);
  border: var(--border);
  border-radius: 1rem;
  padding: 3rem;
}

.info-card h2 {
  font-size: 2.8rem;
  color: var(--main-color);
  margin-bottom: 2rem;
  text-align: center;
  border-bottom: 2px solid var(--main-color);
  padding-bottom: 1rem;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
}

.info-item {
  padding: 1.5rem;
  background-color: rgba(255, 255, 255, 0.05);
  border-radius: 0.5rem;
  transition: all 0.3s;
}

.info-item:hover {
  background-color: rgba(211, 173, 127, 0.1);
  transform: translateY(-3px);
}

.info-item label {
  display: block;
  font-size: 1.4rem;
  color: var(--light-color);
  margin-bottom: 0.5rem;
  text-transform: uppercase;
}

.info-item .value {
  font-size: 1.8rem;
  color: var(--white);
  word-wrap: break-word;
}

.info-item i {
  color: var(--main-color);
  margin-right: 1rem;
  font-size: 2rem;
}

/* Order History Section */
.orders-section {
  background-color: var(--black);
  border: var(--border);
  border-radius: 1rem;
  padding: 3rem;
  margin-top: 3rem;
}

.orders-section h2 {
  font-size: 2.8rem;
  color: var(--main-color);
  margin-bottom: 2rem;
  text-align: center;
  border-bottom: 2px solid var(--main-color);
  padding-bottom: 1rem;
}

.order-card {
  background-color: rgba(255, 255, 255, 0.05);
  border-left: 4px solid var(--main-color);
  padding: 2rem;
  margin-bottom: 2rem;
  border-radius: 0.5rem;
  transition: all 0.3s;
}

.order-card:hover {
  background-color: rgba(211, 173, 127, 0.1);
  transform: translateX(5px);
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.order-number {
  font-size: 2rem;
  color: var(--white);
  font-weight: bold;
}

.order-status {
  padding: 0.5rem 1.5rem;
  border-radius: 2rem;
  font-size: 1.4rem;
  text-transform: uppercase;
}

.order-status.pending {
  background-color: rgba(241, 196, 15, 0.2);
  color: #f1c40f;
  border: 1px solid #f1c40f;
}

.order-status.completed {
  background-color: rgba(46, 204, 113, 0.2);
  color: #2ecc71;
  border: 1px solid #2ecc71;
}

.order-status.processing {
  background-color: rgba(52, 152, 219, 0.2);
  color: #3498db;
  border: 1px solid #3498db;
}

.order-status.cancelled {
  background-color: rgba(231, 76, 60, 0.2);
  color: #e74c3c;
  border: 1px solid #e74c3c;
}

.order-details {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-top: 1rem;
}

.order-detail-item {
  font-size: 1.6rem;
  color: var(--light-color);
}

.order-detail-item strong {
  color: var(--white);
  display: block;
  margin-bottom: 0.5rem;
}

.order-detail-item .highlight {
  color: var(--main-color);
  font-size: 2rem;
  font-weight: bold;
}

.order-detail-item.total-item {
  border-top: 2px solid var(--main-color);
  margin-top: 1rem;
  padding-top: 1rem;
  font-size: 1.8rem;
}

.payment-detail {
  color: var(--light-color);
  font-size: 1.4rem;
  margin-left: 0.5rem;
}

/* Order Items */
.order-items {
  margin: 1.5rem 0;
  padding: 1.5rem;
  background-color: rgba(255, 255, 255, 0.03);
  border-radius: 0.5rem;
}

.items-title {
  font-size: 1.6rem;
  color: var(--white);
  margin-bottom: 1rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  padding-bottom: 0.5rem;
}

.items-list {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
}

.order-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.8rem;
  background-color: rgba(255, 255, 255, 0.05);
  border-radius: 0.3rem;
  font-size: 1.5rem;
}

.item-name {
  flex: 1;
  color: var(--white);
}

.item-quantity {
  color: var(--light-color);
  margin: 0 1rem;
  font-size: 1.4rem;
}

.item-price {
  color: var(--main-color);
  font-weight: bold;
  font-size: 1.6rem;
}

.order-summary {
  margin: 1rem 0;
  padding: 1rem;
  background-color: rgba(255, 255, 255, 0.03);
  border-radius: 0.5rem;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  padding: 0.5rem 0;
  font-size: 1.5rem;
  color: var(--light-color);
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.summary-row:last-child {
  border-bottom: none;
}

.order-status.completed {
  background-color: rgba(46, 204, 113, 0.2);
  color: #2ecc71;
  border: 1px solid #2ecc71;
}

.order-status.processing {
  background-color: rgba(52, 152, 219, 0.2);
  color: #3498db;
  border: 1px solid #3498db;
}

.order-status.cancelled {
  background-color: rgba(231, 76, 60, 0.2);
  color: #e74c3c;
  border: 1px solid #e74c3c;
}

.empty-orders {
  text-align: center;
  padding: 4rem 2rem;
}

.empty-orders-icon {
  font-size: 8rem;
  color: var(--light-color);
  margin-bottom: 2rem;
}

.empty-orders h3 {
  font-size: 2.5rem;
  color: var(--white);
  margin-bottom: 1.5rem;
}

.empty-orders p {
  font-size: 1.6rem;
  color: var(--light-color);
  margin-bottom: 2rem;
}

/* Footer styles */
.footer {
  background: var(--black);
  padding: 3rem 2rem;
  text-align: center;
  margin-top: 5rem;
}

.footer .grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.5rem;
  align-items: flex-start;
}

.footer .grid .box {
  text-align: center;
  padding: 1.2rem 1rem;
  border: var(--border);
}

.footer .grid .box h3 {
  font-size: 1rem;
  margin-bottom: 1.5rem;
  color: var(--white);
  text-transform: capitalize;
}

.footer .grid .box a,
.footer .grid .box p {
  display: block;
  padding: 0.2rem 0;
  font-size: 1rem;
  color: var(--light-color);
}

.footer .grid .box a:hover {
  color: var(--white);
  text-decoration: underline;
}

.footer .grid .box .share {
  margin-top: 1rem;
}

.footer .grid .box .share a {
  display: inline-block;
  margin: 0 0.5rem;
  height: 4rem;
  width: 4rem;
  line-height: 4rem;
  font-size: 1rem;
  background: var(--white);
  color: var(--black);
  border-radius: 50%;
}

.footer .grid .box .share a:hover {
  background: var(--main-color);
  color: var(--white);
}

.footer .grid .box .dateinfo {
  display: flex;
  justify-content: space-between;
  padding: 0.1rem 0;
}

.footer .grid .box .dateinfo p,
.footer .grid .box .dateinfo samp {
  font-size: 1rem;
  color: var(--light-color);
}

.footer .grid .box .phone p {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
}

.footer .grid .box .phone1 h3 {
  margin-top: 2rem;
}

.footer .credit {
  margin-top: 2.5rem;
  padding: 2rem 1rem;
  border-top: var(--border);
  font-size: 1rem;
  color: var(--white);
}

.footer .credit span {
  color: var(--main-color);
}

.footer .credit a {
  color: var(--main-color);
}

.footer .credit a:hover {
  text-decoration: underline;
}

/* Media queries */
@media (max-width: 991px) {
  html {
    font-size: 55%;
  }
}

@media (max-width: 768px) {
  .header .navbar {
    position: absolute;
    top: 99%;
    left: 0;
    right: 0;
    background-color: var(--black);
    clip-path: polygon(0 0, 100% 0, 100% 0, 0 0);
    transition: 0.3s linear;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }

  .order-header {
    flex-direction: column;
    align-items: flex-start;
  }
}

@media (max-width: 450px) {
  html {
    font-size: 50%;
  }

  .title {
    font-size: 3rem;
  }
}
</style>

