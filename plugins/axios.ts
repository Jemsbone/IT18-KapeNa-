import axios from 'axios'

/**
 * Axios Plugin
 * Creates a pre-configured Axios instance and makes it available as `$axios`
 * in the Nuxt app (compatible with `useNuxtApp().$axios` usage).
 */
export default defineNuxtPlugin((nuxtApp) => {
  const config = useRuntimeConfig()

  const apiBase = config.public.apiBase || 'http://localhost:8000/api'

  console.log('[Axios Plugin] API Base URL:', apiBase)

  const instance = axios.create({
    baseURL: apiBase,
    withCredentials: true,
    timeout: 30000, // 30 second timeout
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
    // Explicitly map Laravel Sanctum's CSRF cookie + header
    xsrfCookieName: 'XSRF-TOKEN',
    xsrfHeaderName: 'X-XSRF-TOKEN',
  })

  // Add request interceptor for logging, FormData handling, and authentication
  instance.interceptors.request.use(
    (config) => {
      console.log(`[API Request] ${config.method?.toUpperCase()} ${config.url}`)
      
      // If data is FormData, remove Content-Type header to let browser set it with boundary
      if (config.data instanceof FormData) {
        delete config.headers['Content-Type']
      }
      
      // Add admin token if available (for admin routes)
      if (process.client) {
        const adminToken = localStorage.getItem('admin_token')
        if (adminToken) {
          config.headers['Authorization'] = `Bearer ${adminToken}`
        }
      }
      
      return config
    },
    (error) => {
      console.error('[API Request Error]', error)
      return Promise.reject(error)
    }
  )

  // Add response interceptor for better error handling
  instance.interceptors.response.use(
    (response) => {
      return response
    },
    (error) => {
      // Log detailed error information
      console.error('[API Error Details]', {
        code: error.code,
        message: error.message,
        response: error.response?.data,
        status: error.response?.status,
        url: error.config?.url,
        baseURL: error.config?.baseURL,
        fullError: error
      })

      // Handle network errors
      if (
        error.code === 'ECONNREFUSED' || 
        error.message === 'Network Error' ||
        error.message?.includes('Network Error') ||
        !error.response
      ) {
        console.error('[API Network Error]', {
          message: 'Cannot connect to API server',
          url: error.config?.url,
          baseURL: error.config?.baseURL,
          fullURL: `${error.config?.baseURL}${error.config?.url}`,
          suggestion: 'Make sure Laravel API server is running: php artisan serve'
        })
        
        // Test if server is reachable
        if (process.client) {
          fetch('http://localhost:8000', { method: 'GET', mode: 'no-cors' })
            .then(() => console.log('[Connection Test] Server is reachable'))
            .catch(() => console.error('[Connection Test] Server is NOT reachable'))
        }

        error.response = {
          data: {
            message: 'Cannot connect to API server. Please make sure the Laravel API server is running (php artisan serve)',
            networkError: true,
            details: {
              code: error.code,
              message: error.message,
              url: `${error.config?.baseURL}${error.config?.url}`
            }
          },
          status: 0
        }
      }
      return Promise.reject(error)
    }
  )

  // Provide on Nuxt app as `$axios`
  nuxtApp.provide('axios', instance)
})


