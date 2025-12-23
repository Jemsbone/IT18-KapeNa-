// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: true },

  // We use a custom Axios plugin instead of the legacy @nuxtjs/axios module

  devServer: {
    port: 3000
  },

  runtimeConfig: {
    public: {
      // Base URL for Laravel API
      apiBase: process.env.API_URL || 'http://localhost:8000/api'
    }
  }
})

