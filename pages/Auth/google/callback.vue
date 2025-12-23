<template>
  <div class="oauth-callback">
    <div class="loader-container">
      <div class="loader"></div>
      <p class="loading-text">
        {{ statusMessage }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from '#imports'
import { useAuth } from '@/composables/useAuth'

const router = useRouter()
const route = useRoute()
const { fetchUser } = useAuth()
const statusMessage = ref('Completing Google login...')

onMounted(async () => {
  try {
    // Check for error in URL
    if (route.query.error) {
      statusMessage.value = 'Login failed. Redirecting...'
      
      // Wait a moment then redirect to login with error
      setTimeout(() => {
        router.push({
          path: '/Auth/login',
          query: { error: route.query.error }
        })
      }, 2000)
      return
    }

    // Get success status and message from URL
    const success = route.query.success
    const message = route.query.message || 'Successfully logged in with Google!'

    if (!success) {
      throw new Error('OAuth callback did not indicate success')
    }

    // Fetch the authenticated user (session cookie handles authentication)
    statusMessage.value = 'Fetching your profile...'
    const userData = await fetchUser()
    
    if (!userData) {
      throw new Error('Failed to fetch user data after OAuth')
    }

    // Redirect to dashboard with success message
    statusMessage.value = 'Success! Redirecting to dashboard...'
    setTimeout(() => {
      router.push({
        path: '/Customer/Cdashboard',
        query: { success: message }
      })
    }, 1000)
  } catch (error) {
    console.error('OAuth callback error:', error)
    statusMessage.value = 'Login failed. Redirecting...'
    
    // Redirect to login with error after a delay
    setTimeout(() => {
      router.push({
        path: '/Auth/login',
        query: { error: 'Failed to complete Google login. Please try again.' }
      })
    }, 2000)
  }
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Anton&display=swap');

:root {
  --yellow: black;
  --red: #e74c3c;
  --white: #fff;
  --black: #222;
  --light-color: #777;
  --main-color: #d3ad7f;
  --bg: #856731bd;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Anton', sans-serif;
}

.oauth-callback {
  min-height: 100vh;
  background: var(--bg);
  display: flex;
  align-items: center;
  justify-content: center;
}

.loader-container {
  text-align: center;
  padding: 3rem;
  background: var(--black);
  border-radius: 1rem;
  box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.3);
}

.loader {
  width: 50px;
  height: 50px;
  border: 5px solid rgba(211, 173, 127, 0.2);
  border-top-color: var(--main-color);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 2rem;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.loading-text {
  color: var(--white);
  font-size: 2rem;
  letter-spacing: 0.5px;
}
</style>

