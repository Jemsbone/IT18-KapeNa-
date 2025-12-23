<template>
  <div class="page">
    <!-- Loading Overlay -->
    <div v-if="isProcessing" class="loading-overlay">
      <div class="loading-spinner"></div>
      <div class="loading-text">Processing your order...</div>
    </div>

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
            <NuxtLink to="/Customer/Ccart" class="nav-link active">Cart</NuxtLink>
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

    <!-- Cart Section -->
    <section class="cart-section">
      <h1 class="title">My Cart</h1>

      <!-- Cart Container -->
      <div class="cart-container" :class="{ empty: cartItems.length === 0 }">
        <!-- Cart Items -->
        <div v-if="cartItems.length > 0" class="cart-items">
          <div
            v-for="item in cartItems"
            :key="item.id"
            class="cart-item"
          >
            <div class="item-image">
              <img
                :src="item.image || 'http://localhost:8000/images/product-1.png'"
                :alt="item.name"
              />
            </div>
            <div class="item-details">
              <h3>{{ item.name }}</h3>
              <p>Price: ₱{{ formatPrice(item.price) }}</p>
            </div>
            <div class="item-quantity">
              <span
                class="quantity-btn minus"
                @click="decreaseQuantity(item.id)"
              >
                -
              </span>
              <input
                type="number"
                class="quantity-input"
                :value="item.quantity"
                min="1"
                @change="updateQuantity(item.id, $event.target.value)"
              />
              <span
                class="quantity-btn plus"
                @click="increaseQuantity(item.id)"
              >
                +
              </span>
            </div>
            <div class="item-price">
              ₱{{ formatPrice(item.price * item.quantity) }}
            </div>
            <div class="item-remove" @click="removeItem(item.id)">
              <i class="fas fa-trash"></i>
            </div>
          </div>
        </div>

        <!-- Cart Summary -->
        <div class="cart-summary">
          <h3 class="summary-title">Order Summary</h3>

          <div class="summary-row">
            <span class="summary-label">Subtotal</span>
            <span class="summary-value">₱{{ formatPrice(subtotal) }}</span>
          </div>

          <div class="summary-row">
            <span class="summary-label">Service Fee</span>
            <span class="summary-value">₱{{ formatPrice(serviceFee) }}</span>
          </div>

          <div class="summary-row">
            <span class="summary-label">Tax</span>
            <span class="summary-value">₱{{ formatPrice(tax) }}</span>
          </div>

          <div class="summary-row summary-total">
            <span class="summary-label">Total</span>
            <span class="summary-value">₱{{ formatPrice(total) }}</span>
          </div>

          <!-- Payment Method Section -->
          <div class="payment-section">
            <h4 class="payment-title">Select Payment Method</h4>

            <div class="payment-method">
              <div
                class="payment-option"
                :class="{ selected: selectedPaymentMethod === 'cash' }"
                @click="selectPaymentMethod('cash')"
              >
                <input
                  type="radio"
                  name="payment_method"
                  id="payment-cash"
                  value="cash"
                  v-model="selectedPaymentMethod"
                />
                <label for="payment-cash">
                  <i class="fas fa-money-bill-wave"></i>
                  <span>Cash</span>
                </label>
              </div>

              <div
                class="payment-option"
                :class="{ selected: selectedPaymentMethod === 'gcash' }"
                @click="selectPaymentMethod('gcash')"
              >
                <input
                  type="radio"
                  name="payment_method"
                  id="payment-gcash"
                  value="gcash"
                  v-model="selectedPaymentMethod"
                />
                <label for="payment-gcash">
                  <i class="fas fa-mobile-alt"></i>
                  <span>GCash</span>
                </label>
              </div>
              <div
                class="payment-details"
                :class="{ active: selectedPaymentMethod === 'gcash' }"
              >
                <label for="gcash-phone">Phone Number</label>
                <input
                  type="tel"
                  id="gcash-phone"
                  v-model="gcashPhone"
                  placeholder="09XX XXX XXXX"
                  maxlength="11"
                />
              </div>

              <div
                class="payment-option"
                :class="{ selected: selectedPaymentMethod === 'bank' }"
                @click="selectPaymentMethod('bank')"
              >
                <input
                  type="radio"
                  name="payment_method"
                  id="payment-bank"
                  value="bank"
                  v-model="selectedPaymentMethod"
                />
                <label for="payment-bank">
                  <i class="fas fa-university"></i>
                  <span>Bank Transfer</span>
                </label>
              </div>
              <div
                class="payment-details"
                :class="{ active: selectedPaymentMethod === 'bank' }"
              >
                <label for="bank-account">Bank Account Number</label>
                <input
                  type="text"
                  id="bank-account"
                  v-model="bankAccount"
                  placeholder="Enter your bank account number"
                />
              </div>
            </div>
          </div>

          <button
            class="checkout-btn"
            :disabled="cartItems.length === 0 || isProcessing"
            @click="processCheckout"
          >
            Proceed to Checkout
          </button>
          <NuxtLink to="/Customer/Cdashboard" class="continue-shopping">
            Continue Shopping
          </NuxtLink>
        </div>

        <!-- Empty Cart Message -->
        <div v-if="cartItems.length === 0" class="empty-cart">
          <i class="fas fa-shopping-cart empty-cart-icon"></i>
          <h3>Your Cart is Empty</h3>
          <p>Looks like you haven't added anything to your cart yet.</p>
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
import { ref, computed, onMounted, watch } from 'vue'
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
const { user, isAuthenticated, logout } = useAuth()
const router = useRouter()
const { $axios } = useNuxtApp()

// State
const cartItems = ref([])
const selectedPaymentMethod = ref('')
const gcashPhone = ref('')
const bankAccount = ref('')
const isProcessing = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

// Computed properties
const subtotal = computed(() => {
  return cartItems.value.reduce(
    (total, item) => total + item.price * item.quantity,
    0
  )
})

const serviceFee = computed(() => {
  return subtotal.value * 0.05 // 5% service fee
})

const tax = computed(() => {
  return subtotal.value * 0.1 // 10% tax
})

const total = computed(() => {
  return subtotal.value + serviceFee.value + tax.value
})

// Load cart from localStorage
const loadCart = () => {
  if (process.client) {
    const cart = localStorage.getItem('cart')
    cartItems.value = cart ? JSON.parse(cart) : []
  }
}

// Save cart to localStorage
const saveCart = () => {
  if (process.client) {
    localStorage.setItem('cart', JSON.stringify(cartItems.value))
  }
}

// Initialize cart on mount
onMounted(() => {
  loadCart()
})

// Watch cart items and save to localStorage
watch(
  cartItems,
  () => {
    saveCart()
  },
  { deep: true }
)

// Cart methods
const increaseQuantity = (itemId) => {
  const item = cartItems.value.find((item) => item.id === itemId)
  if (item) {
    item.quantity++
  }
}

const decreaseQuantity = (itemId) => {
  const item = cartItems.value.find((item) => item.id === itemId)
  if (item && item.quantity > 1) {
    item.quantity--
  }
}

const updateQuantity = (itemId, quantity) => {
  const item = cartItems.value.find((item) => item.id === itemId)
  if (item) {
    const qty = parseInt(quantity)
    item.quantity = qty < 1 ? 1 : qty
  }
}

const removeItem = (itemId) => {
  cartItems.value = cartItems.value.filter((item) => item.id !== itemId)
  showMessage('Item removed from cart!', 'success')
}

// Payment method selection
const selectPaymentMethod = (method) => {
  selectedPaymentMethod.value = method
}

// Validate payment method
const validatePaymentMethod = () => {
  if (!selectedPaymentMethod.value) {
    showMessage('Please select a payment method', 'error')
    return false
  }

  if (selectedPaymentMethod.value === 'gcash') {
    if (!gcashPhone.value.trim()) {
      showMessage('Please enter your GCash phone number', 'error')
      return false
    }
    if (!/^09\d{9}$/.test(gcashPhone.value.trim())) {
      showMessage(
        'Please enter a valid GCash phone number (09XXXXXXXXX)',
        'error'
      )
      return false
    }
  }

  if (selectedPaymentMethod.value === 'bank') {
    if (!bankAccount.value.trim()) {
      showMessage('Please enter your bank account number', 'error')
      return false
    }
    if (bankAccount.value.trim().length < 8) {
      showMessage('Please enter a valid bank account number', 'error')
      return false
    }
  }

  return true
}

// Get payment method data
const getPaymentMethodData = () => {
  if (!selectedPaymentMethod.value) return null

  const paymentData = {
    method: selectedPaymentMethod.value,
  }

  if (selectedPaymentMethod.value === 'gcash') {
    paymentData.phone_number = gcashPhone.value.trim()
  } else if (selectedPaymentMethod.value === 'bank') {
    paymentData.bank_account = bankAccount.value.trim()
  }

  return paymentData
}

// Process checkout
const processCheckout = async () => {
  try {
    // Check if user is authenticated
    if (!isAuthenticated.value) {
      showMessage('Please log in to proceed with checkout.', 'error')
      setTimeout(() => {
        router.push('/Auth/login')
      }, 2000)
      return
    }

    // Check if cart is empty
    if (cartItems.value.length === 0) {
      showMessage('Your cart is empty!', 'error')
      return
    }

    // Validate payment method
    if (!validatePaymentMethod()) {
      return
    }

    isProcessing.value = true

    // Get payment method data
    const paymentMethodData = getPaymentMethodData()

    // Prepare payment details (exclude the method itself)
    const paymentDetails = {}
    if (paymentMethodData.phone_number) {
      paymentDetails.phone_number = paymentMethodData.phone_number
    }
    if (paymentMethodData.bank_account) {
      paymentDetails.bank_account = paymentMethodData.bank_account
    }

    // Prepare checkout data
    const checkoutData = {
      cart_data: cartItems.value,
      order_summary: {
        subtotal: subtotal.value,
        service_fee: serviceFee.value, // Changed from serviceFee to service_fee
        tax: tax.value,
        total: total.value,
      },
      payment_method: paymentMethodData.method, // Send only the method string
      payment_details: Object.keys(paymentDetails).length > 0 ? paymentDetails : null,
    }

    // Debug: Log checkout data
    console.log('Sending checkout data:', checkoutData)

    // Call checkout endpoint
    const response = await $axios.post('/checkout', checkoutData)
    
    if (response.data.success) {
      // Clear cart
      cartItems.value = []
      if (process.client) {
        localStorage.removeItem('cart')
      }
      
      isProcessing.value = false
      
      // Redirect to confirmation page with order details
      const orderNumber = response.data.order?.order_number || response.data.data?.order_number || ''
      router.push({
        path: '/Customer/Cconfirmation',
        query: {
          order_number: orderNumber,
          subtotal: checkoutData.order_summary.subtotal,
          serviceFee: serviceFee.value, // Use the original ref value
          tax: checkoutData.order_summary.tax,
          total: checkoutData.order_summary.total,
        }
      })
    } else {
      throw new Error(response.data.message || 'Failed to process order')
    }
  } catch (error) {
    console.error('Checkout error:', error)
    
    // Handle validation errors
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      const errorMessages = Object.values(errors).flat().join(', ')
      showMessage('Validation failed: ' + errorMessages, 'error')
    } else {
      showMessage(
        'Failed to process order: ' +
          (error.response?.data?.message || error.message),
        'error'
      )
    }
    
    isProcessing.value = false
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

// Show message
const showMessage = (message, type = 'success') => {
  if (type === 'success') {
    successMessage.value = message
    errorMessage.value = ''
  } else {
    errorMessage.value = message
    successMessage.value = ''
  }

  setTimeout(() => {
    successMessage.value = ''
    errorMessage.value = ''
  }, 5000)
}

// Format price
const formatPrice = (price) => {
  return parseFloat(price).toFixed(2)
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

/* Loading overlay */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  flex-direction: column;
}

.loading-spinner {
  width: 50px;
  height: 50px;
  border: 5px solid var(--main-color);
  border-top: 5px solid transparent;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.loading-text {
  color: var(--white);
  font-size: 1.8rem;
  margin-top: 1rem;
}

/* Cart Section Styles */
.cart-section {
  min-height: 70vh;
  padding: 2rem;
}

.cart-container {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 3rem;
  margin-top: 2rem;
}

.cart-container.empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  grid-template-columns: none;
}

@media (max-width: 768px) {
  .cart-container {
    grid-template-columns: 1fr;
  }
}

.cart-items {
  background-color: var(--black);
  border: var(--border);
  border-radius: 1rem;
  padding: 2rem;
}

.cart-item {
  display: grid;
  grid-template-columns: 1fr 2fr 1fr 1fr 1fr;
  gap: 1.5rem;
  align-items: center;
  padding: 1.5rem 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

@media (max-width: 768px) {
  .cart-item {
    grid-template-columns: 1fr 2fr;
    grid-template-rows: auto auto auto;
    gap: 1rem;
  }

  .item-image {
    grid-column: 1;
    grid-row: 1 / 3;
  }

  .item-details {
    grid-column: 2;
    grid-row: 1;
  }

  .item-quantity {
    grid-column: 1;
    grid-row: 3;
  }

  .item-price {
    grid-column: 2;
    grid-row: 2;
  }

  .item-remove {
    grid-column: 2;
    grid-row: 3;
    justify-self: end;
  }
}

.item-image {
  width: 80px;
  height: 80px;
  border-radius: 0.5rem;
  overflow: hidden;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-details h3 {
  font-size: 1.8rem;
  color: var(--white);
  margin-bottom: 0.5rem;
}

.item-details p {
  font-size: 1.4rem;
  color: var(--light-color);
}

.item-quantity {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.quantity-btn {
  width: 3rem;
  height: 3rem;
  background-color: var(--main-color);
  color: var(--black);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 1.6rem;
  transition: all 0.3s;
}

.quantity-btn:hover {
  background-color: #c19a6b;
}

.quantity-input {
  width: 5rem;
  text-align: center;
  font-size: 1.6rem;
  background: transparent;
  color: var(--white);
  border: 1px solid var(--light-color);
  border-radius: 0.5rem;
  padding: 0.5rem;
}

.item-price {
  font-size: 1.8rem;
  color: var(--main-color);
  font-weight: bold;
}

.item-remove {
  color: var(--red);
  font-size: 2rem;
  cursor: pointer;
  transition: all 0.3s;
}

.item-remove:hover {
  color: #c0392b;
}

.cart-summary {
  background-color: var(--black);
  border: var(--border);
  border-radius: 1rem;
  padding: 2rem;
  height: fit-content;
}

.cart-container.empty .cart-summary {
  max-width: 500px;
  width: 100%;
}

.summary-title {
  font-size: 2.2rem;
  color: var(--white);
  margin-bottom: 1.5rem;
  text-align: center;
  border-bottom: 1px solid var(--light-color);
  padding-bottom: 1rem;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.summary-label {
  font-size: 1.6rem;
  color: var(--white);
}

.summary-value {
  font-size: 1.6rem;
  color: var(--main-color);
}

.summary-total {
  font-size: 2rem;
  font-weight: bold;
  margin-top: 1rem;
}

.checkout-btn {
  width: 100%;
  padding: 1.5rem;
  background-color: var(--main-color);
  color: var(--black);
  font-size: 1.8rem;
  border-radius: 0.5rem;
  cursor: pointer;
  margin-top: 2rem;
  transition: all 0.3s;
  text-align: center;
  display: block;
}

.checkout-btn:hover:not(:disabled) {
  background-color: #c19a6b;
  letter-spacing: 0.1rem;
}

.checkout-btn:disabled {
  background-color: var(--light-color);
  cursor: not-allowed;
  letter-spacing: normal;
}

.continue-shopping {
  display: block;
  text-align: center;
  margin-top: 1.5rem;
  font-size: 1.6rem;
  color: var(--main-color);
  transition: all 0.3s;
}

.continue-shopping:hover {
  color: var(--white);
  text-decoration: underline;
}

/* Payment Method Styles */
.payment-section {
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid var(--light-color);
}

.payment-title {
  font-size: 1.8rem;
  color: var(--white);
  margin-bottom: 1.5rem;
  text-align: center;
}

.payment-method {
  margin-bottom: 1.5rem;
}

.payment-option {
  display: flex;
  align-items: center;
  padding: 1rem;
  margin-bottom: 1rem;
  background-color: rgba(255, 255, 255, 0.05);
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.3s;
  border: 2px solid transparent;
}

.payment-option:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.payment-option.selected {
  border-color: var(--main-color);
  background-color: rgba(211, 173, 127, 0.1);
}

.payment-option input[type='radio'] {
  width: 2rem;
  height: 2rem;
  margin-right: 1rem;
  cursor: pointer;
  accent-color: var(--main-color);
}

.payment-option label {
  font-size: 1.6rem;
  color: var(--white);
  cursor: pointer;
  flex: 1;
  display: flex;
  align-items: center;
  gap: 0.8rem;
}

.payment-option i {
  font-size: 2rem;
  color: var(--main-color);
}

.payment-details {
  display: none;
  margin-top: 1rem;
  padding: 1.5rem;
  background-color: rgba(255, 255, 255, 0.05);
  border-radius: 0.5rem;
  border-left: 3px solid var(--main-color);
}

.payment-details.active {
  display: block;
}

.payment-details label {
  display: block;
  font-size: 1.4rem;
  color: var(--white);
  margin-bottom: 0.5rem;
}

.payment-details input {
  width: 100%;
  padding: 1rem;
  font-size: 1.6rem;
  background-color: rgba(255, 255, 255, 0.1);
  border: 1px solid var(--light-color);
  border-radius: 0.5rem;
  color: var(--white);
  outline: none;
  transition: all 0.3s;
}

.payment-details input:focus {
  border-color: var(--main-color);
  background-color: rgba(255, 255, 255, 0.15);
}

.payment-details input::placeholder {
  color: var(--light-color);
}

.empty-cart {
  text-align: center;
  padding: 4rem 2rem;
  grid-column: 1 / -1;
}

.cart-container.empty .empty-cart {
  margin-top: 3rem;
  max-width: 600px;
  width: 100%;
}

.empty-cart-icon {
  font-size: 8rem;
  color: var(--light-color);
  margin-bottom: 2rem;
}

.empty-cart h3 {
  font-size: 2.5rem;
  color: var(--white);
  margin-bottom: 1.5rem;
}

.empty-cart p {
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
  html {
    font-size: 50%;
  }

  .title {
    font-size: 3rem;
  }
}
</style>

