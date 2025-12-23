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
        <h1>Products</h1>
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

        <!-- Add Product Section -->
        <div class="add-product-section">
          <h2>Add Product</h2>
          <form @submit.prevent="handleAddProduct" class="product-form">
            <input
              v-model="newProduct.name"
              type="text"
              name="name"
              placeholder="enter product name"
              required
            />
            <input
              v-model.number="newProduct.price"
              type="number"
              name="price"
              placeholder="enter product price"
              step="0.01"
              required
            />
            <input
              v-model.number="newProduct.stock"
              type="number"
              name="stock"
              placeholder="enter product stock"
              min="0"
              required
            />
            <select v-model="newProduct.category" name="category" required>
              <option value="">select category --</option>
              <option value="coffee">Coffee</option>
              <option value="main-dish">Main Dish</option>
              <option value="drinks">Drinks</option>
              <option value="desserts">Desserts</option>
            </select>
            <input type="file" name="image" accept="image/*" class="file-input" @change="handleImageChange" required>
            <small style="color: var(--light-color); font-size: 0.9rem; margin-top: -0.5rem; display: block;">
              Accepts all image formats (max 10MB)
            </small>
            <button type="submit" class="submit-btn">Add Product</button>
          </form>
        </div>

      <!-- Product Details Section -->
      <h2 class="section-title">Product Details</h2>

      <!-- Search Bar -->
      <div class="search-container">
          <input
            v-model="searchTerm"
            type="text"
            class="search-input"
            placeholder="product name"
          />
          <button class="search-btn" @click="noop">search</button>
        </div>

        <div class="table-container">
          <!-- Loading State -->
          <div v-if="isLoading" class="loading-state">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Loading products...</p>
          </div>
          <table v-else>
            <thead>
              <tr>
                <th>ID</th>
                <th>Photo</th>
                <th>name</th>
                <th>price</th>
                <th>stock</th>
                <th>category</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="product in filteredProducts"
                :key="product.id"
                :class="getStockClass(product.stock)"
              >
                <td>{{ product.id }}</td>
                <td>
                  <img
                    :src="getProductImage(product)"
                    :alt="product.name"
                    class="product-image"
                    @error="handleImageError($event, product)"
                    @load="handleImageLoad($event, product)"
                  />
                </td>
                <td>{{ product.name }}</td>
                <td>₱{{ formatPrice(product.price) }}</td>
                <td>
                  <div class="stock-info">
                    <span>{{ product.stock }}</span>
                    <span :class="getStockStatusClass(product.stock)">
                      {{ getStockStatus(product.stock) }}
                    </span>
                  </div>
                </td>
                <td>{{ product.category }}</td>
                <td>
                  <div class="action-buttons">
                    <NuxtLink
                      :to="`/Admin/aproduct_edit?id=${product.id}`"
                      class="btn-update"
                    >
                      Edit
                    </NuxtLink>
                    <button
                      type="button"
                      class="btn-delete"
                      @click="deleteProduct(product.id)"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!filteredProducts.length">
                <td colspan="7" class="empty-row">
                  No products found. Add your first product above!
                </td>
              </tr>
            </tbody>
          </table>
      </div>
    </main>
  </div>
</template>

<script setup>
// Imports
import { computed, reactive, ref, onMounted } from 'vue'
import { useHead } from '#imports'
import { useRouter } from 'vue-router'
import { useNuxtApp } from '#app'
import useApi from '@/utils/api'

// Page Head Configuration
useHead({
  title: 'Admin Products - Kape Na!',
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

const newProduct = reactive({
  name: '',
  price: 0,
  stock: 0,
  category: '',
  image: null,
})

const products = ref([])

const filteredProducts = computed(() => {
  const term = searchTerm.value.trim().toLowerCase()
  if (!term) return products.value
  return products.value.filter((p) =>
    p.name.toLowerCase().includes(term)
  )
})

const getStockClass = (stock) => {
  if (stock <= 10) return 'critical-stock'
  if (stock <= 20) return 'low-stock'
  return ''
}

const getStockStatus = (stock) => {
  if (stock <= 10) return 'CRITICAL STOCK'
  if (stock <= 20) return 'LOW STOCK'
  return 'IN STOCK'
}

const getStockStatusClass = (stock) => {
  if (stock <= 10) return 'stock-status critical'
  if (stock <= 20) return 'stock-status low'
  return 'stock-status in'
}

const formatPrice = (value) =>
  Number(value || 0)
    .toFixed(2)
    .replace(/\d(?=(\d{3})+\.)/g, '$&,')

// Load products from API
const loadProducts = async () => {
  isLoading.value = true
  try {
    const api = useApi()
    const response = await api.getProducts()
    
    products.value = response.data.map((product) => {
      // Prefer the full URL from API, fallback to constructing it
      let imageUrl = product.image || product.product_image || null
      
      // If we have debug info, use it
      if (product._debug) {
        if (product._debug.image_url) {
          imageUrl = product._debug.image_url
        }
      }
      
      return {
        id: product.id || product.product_id,
        name: product.name || product.product_name,
        price: product.price || product.product_price,
        stock: product.stock || product.product_stock || 0,
        category: product.category || product.product_category,
        image: imageUrl,
      }
    })
    
    // Debug: Log product images and test URLs
    // console.log('✅ Loaded products with images:', products.value.map(p => {
    //   const finalUrl = getProductImage(p)
    //   console.log(`🖼️ Product ${p.id} (${p.name}):`, {
    //     image: p.image,
    //     finalUrl: finalUrl
    //   })
    //   return { 
    //     id: p.id, 
    //     name: p.name, 
    //     image: p.image,
    //     finalUrl: finalUrl
    //   }
    // }))
  } catch (error) {
    // console.error('❌ Error loading products:', error)
    errorMessage.value = 'Failed to load products. Please refresh the page.'
    setTimeout(() => {
      errorMessage.value = ''
    }, 5000)
  } finally {
    isLoading.value = false
  }
}

// Get product image URL
const getProductImage = (product) => {
  if (!product || !product.image) {
    return 'http://localhost:8000/images/cart-img-1.png'
  }
  
  const baseUrl = 'http://localhost:8000'
  let imageUrl = product.image
  
  // If already a full URL, use it directly
  if (imageUrl.startsWith('http://') || imageUrl.startsWith('https://')) {
    return imageUrl
  }
  
  // If it's a relative path, construct the full URL
  // Remove leading slash if present to avoid double slashes
  const cleanPath = imageUrl.startsWith('/') ? imageUrl.substring(1) : imageUrl
  
  // Laravel storage: physical path is storage/app/public/* but web path is storage/*
  // If path starts with 'storage/', use it as is
  // Otherwise assume it needs 'storage/' prefix
  if (cleanPath.startsWith('storage/')) {
    return `${baseUrl}/${cleanPath}`
  } else {
    return `${baseUrl}/storage/${cleanPath}`
  }
}

// Handle image load errors
const handleImageError = (event, product) => {
  console.error('❌ Image failed to load for product', product.id, ':', {
    attemptedUrl: event.target.src,
    productName: product.name,
    originalImage: product.image
  })
  // Set a fallback image
  event.target.src = 'http://localhost:8000/images/cart-img-1.png'
}

// Handle successful image load
const handleImageLoad = (event, product) => {
  // Image loaded successfully - no need to log every time
}

const handleImageChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    newProduct.image = file
  }
}

const handleAddProduct = async () => {
  if (!newProduct.name || !newProduct.price || !newProduct.stock || !newProduct.category || !newProduct.image) {
    errorMessage.value = 'Please fill all fields'
    setTimeout(() => {
      errorMessage.value = ''
    }, 3000)
    return
  }

  try {
    const api = useApi()
    const formData = new FormData()
    formData.append('name', newProduct.name)
    formData.append('price', parseFloat(newProduct.price)) // Ensure it's a number
    formData.append('stock', parseInt(newProduct.stock)) // Ensure it's an integer
    formData.append('category', newProduct.category)
    formData.append('image', newProduct.image)

    await api.createProduct(formData)

    // Reset form
    newProduct.name = ''
    newProduct.price = 0
    newProduct.stock = 0
    newProduct.category = ''
    newProduct.image = null
    // Reset file input
    const fileInput = document.querySelector('input[type="file"]')
    console.log('fileInput',fileInput);
    
    if (fileInput) fileInput.value = ''

    successMessage.value = 'Product added successfully!'
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)

    // Reload products to show the new one
    await loadProducts()
  } catch (error) {
    // console.error('Error adding product:', error)
    // console.error('Error response:', error.response?.data)
    
    // Show detailed validation errors if available
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      const errorMessages = Object.keys(errors).map(key => {
        return `${key}: ${errors[key].join(', ')}`
      }).join(' | ')
      errorMessage.value = `Validation failed: ${errorMessages}`
    } else {
      errorMessage.value = error.response?.data?.message || 'Failed to add product. Please try again.'
    }
    
    setTimeout(() => {
      errorMessage.value = ''
    }, 7000)
  }
}

const deleteProduct = async (id) => {
  if (!confirm('Are you sure you want to delete this product?')) {
    return
  }

  try {
    const api = useApi()
    await api.deleteProduct(id)
    products.value = products.value.filter((p) => p.id !== id)
    successMessage.value = 'Product deleted successfully!'
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)
  } catch (error) {
    // console.error('Error deleting product:', error)
    errorMessage.value = 'Failed to delete product. Please try again.'
    setTimeout(() => {
      errorMessage.value = ''
    }, 5000)
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
    // console.error('Logout error:', error)
  } finally {
    localStorage.removeItem('admin_token')
    router.push('/Auth/admin_login')
  }
}

// No-op function for search button (search is reactive via computed)
const noop = () => {}

// Load products on mount
onMounted(() => {
  loadProducts()
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

.section-title {
  font-size: 1.8rem;
  font-weight: 600;
  color: var(--black);
  margin-bottom: 1rem;
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

/* Add Product Section */
.add-product-section {
  background: var(--white);
  padding: 2rem;
  border-radius: 0.8rem;
  margin-bottom: 2rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.add-product-section h2 {
  font-size: 1.8rem;
  margin-bottom: 1.5rem;
  color: var(--black);
  text-align: center;
}

.product-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.product-form input,
.product-form select {
  padding: 0.8rem;
  font-size: 1rem;
  border: 2px solid var(--main-color);
  border-radius: 0.5rem;
  background: var(--white);
}

.product-form input:focus,
.product-form select:focus {
  border-color: var(--main-color);
  background: var(--white);
}

.file-input {
  padding: 0.5rem;
  cursor: pointer;
}

.submit-btn {
  background: var(--main-color);
  color: var(--white);
  padding: 0.8rem 2rem;
  font-size: 1rem;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.3s;
  border: none;
}

.submit-btn:hover {
  background: #b8956a;
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
  background: var(--main-color);
  color: var(--white);
}

thead th {
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.95rem;
}

tbody tr {
  border-bottom: 1px solid #e0e0e0;
}

tbody tr:hover {
  background: #f5f5f5;
}

tbody tr.critical-stock {
  background: #ffebee;
}

tbody tr.low-stock {
  background: #fff3e0;
}

tbody td {
  padding: 1rem;
  font-size: 0.9rem;
  color: var(--black);
}

.stock-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.stock-status {
  display: inline-block;
  padding: 0.3rem 0.8rem;
  border-radius: 0.3rem;
  font-weight: 600;
  font-size: 0.85rem;
}

.stock-status.critical {
  background: var(--delete-btn);
  color: var(--white);
}

.stock-status.low {
  background: #ff9800;
  color: var(--white);
}

.stock-status.in {
  background: #4caf50;
  color: var(--white);
}

.product-image {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 0.5rem;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.btn-update {
  padding: 0.6rem 1.2rem;
  background: var(--update-btn);
  color: var(--black);
  border-radius: 0.3rem;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  transition: all 0.3s;
  text-decoration: none;
  display: inline-block;
}

.btn-update:hover {
  background: #4f46e5;
}

.btn-delete {
  padding: 0.6rem 1.2rem;
  background: var(--delete-btn);
  color: var(--black);
  border-radius: 0.3rem;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  transition: all 0.3s;
}

.btn-delete:hover {
  background: #c0392b;
}

.empty-row {
  text-align: center;
  padding: 2rem;
  color: var(--light-color);
}

.loading-state {
  text-align: center;
  padding: 3rem;
  color: var(--light-color);
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

  .action-buttons {
    flex-direction: column;
  }
}
</style>
