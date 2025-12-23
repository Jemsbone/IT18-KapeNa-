/**
 * Authentication Plugin
 * Handles CSRF cookie retrieval for Sanctum authentication
 * Runs only on client-side
 */
export default defineNuxtPlugin(async () => {
  const config = useRuntimeConfig()
  const { $axios } = useNuxtApp()
  
  // Get CSRF cookie from Laravel Sanctum
  // This must be called before making authenticated requests
  // The base URL should point to Laravel root, not /api
  const apiBase = config.public.apiBase || 'http://localhost:8000/api'
  const laravelBase = apiBase.replace('/api', '')
  
  try {
    await $axios.get('/sanctum/csrf-cookie', {
      baseURL: laravelBase
    })
  } catch (error) {
    console.warn('Failed to get CSRF cookie:', error)
  }
})

