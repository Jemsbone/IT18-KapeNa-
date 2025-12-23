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
        <NuxtLink to="/Admin/aproduct" class="nav-item active">
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
        <h1>Edit Product</h1>
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

      <!-- Loading State -->
      <div v-if="isLoading && !form.name" class="loading-state">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Loading product...</p>
      </div>

      <!-- Edit Product Section -->
      <div v-else class="edit-product-section">
          <form @submit.prevent="handleSubmit" class="product-form">
            <div class="form-group">
              <label for="name">Product Name</label>
              <input
                v-model="form.name"
                type="text"
                name="name"
                id="name"
                required
              />
              <span v-if="errors.name" class="error-text">{{ errors.name }}</span>
            </div>

            <div class="form-group">
              <label for="price">Product Price ($)</label>
              <input
                v-model.number="form.price"
                type="number"
                name="price"
                id="price"
                step="0.01"
                required
              />
              <span v-if="errors.price" class="error-text">{{ errors.price }}</span>
            </div>

            <div class="form-group">
              <label for="stock">Product Stock</label>
              <input
                v-model.number="form.stock"
                type="number"
                name="stock"
                id="stock"
                min="0"
                required
              />
              <span v-if="errors.stock" class="error-text">{{ errors.stock }}</span>
            </div>

            <div class="form-group">
              <label for="category">Category</label>
              <select v-model="form.category" name="category" id="category" required>
                <option value="">select category --</option>
                <option value="coffee">Coffee</option>
                <option value="main-dish">Main Dish</option>
                <option value="drinks">Drinks</option>
                <option value="desserts">Desserts</option>
              </select>
              <span v-if="errors.category" class="error-text">{{ errors.category }}</span>
            </div>

            <div class="form-group">
              <label for="image">Product Image (leave empty to keep current)</label>
              <div class="current-image">
                <p class="current-image-label">Current Image:</p>
                <img :src="currentImageUrl" :alt="form.name" />
              </div>
              <input
                type="file"
                name="image"
                id="image"
                accept="image/*"
                class="file-input"
                @change="handleImageChange"
              />
              <span v-if="errors.image" class="error-text">{{ errors.image }}</span>
            </div>

            <div class="button-group">
              <button type="submit" class="submit-btn" :disabled="isLoading">
                <span v-if="isLoading">
                  <i class="fas fa-spinner fa-spin"></i> Updating...
                </span>
                <span v-else>Update Product</span>
              </button>
              <NuxtLink to="/Admin/aproduct" class="cancel-btn">Cancel</NuxtLink>
            </div>
          </form>
      </div>
    </main>
  </div>
</template>

<script setup>
// Imports
import { reactive, ref, onMounted } from 'vue'
import { useHead } from '#imports'
import { useRouter, useRoute } from 'vue-router'
import { useNuxtApp } from '#app'
import useApi from '@/utils/api'

// Page Head Configuration
useHead({
  title: 'Edit Product - Kape Na!',
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
const route = useRoute()
const { $axios } = useNuxtApp()

// Reactive Data
const productId = ref(null)
const isLoading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const form = reactive({
  name: '',
  price: 0,
  stock: 0,
  category: '',
  image: null,
})

const errors = reactive({})
const currentImageUrl = ref('/placeholder-product.jpg')
const originalImageUrl = ref('')

// Load product data
const loadProduct = async () => {
  try {
    isLoading.value = true
    const api = useApi()
    const response = await api.getProduct(productId.value)
    
    // Get product data from response
    const product = response.data
    
    // Populate form
    form.name = product.name || product.product_name
    form.price = product.price || product.product_price
    form.stock = product.stock || product.product_stock || 0
    form.category = product.category || product.product_category
    
    // Set image URL
    let imageUrl = product.image || product.product_image
    if (imageUrl) {
      if (imageUrl.startsWith('http://') || imageUrl.startsWith('https://')) {
        currentImageUrl.value = imageUrl
        originalImageUrl.value = imageUrl
      } else {
        const imagePath = imageUrl.startsWith('/') ? imageUrl.substring(1) : imageUrl
        currentImageUrl.value = `http://localhost:8000/storage/${imagePath}`
        originalImageUrl.value = currentImageUrl.value
      }
    }
    
    console.log('✅ Product loaded:', form)
  } catch (error) {
    console.error('❌ Error loading product:', error)
    errorMessage.value = 'Failed to load product. Redirecting...'
    setTimeout(() => {
      router.push('/Admin/aproduct')
    }, 2000)
  } finally {
    isLoading.value = false
  }
}

// Methods
const handleImageChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.image = file
    const reader = new FileReader()
    reader.onload = (e) => {
      currentImageUrl.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const handleSubmit = async () => {
  try {
    isLoading.value = true
    const api = useApi()
    
    // Create FormData for multipart/form-data submission
    const formData = new FormData()
    formData.append('name', form.name)
    formData.append('price', parseFloat(form.price))
    formData.append('stock', parseInt(form.stock))
    formData.append('category', form.category)
    
    // Only append image if a new one was selected
    if (form.image instanceof File) {
      formData.append('image', form.image)
    }
    
    // Laravel requires _method for PUT with FormData
    formData.append('_method', 'PUT')
    
    // Use POST with _method=PUT for FormData (Laravel convention)
    await $axios.post(`/products/${productId.value}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    
    successMessage.value = 'Product updated successfully!'
    
    // Redirect to products page after 1.5 seconds
    setTimeout(() => {
      router.push('/Admin/aproduct')
    }, 1500)
  } catch (error) {
    console.error('❌ Error updating product:', error)
    console.error('Error response:', error.response?.data)
    
    // Show detailed validation errors if available
    if (error.response?.data?.errors) {
      const errorsList = error.response.data.errors
      const errorMessages = Object.keys(errorsList).map(key => {
        return `${key}: ${errorsList[key].join(', ')}`
      }).join(' | ')
      errorMessage.value = `Validation failed: ${errorMessages}`
    } else {
      errorMessage.value = error.response?.data?.message || 'Failed to update product. Please try again.'
    }
    
    setTimeout(() => {
      errorMessage.value = ''
    }, 5000)
  } finally {
    isLoading.value = false
  }
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

// Initialize on mount
onMounted(() => {
  // Get product ID from query params
  productId.value = route.query.id
  
  if (!productId.value) {
    errorMessage.value = 'No product ID provided. Redirecting...'
    setTimeout(() => {
      router.push('/Admin/aproduct')
    }, 2000)
    return
  }
  
  loadProduct()
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
  display: flex;
  flex-direction: column;
}

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
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.alert i {
  font-size: 1.2rem;
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

/* Loading State */
.loading-state {
  text-align: center;
  padding: 3rem;
  color: var(--light-color);
  background: var(--white);
  border-radius: 0.8rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.loading-state i {
  font-size: 3rem;
  color: var(--main-color);
  margin-bottom: 1rem;
  animation: spin 1s linear infinite;
}

.loading-state p {
  font-size: 1.2rem;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Edit Product Section */
.edit-product-section {
  background: var(--white);
  padding: 2.5rem;
  border-radius: 0.8rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  max-width: 600px;
  width: 100%;
  margin: 0 auto;
}

.product-form {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-size: 1.1rem;
  color: var(--black);
  font-weight: 500;
}

.product-form input,
.product-form select {
  padding: 0.8rem;
  font-size: 1.1rem;
  border: 1px solid #ddd;
  border-radius: 0.5rem;
  background: #f9f9f9;
}

.product-form input:focus,
.product-form select:focus {
  border-color: var(--admin-color);
  background: var(--white);
}

.current-image {
  margin-top: 0.5rem;
}

.current-image-label {
  font-size: 1rem;
  color: var(--light-color);
  margin-bottom: 0.5rem;
}

.current-image img {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 0.5rem;
  border: 2px solid var(--admin-color);
}

.file-input {
  padding: 0.5rem;
  cursor: pointer;
}

.error-text {
  color: #e74c3c;
  font-size: 1rem;
}

.button-group {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.submit-btn,
.cancel-btn {
  flex: 1;
  padding: 0.8rem 2rem;
  font-size: 1.2rem;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.3s;
  border: none;
  text-align: center;
}

.submit-btn {
  background: #5856d6;
  color: var(--white);
}

.submit-btn:hover:not(:disabled) {
  background: #4745b8;
}

.submit-btn:disabled {
  background: #9a99db;
  cursor: not-allowed;
  opacity: 0.7;
}

.submit-btn i {
  margin-right: 0.5rem;
}

.cancel-btn {
  background: #e74c3c;
  color: var(--white);
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
}

.cancel-btn:hover {
  background: #c0392b;
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

  .main-content {
    padding: 1rem;
  }

  .edit-product-section {
    padding: 1.5rem;
  }

  .button-group {
    flex-direction: column;
  }
}
</style>
