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
                <button class="user-dropdown-btn">
                  <i class="fas fa-user-circle user-icon"></i>
                  <span>{{ user?.name }}</span>
                  <i class="fas fa-chevron-down dropdown-icon"></i>
                </button>
                <div class="dropdown-content">
                  <NuxtLink to="/Customer/Cprofile" class="dropdown-item">
                    <i class="fas fa-user"></i> My Profile
                  </NuxtLink>
                  <NuxtLink to="/Customer/Ccart" class="dropdown-item">
                    <i class="fas fa-shopping-cart"></i> Cart
                  </NuxtLink>
                  <NuxtLink to="/Customer/Csettings" class="dropdown-item">
                    <i class="fas fa-cog"></i> Settings
                  </NuxtLink>
                  <a href="#" @click.prevent="handleLogout" class="dropdown-item logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                  </a>
                </div>
              </div>
            </div>
          </template>
          <template v-else>
            <!-- Auth buttons when not logged in -->
            <div class="auth-buttons">
              <NuxtLink to="/Auth/login" class="auth-btn">Login</NuxtLink>
              <NuxtLink to="/Auth/register" class="auth-btn register">Register</NuxtLink>
            </div>
          </template>
        </div>
      </div>
    </header>

    <!-- Confirmation Section -->
    <section class="confirmation-section">
      <div class="confirmation-container">
        <!-- Success Icon -->
        <div class="success-icon">
          <i class="fas fa-check-circle"></i>
        </div>

        <!-- Confirmation Message -->
        <h1 class="confirmation-title">Order Confirmed!</h1>
        <p class="confirmation-subtitle">
          Thank you for your order. We've received your order and will begin preparing it right away.
        </p>

        <!-- Order Number -->
        <div v-if="orderNumber" class="order-number-box">
          <p class="order-label">Your Order Number</p>
          <h2 class="order-number">{{ orderNumber }}</h2>
          <p class="order-note">
            <i class="fas fa-envelope"></i>
            A confirmation email has been sent to <strong>{{ user?.email || 'your email' }}</strong>
          </p>
        </div>

        <!-- Order Details (if available) -->
        <div v-if="orderDetails" class="order-details">
          <h3 class="details-title">Order Summary</h3>
          
          <div class="summary-item">
            <span>Subtotal:</span>
            <span>₱{{ formatPrice(orderDetails.subtotal) }}</span>
          </div>
          <div class="summary-item">
            <span>Service Fee:</span>
            <span>₱{{ formatPrice(orderDetails.serviceFee) }}</span>
          </div>
          <div class="summary-item">
            <span>Tax:</span>
            <span>₱{{ formatPrice(orderDetails.tax) }}</span>
          </div>
          <div class="summary-item total">
            <span>Total:</span>
            <span>₱{{ formatPrice(orderDetails.total) }}</span>
          </div>
        </div>

        <!-- Next Steps -->
        <div class="next-steps">
          <h3>What's Next?</h3>
          <ul>
            <li>
              <i class="fas fa-envelope"></i>
              Check your email for order confirmation and details
            </li>
            <li>
              <i class="fas fa-clock"></i>
              We'll notify you when your order is ready for pickup
            </li>
            <li>
              <i class="fas fa-phone"></i>
              If you have questions, contact us at +639914677225
            </li>
          </ul>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
          <NuxtLink to="/Customer/Cdashboard" class="btn btn-primary">
            <i class="fas fa-home"></i>
            Continue Shopping
          </NuxtLink>
          <NuxtLink to="/Customer/Cprofile" class="btn btn-secondary">
            <i class="fas fa-list"></i>
            View My Orders
          </NuxtLink>
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
import { ref, onMounted } from 'vue'
import { useHead, useRoute, useRouter } from '#imports'
import { useAuth } from '@/composables/useAuth'

// Page Head Configuration
useHead({
  title: 'Order Confirmation - Kape Na!',
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

// Authentication
const { user, isAuthenticated, logout } = useAuth()
const route = useRoute()
const router = useRouter()

// State
const orderNumber = ref('')
const orderDetails = ref(null)

// Get order number from route query
onMounted(() => {
  if (route.query.order_number) {
    orderNumber.value = route.query.order_number
  }
  
  // Get order details from route query if available
  if (route.query.subtotal) {
    orderDetails.value = {
      subtotal: parseFloat(route.query.subtotal) || 0,
      serviceFee: parseFloat(route.query.serviceFee) || 0,
      tax: parseFloat(route.query.tax) || 0,
      total: parseFloat(route.query.total) || 0,
    }
  }

  // If no order number, redirect to dashboard after 3 seconds
  if (!orderNumber.value) {
    setTimeout(() => {
      router.push('/Customer/Cdashboard')
    }, 3000)
  }
})

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

// Format price
const formatPrice = (price) => {
  return parseFloat(price || 0).toFixed(2)
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
  --success-color: #2ecc71;
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

/* Header styles */
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

.header-right {
  flex: 0 0 auto;
  display: flex;
  align-items: center;
}

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

/* Confirmation Section */
.confirmation-section {
  min-height: 70vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
}

.confirmation-container {
  background-color: var(--black);
  border: var(--border);
  border-radius: 1rem;
  padding: 4rem 3rem;
  max-width: 700px;
  width: 100%;
  text-align: center;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.success-icon {
  font-size: 8rem;
  color: var(--success-color);
  margin-bottom: 2rem;
  animation: scaleIn 0.5s ease-out;
}

@keyframes scaleIn {
  from {
    transform: scale(0);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

.confirmation-title {
  font-size: 3.5rem;
  color: var(--white);
  margin-bottom: 1rem;
  text-transform: uppercase;
}

.confirmation-subtitle {
  font-size: 1.8rem;
  color: var(--light-color);
  margin-bottom: 3rem;
  line-height: 1.6;
}

.order-number-box {
  background-color: rgba(211, 173, 127, 0.1);
  border: 2px solid var(--main-color);
  border-radius: 0.5rem;
  padding: 2rem;
  margin: 3rem 0;
}

.order-label {
  font-size: 1.4rem;
  color: var(--light-color);
  margin-bottom: 1rem;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.order-number {
  font-size: 2.5rem;
  color: var(--main-color);
  margin-bottom: 1.5rem;
  font-weight: bold;
  letter-spacing: 2px;
}

.order-note {
  font-size: 1.4rem;
  color: var(--light-color);
  margin-top: 1rem;
}

.order-note i {
  color: var(--main-color);
  margin-right: 0.5rem;
}

.order-details {
  background-color: rgba(255, 255, 255, 0.05);
  border-radius: 0.5rem;
  padding: 2rem;
  margin: 2rem 0;
  text-align: left;
}

.details-title {
  font-size: 2rem;
  color: var(--white);
  margin-bottom: 1.5rem;
  text-align: center;
  border-bottom: 1px solid var(--light-color);
  padding-bottom: 1rem;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  padding: 1rem 0;
  font-size: 1.6rem;
  color: var(--white);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.summary-item.total {
  font-size: 2rem;
  font-weight: bold;
  color: var(--main-color);
  border-top: 2px solid var(--main-color);
  margin-top: 1rem;
  padding-top: 1.5rem;
}

.next-steps {
  background-color: rgba(211, 173, 127, 0.1);
  border-radius: 0.5rem;
  padding: 2rem;
  margin: 3rem 0;
  text-align: left;
}

.next-steps h3 {
  font-size: 2rem;
  color: var(--white);
  margin-bottom: 1.5rem;
  text-align: center;
}

.next-steps ul {
  list-style: none;
  padding: 0;
}

.next-steps li {
  font-size: 1.6rem;
  color: var(--light-color);
  padding: 1rem 0;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.next-steps li i {
  color: var(--main-color);
  font-size: 1.8rem;
  width: 30px;
}

.action-buttons {
  display: flex;
  gap: 2rem;
  justify-content: center;
  margin-top: 3rem;
  flex-wrap: wrap;
}

.btn {
  padding: 1.2rem 3rem;
  font-size: 1.8rem;
  border-radius: 0.5rem;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 1rem;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.btn-primary {
  background-color: var(--main-color);
  color: var(--black);
}

.btn-primary:hover {
  background-color: #c19a6b;
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(211, 173, 127, 0.3);
}

.btn-secondary {
  background-color: transparent;
  color: var(--white);
  border: 2px solid var(--white);
}

.btn-secondary:hover {
  background-color: var(--white);
  color: var(--black);
  transform: translateY(-2px);
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

/* Responsive Design */
@media (max-width: 768px) {
  .confirmation-container {
    padding: 3rem 2rem;
  }

  .confirmation-title {
    font-size: 2.5rem;
  }

  .confirmation-subtitle {
    font-size: 1.5rem;
  }

  .order-number {
    font-size: 2rem;
  }

  .action-buttons {
    flex-direction: column;
  }

  .btn {
    width: 100%;
    justify-content: center;
  }

  .header .navbar {
    position: absolute;
    top: 99%;
    left: 0;
    right: 0;
    background-color: var(--black);
    clip-path: polygon(0 0, 100% 0, 100% 0, 0 0);
    transition: 0.3s linear;
  }

  .header .navbar.active {
    clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
  }

  .header .navbar a {
    display: block;
    margin: 2rem;
    font-size: 2.2rem;
  }
}

@media (max-width: 450px) {
  .confirmation-title {
    font-size: 2rem;
  }

  .order-number {
    font-size: 1.8rem;
  }
}
</style>

