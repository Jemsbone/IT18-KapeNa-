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
            <NuxtLink to="/Customer/Cmenue" class="nav-link active">Menu</NuxtLink>
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

    <!-- Dashboard Welcome Section -->
    <section class="dashboard-welcome">
      <h2>CATEGORY</h2>
    </section>

    <!-- Loading State -->
    <div v-if="loading" class="loading-message">Loading products...</div>

    <!-- Dynamic Products Section - Grouped by Category -->
    <template v-if="!loading && groupedProducts.length > 0">
      <section
        v-for="categoryGroup in groupedProducts"
        :key="categoryGroup.category"
        class="products"
      >
        <h1 class="title">{{ categoryGroup.title }}</h1>
        <div class="products-container">
          <div
            v-for="product in categoryGroup.products"
            :key="product.id"
            class="product-box"
          >
            <img
              :src="getProductImage(product)"
              :alt="product.name"
              class="product-image"
            />
            <h3>{{ product.name }}</h3>
            <p class="price">₱{{ formatPrice(product.price) }}</p>
            <div class="quantity-controls">
              <div
                class="quantity-btn minus"
                @click="decreaseQuantity(product.id)"
              >
                -
              </div>
              <div class="quantity-display">
                {{ getQuantity(product.id) }}
              </div>
              <div
                class="quantity-btn plus"
                @click="increaseQuantity(product.id)"
              >
                +
              </div>
            </div>
            <button
              class="add-to-cart-btn"
              @click="addToCart(product)"
            >
              Add to Cart
            </button>
          </div>
        </div>
      </section>
    </template>

    <!-- Empty State -->
    <section v-if="!loading && groupedProducts.length === 0" class="products">
      <div class="empty-products">
        <i class="fas fa-shopping-basket empty-icon"></i>
        <p>No products available at the moment.</p>
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
import useApi from '@/utils/api'

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
const products = ref([])
const loading = ref(false)
const successMessage = ref('')
const productQuantities = ref({})
const cartCount = ref(0)

// Category titles mapping
const categoryTitles = {
  coffee: 'Coffee Products',
  'main-dish': 'Special Dishes Products',
  drinks: 'Drink Products',
  desserts: 'Dessert Products',
}

// Computed: Group products by category
const groupedProducts = computed(() => {
  const categories = ['coffee', 'main-dish', 'drinks', 'desserts']
  const grouped = []

  categories.forEach((category) => {
    const categoryProducts = products.value.filter(
      (product) => product.category === category
    )

    if (categoryProducts.length > 0) {
      grouped.push({
        category,
        title: categoryTitles[category] || `${category} Products`,
        products: categoryProducts,
      })
    }
  })

  return grouped
})

// Load products from API
const loadProducts = async () => {
  loading.value = true
  try {
    const api = useApi()
    const response = await api.getProducts()
    products.value = response.data.map(product => {
      const imageUrl = product.image || product.product_image || null
      return {
        id: product.id || product.product_id,
        name: product.name || product.product_name,
        price: product.price || product.product_price,
        image: imageUrl,
        category: product.category || product.product_category,
        stock: product.stock || product.product_stock || 0
      }
    })
    console.log('Customer Menu - Loaded products:', products.value.length)
  } catch (error) {
    console.error('Error loading products:', error)
    products.value = []
  } finally {
    loading.value = false
  }
}

// Get product image URL
const getProductImage = (product) => {
  if (!product || !product.image) {
    return 'http://localhost:8000/images/product-1.png'
  }
  
  // If already a full URL, return as is
  if (product.image.startsWith('http://') || product.image.startsWith('https://')) {
    return product.image
  }
  
  // If it's a storage path, construct the full URL
  // Remove leading slash if present
  const imagePath = product.image.startsWith('/') ? product.image.substring(1) : product.image
  const finalUrl = `http://localhost:8000/storage/${imagePath}`
  return finalUrl
}

// Get quantity for a product
const getQuantity = (productId) => {
  return productQuantities.value[productId] || 1
}

// Increase quantity
const increaseQuantity = (productId) => {
  const currentQty = productQuantities.value[productId] || 1
  productQuantities.value[productId] = currentQty + 1
}

// Decrease quantity
const decreaseQuantity = (productId) => {
  const currentQty = productQuantities.value[productId] || 1
  if (currentQty > 1) {
    productQuantities.value[productId] = currentQty - 1
  }
}

// Add to cart
const addToCart = (product) => {
  if (process.client) {
    const quantity = getQuantity(product.id)
    const cart = JSON.parse(localStorage.getItem('cart') || '[]')

    const existingItem = cart.find((item) => item.id === product.id)

    if (existingItem) {
      existingItem.quantity += quantity
    } else {
      cart.push({
        id: product.id,
        name: product.name,
        price: product.price,
        image: getProductImage(product),
        quantity: quantity,
      })
    }

    localStorage.setItem('cart', JSON.stringify(cart))
    updateCartCount()
    showSuccessMessage(`Added ${quantity} x ${product.name} to cart!`)

    // Reset quantity to 1
    productQuantities.value[product.id] = 1
  }
}

// Update cart count
const updateCartCount = () => {
  if (process.client) {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]')
    cartCount.value = cart.reduce((total, item) => total + item.quantity, 0)
  }
}

// Show success message
const showSuccessMessage = (message) => {
  successMessage.value = message
  setTimeout(() => {
    successMessage.value = ''
  }, 3000)
}

// Format price
const formatPrice = (price) => {
  return parseFloat(price).toFixed(2)
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

// Initialize on mount
onMounted(() => {
  loadProducts()
  updateCartCount()

  // Update cart count when localStorage changes (from other tabs/windows)
  if (process.client) {
    window.addEventListener('storage', () => {
      updateCartCount()
    })
  }
})

// Watch for cart changes in the same window
if (process.client) {
  setInterval(() => {
    updateCartCount()
  }, 1000)
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

/* Success message styling */
.success-message {
  color: #2ecc71;
  font-size: 1.6rem;
  text-align: center;
  padding: 1rem;
  background: rgba(46, 204, 113, 0.1);
  border-radius: 0.5rem;
  margin: 1rem auto;
  max-width: 1200px;
  border: 1px solid #2ecc71;
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

/* Cart Count Badge */
.cart-count {
  position: absolute;
  top: -8px;
  right: -8px;
  background-color: var(--main-color);
  color: var(--black);
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  font-weight: bold;
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

/* Dashboard specific styles */
.dashboard-welcome {
  text-align: center;
  margin: 2rem auto;
  padding: 2rem;
  background-color: var(--black);
  border-radius: 1rem;
  max-width: 1200px;
  width: 90%;
}

.dashboard-welcome h2 {
  font-size: 3rem;
  margin-bottom: 1rem;
  color: var(--main-color);
}

.dashboard-welcome p {
  font-size: 1.8rem;
  color: var(--light-color);
}

.loading-message {
  text-align: center;
  padding: 2rem;
  font-size: 1.6rem;
  color: var(--light-color);
}

/* Coffee Products Styles */
.products {
  padding: 2rem;
  margin: 2rem auto;
  max-width: 1200px;
}

.products-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  align-items: stretch;
}

.product-box {
  background: var(--black);
  border: var(--border);
  padding: 2rem;
  text-align: center;
  transition: transform 0.3s, background-color 0.3s;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  height: 100%;
  min-height: 350px;
}

.product-box:hover {
  transform: translateY(-10px);
  background-color: var(--white);
}

.product-box:hover h3,
.product-box:hover .price,
.product-box:hover .quantity-display {
  color: var(--black);
}

.product-box .product-image {
  width: 200px;
  height: 200px;
  object-fit: cover;
  border-radius: 0.5rem;
  margin: 0 auto 1.5rem auto;
  border: 2px solid var(--main-color);
  display: block;
}

.product-box h3 {
  font-size: 2.5rem;
  color: var(--white);
  text-transform: uppercase;
  margin-bottom: 1rem;
}

.product-box .price {
  font-size: 2.2rem;
  color: var(--main-color);
  margin-bottom: 1rem;
}

.quantity-controls {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 1rem 0;
}

.quantity-btn {
  background-color: var(--main-color);
  color: var(--black);
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

.quantity-btn:hover {
  background-color: var(--yellow);
  color: var(--white);
}

.quantity-display {
  font-size: 2rem;
  color: var(--white);
  background: var(--yellow);
  padding: 0.5rem 1.5rem;
  border-radius: 0.5rem;
  margin: 0 1rem;
  min-width: 5rem;
  text-align: center;
}

.add-to-cart-btn {
  background-color: var(--main-color);
  color: var(--black);
  font-size: 1.8rem;
  padding: 1rem 2rem;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 1rem;
  width: 100%;
}

.add-to-cart-btn:hover {
  background-color: var(--yellow);
  color: var(--white);
  letter-spacing: 0.1rem;
}

.empty-products {
  width: 100%;
  text-align: center;
  padding: 5rem 0;
}

.empty-products .empty-icon {
  font-size: 5rem;
  margin-bottom: 2rem;
  display: block;
  color: var(--main-color);
}

.empty-products p {
  font-size: 2rem;
  color: var(--light-color);
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

  .products-container {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  }
}

@media (max-width: 450px) {
  html {
    font-size: 50%;
  }

  .title {
    font-size: 3rem;
  }

  .products-container {
    grid-template-columns: 1fr;
  }
}
</style>

