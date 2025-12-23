/**
 * API Service Utility
 * Provides reusable API methods for authentication and resources
 */
export default function useApi() {
  const { $axios } = useNuxtApp()

  return {
    // Authentication endpoints (must exist in Laravel API)
    login: (credentials) => $axios.post('/login', credentials),
    register: (userData) => $axios.post('/register', userData),
    logout: () => $axios.post('/logout'),
    getUser: () => $axios.get('/user'),
    
    // Product resource endpoints (map to Laravel coffee shop backend)
    getProducts: () => $axios.get('/products'),
    getProduct: (id) => $axios.get(`/products/${id}`),
    createProduct: (data) => $axios.post('/products', data),
    updateProduct: (id, data) => $axios.put(`/products/${id}`, data),
    deleteProduct: (id) => $axios.delete(`/products/${id}`),
  }
}

