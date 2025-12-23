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
            <NuxtLink to="/Customer/Cdashboard" class="nav-link active">Home</NuxtLink>
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

    <!-- Success Messages -->
    <div v-if="successMessage" class="success-message">
      {{ successMessage }}
    </div>

    <!-- showcase area -->
    <section class="hero">
      <div class="swiper hero-slider">
        <div class="swiper-wrapper">
          <div class="swiper-slide slide">
            <div class="content">
              <span>order online</span>
              <h3>Turmeric Spiced Coffee</h3>
              <NuxtLink to="/Customer/Cmenue" class="btn">see menus</NuxtLink>
            </div>
            <div class="image">
              <img
                src="http://localhost:8000/images/home-img-1.1.png"
                alt="Turmeric Spiced Coffee"
              />
            </div>
          </div>

          <div class="swiper-slide slide">
            <div class="content">
              <span>order online</span>
              <h3>delicious pizza</h3>
              <NuxtLink to="/Customer/Cmenue" class="btn">see menus</NuxtLink>
            </div>
            <div class="image">
              <img
                src="http://localhost:8000/images/home-img-1.png"
                alt="Delicious Pizza"
              />
            </div>
          </div>

          <div class="swiper-slide slide">
            <div class="content">
              <span>order online</span>
              <h3>chezzy hamburger</h3>
              <NuxtLink to="/Customer/Cmenue" class="btn">see menus</NuxtLink>
            </div>
            <div class="image">
              <img
                src="http://localhost:8000/images/home-img-2.png"
                alt="Cheesy Hamburger"
              />
            </div>
          </div>

          <div class="swiper-slide slide">
            <div class="content">
              <span>order online</span>
              <h3>roasted chicken</h3>
              <NuxtLink to="/Customer/Cmenue" class="btn">see menus</NuxtLink>
            </div>
            <div class="image">
              <img
                src="http://localhost:8000/images/home-img-3.png"
                alt="Roasted Chicken"
              />
            </div>
          </div>
        </div>

        <div class="swiper-pagination"></div>
      </div>
    </section>

    <!-- category Area -->
    <section class="category">
      <h1 class="title">food category</h1>

      <div class="box-container">
        <NuxtLink to="/Customer/Ccoffee" class="box">
          <img
            src="http://localhost:8000/images/cat-1.png"
            alt="Coffee"
          />
          <h3>Coffee</h3>
        </NuxtLink>

        <NuxtLink to="/Customer/Cmaindish" class="box">
          <img
            src="http://localhost:8000/images/cat-2.png"
            alt="Special Dishes"
          />
          <h3>Special dishes</h3>
        </NuxtLink>

        <NuxtLink to="/Customer/Cdrink" class="box">
          <img
            src="http://localhost:8000/images/cat-3.png"
            alt="Drinks"
          />
          <h3>drinks</h3>
        </NuxtLink>

        <NuxtLink to="/Customer/Cdessert" class="box">
          <img
            src="http://localhost:8000/images/cat-4.png"
            alt="Desserts"
          />
          <h3>desserts</h3>
        </NuxtLink>
      </div>
    </section>

    <!-- about section starts  -->
    <section class="about">
      <div class="row">
        <div class="image">
          <img
            src="http://localhost:8000/images/coffee-shop-1209863_1280.jpg"
            alt="Why Choose Us"
          />
        </div>
        <div class="content">
          <h3>why choose us?</h3>
          <p>
            Lorem, ipsum dolor sit amet consectetur adipisicing elit. Deserunt,
            neque debitis incidunt qui ipsum sed doloremque a molestiae in
            veritatis ullam similique sunt aliquam dolores dolore? Quasi atque
            debitis nobis!
          </p>
          <NuxtLink to="/Customer/Cmenue" class="btn">our menu</NuxtLink>
        </div>
      </div>
    </section>

    <!-- Simple Steps section -->
    <section class="steps">
      <h1 class="title">simple steps</h1>
      <div class="box-container">
        <div class="box">
          <img
            src="http://localhost:8000/images/step-1.png"
            alt="Choose Order"
          />
          <h3>Choose Order</h3>
          <p>
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam,
            voluptatum.
          </p>
        </div>
        <div class="box">
          <img
            src="http://localhost:8000/images/step-2.png"
            alt="Fast Delivery"
          />
          <h3>Fast Delivery</h3>
          <p>
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam,
            voluptatum.
          </p>
        </div>
        <div class="box">
          <img
            src="http://localhost:8000/images/step-3.png"
            alt="Enjoy Food"
          />
          <h3>Enjoy Food</h3>
          <p>
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam,
            voluptatum.
          </p>
        </div>
      </div>
    </section>

    <!-- Map / contact -->
    <section class="contact">
      <div class="row">
        <div class="map">
          <iframe
            class="minmap"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15777.467380763725!2d125.60962806977538!3d8.956199999999997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3301ceb3eb7f5e0f%3A0x4a5f5b04a5b5b5b5!2sCaraga%20State%20University%20-%20Ampayon%20Campus!5e1!3m2!1sen!2sbd!4v1660587920897!5m2!1sen!2sbd"
            width="600"
            height="450"
            style="border: 0"
            allowfullscreen
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
          />
        </div>

        <form
          v-if="isAuthenticated"
          action="http://localhost:8000/message/store"
          method="post"
          @submit.prevent="handleContactSubmit"
        >
          <h3>Contact Us !</h3>
          <input
            v-model="contactName"
            type="text"
            name="name"
            maxlength="50"
            class="box"
            placeholder="Enter Your Name"
            required
          />
          <input
            v-model="contactEmail"
            type="email"
            name="email"
            maxlength="50"
            class="box"
            placeholder="Enter Your Email"
            required
          />
          <textarea
            v-model="contactMessage"
            name="msg"
            class="box"
            required
            placeholder="Enter Your Message"
            maxlength="500"
            cols="30"
            rows="10"
          />
          <input 
            type="submit" 
            :value="isSubmitting ? 'Sending...' : 'Send'" 
            name="send" 
            class="btn" 
            :disabled="isSubmitting"
          />
        </form>
        <div v-else class="login-prompt">
          <p>
            Please
            <NuxtLink to="/Auth/login">login</NuxtLink> to send us a
            message.
          </p>
        </div>
      </div>
    </section>

    <!-- Footer -->
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
          <h3>contact info</h3>
          <div class="phone">
            <p><i class="fas fa-phone"></i> +639914677225</p>
            <p><i class="fas fa-phone"></i> +639512592678</p>
            <p>
              <i class="fas fa-envelope"></i> magbanuajembo17@gmail.com
            </p>
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
import { ref, onMounted, watch } from 'vue'
import { useHead, useRouter, useRoute } from '#imports'
import { useAuth } from '@/composables/useAuth'

const router = useRouter()
const route = useRoute()
const { user, isAuthenticated, fetchUser, logout } = useAuth()
const successMessage = ref('')
const contactMessage = ref('')
const contactName = ref('')
const contactEmail = ref('')
const isSubmitting = ref(false)

// Load Swiper assets for hero slider pagination
useHead({
  title: 'Kape Na! - Welcome',
  link: [
    {
      rel: 'icon',
      href:
        'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>☕</text></svg>',
    },
    {
      rel: 'stylesheet',
      href: 'https://unpkg.com/swiper@8/swiper-bundle.min.css',
    },
    {
      rel: 'stylesheet',
      href: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css',
    },
  ],
  script: [
    {
      src: 'https://unpkg.com/swiper@8/swiper-bundle.min.js',
      defer: true,
    },
  ],
})

// Check for success message in route query
onMounted(async () => {
  // Fetch user if authenticated
  await fetchUser()
  
  // Initialize contact form with user data if available
  if (user.value) {
    contactName.value = user.value.name || ''
    contactEmail.value = user.value.email || ''
  }

  // Watch for user changes and update form fields
  watch(user, (newUser) => {
    if (newUser) {
      if (!contactName.value) contactName.value = newUser.name || ''
      if (!contactEmail.value) contactEmail.value = newUser.email || ''
    }
  }, { immediate: true })

  // Check for success message from route query
  if (route.query.success) {
    successMessage.value = route.query.success
    // Auto-hide after 5 seconds
    setTimeout(() => {
      successMessage.value = ''
    }, 5000)
  }

  // Initialize Swiper hero slider
  const initSwiper = () => {
    const SwiperCtor = window.Swiper
    if (!SwiperCtor) {
      setTimeout(initSwiper, 100)
      return
    }

    // eslint-disable-next-line no-new
    new SwiperCtor('.hero-slider', {
      loop: true,
      grabCursor: true,
      effect: 'flip',
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
    })
  }

  initSwiper()
})

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

const handleContactSubmit = async (e) => {
  e.preventDefault()
  
  // Prevent double submission
  if (isSubmitting.value) {
    return
  }
  
  isSubmitting.value = true
  successMessage.value = '' // Clear any previous messages
  
  try {
    const { $axios } = useNuxtApp()
    
    // Get form data - use v-model values first, then fallback to form values
    const form = e.target
    const nameValue = contactName.value || user.value?.name || form.querySelector('[name="name"]')?.value || form.name?.value
    const emailValue = contactEmail.value || user.value?.email || form.querySelector('[name="email"]')?.value || form.email?.value
    const msgValue = contactMessage.value || form.querySelector('[name="msg"]')?.value || form.msg?.value
    
    // Validate required fields
    if (!nameValue || !emailValue || !msgValue) {
      successMessage.value = 'Please fill in all required fields.'
      isSubmitting.value = false
      setTimeout(() => {
        successMessage.value = ''
      }, 5000)
      return
    }
    
    const formData = {
      name: nameValue,
      email: emailValue,
      msg: msgValue,
      userId: user.value?.id || null,
    }
    
    console.log('Sending message:', formData)
    
    // Try API route first, fallback to web route if needed
    let response
    try {
      // Try API route: /api/message/store
      response = await $axios.post('/message/store', formData)
    } catch (apiError) {
      console.warn('API route failed, trying web route:', apiError)
      // Fallback to web route: /message/store (without /api)
      const config = useRuntimeConfig()
      const apiBase = config.public.apiBase || 'http://localhost:8000/api'
      const laravelBase = apiBase.replace('/api', '')
      
      response = await $axios.post('/message/store', formData, {
        baseURL: laravelBase,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        }
      })
    }
    
    console.log('Message sent successfully:', response.data)
    
    // Clear form fields
    contactMessage.value = ''
    // Keep name and email if they're from user data, otherwise clear them
    if (!user.value?.name) {
      contactName.value = ''
    }
    if (!user.value?.email) {
      contactEmail.value = ''
    }
    
    // Show success message briefly
    successMessage.value = 'Message sent successfully! We will get back to you soon.'
    
    // Scroll to top to show success message
    if (process.client) {
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }
    
    // Clear success message and refresh the page after 2 seconds
    setTimeout(() => {
      if (process.client) {
        successMessage.value = ''
        // Refresh the page without query parameters to clear everything
        router.push({
          path: '/Customer/Cdashboard',
          query: {}
        })
      }
    }, 2000)
  } catch (error) {
    console.error('Error sending message:', error)
    console.error('Error details:', {
      message: error.message,
      response: error.response?.data,
      status: error.response?.status,
      url: error.config?.url,
      baseURL: error.config?.baseURL,
    })
    
    const errorMsg = error.response?.data?.message 
      || error.response?.data?.error
      || error.message
      || 'Failed to send message. Please try again.'
    
    successMessage.value = `Error: ${errorMsg}`
    
    // Auto-hide error message after 7 seconds
    setTimeout(() => {
      successMessage.value = ''
    }, 7000)
  } finally {
    isSubmitting.value = false
  }
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

*::selection {
  background-color: var(--white);
  color: var(--white);
}

::-webkit-scrollbar {
  height: 0.5rem;
  width: 1rem;
}

::-webkit-scrollbar-track {
  background-color: transparent;
}

::-webkit-scrollbar-thumb {
  background-color: var(--yellow);
}

html {
  font-size: 62.5%;
  overflow-x: hidden;
  scroll-behavior: smooth;
  scroll-padding-top: 7rem;
}

body {
  background: var(--bg);
  color: #fff;
}

section {
  margin: 0 auto;
  max-width: 1200px;
  padding: 2rem;
}

.title {
  text-align: center;
  margin-bottom: 2.5rem;
  font-size: 4rem;
  color: var(--white);
  text-transform: uppercase;
  text-underline-offset: 1rem;
}

.btn {
  margin-top: 1rem;
  display: inline-block;
  font-size: 2rem;
  padding: 1rem 3rem;
  cursor: pointer;
  text-transform: capitalize;
  transition: 0.2s linear;
  background-color: var(--yellow);
  color: var(--white);
}

.btn:hover {
  letter-spacing: 0.2rem;
}

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

/* Success message */
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

/* Hero section */
.hero {
  width: 100%;
  margin-bottom: 100px;
}

.hero .swiper-slide {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 2rem;
  padding-top: 9rem;
}

.hero .slide .content {
  flex: 1 1 45rem;
  text-align: center;
}

.hero .slide .image {
  flex: 1 1 45rem;
  display: flex;
  justify-content: center;
  align-items: center;
}

.hero .slide .image img {
  width: 100%;
  max-height: 40rem;
  object-fit: cover;
  border-radius: 1rem;
}

.hero .slide .content span {
  color: var(--main-color);
  font-size: 2.5rem;
}

.hero .slide .content h3 {
  color: var(--white);
  font-size: 7rem;
  text-transform: capitalize;
  margin: 1rem 0;
}

.swiper-pagination-bullet-active {
  background-color: var(--white);
}

/* Category area */
.category .box-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(27rem, 1fr));
  gap: 2rem;
  align-items: flex-start;
}

.category .box-container .box {
  border: var(--border);
  padding: 2rem;
  text-align: center;
  background: var(--black);
  transition: transform 0.3s;
  display: flex;
  flex-direction: column;
  align-items: center;
  height: 100%;
}

.category .box-container .box:hover {
  transform: translateY(-10px);
}

.category .box-container .box img {
  width: 100%;
  max-width: 200px;
  height: auto;
  max-height: 12rem;
  object-fit: contain;
  border-radius: 0.5rem;
}

.category .box-container .box h3 {
  font-size: 2.5rem;
  margin-top: 1.5rem;
  color: var(--white);
  text-transform: capitalize;
}

.category .box-container .box:hover {
  background-color: var(--white);
}

.category .box-container .box:hover h3 {
  color: var(--black);
}

/* About section */
.about .row {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 1.5rem;
  background-color: var(--black);
  padding: 2rem;
}

.about .row .image {
  flex: 1 1 40rem;
}

.about .row .image img {
  width: 100%;
  border-radius: 1rem;
}

.about .row .content {
  flex: 1 1 40rem;
  text-align: center;
}

.about .row .content h3 {
  font-size: 3rem;
  color: var(--white);
  text-transform: capitalize;
  margin-bottom: 1rem;
}

.about .row .content p {
  padding: 1rem 0;
  line-height: 2;
  font-size: 1.6rem;
  color: var(--light-color);
}

/* Simple Steps section */
.steps {
  margin-bottom: 5rem;
}

.steps .box-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(30rem, 1fr));
  gap: 2rem;
  align-items: flex-start;
}

.steps .box-container .box {
  text-align: center;
  padding: 2rem;
  background: var(--black);
  border: var(--border);
  transition: transform 0.3s;
}

.steps .box-container .box:hover {
  transform: translateY(-10px);
}

.steps .box-container .box img {
  width: 100%;
  height: 20rem;
  object-fit: contain;
  margin-bottom: 1.5rem;
}

.steps .box-container .box h3 {
  font-size: 2.5rem;
  color: var(--white);
  text-transform: capitalize;
  margin-bottom: 1rem;
}

.steps .box-container .box p {
  font-size: 1.6rem;
  color: var(--light-color);
  line-height: 2;
}

/* Contact section */
.contact .row {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 1.5rem;
}

.contact .row .map {
  flex: 1 1 40rem;
}

.contact .row .map iframe {
  width: 100%;
  height: 40rem;
  border: var(--border);
  border-radius: 1rem;
}

.contact .row form {
  flex: 1 1 40rem;
  padding: 2rem;
  text-align: center;
}

.contact .row form h3 {
  font-size: 2.5rem;
  color: var(--white);
  margin-bottom: 1rem;
  text-transform: capitalize;
}

.contact .row form .box {
  margin: 0.7rem 0;
  font-size: 1.8rem;
  color: var(--white);
  border-bottom: var(--border);
  padding: 1.4rem;
  width: 100%;
  background: transparent;
}

.contact .row form textarea {
  height: 20rem;
  resize: none;
}

.contact .row .login-prompt {
  flex: 1 1 40rem;
  padding: 2rem;
  text-align: center;
}

.contact .row .login-prompt p {
  color: var(--light-color);
  font-size: 1.6rem;
  text-align: center;
  padding: 2rem;
}

.contact .row .login-prompt a {
  color: var(--main-color);
  text-decoration: underline;
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
  grid-template-columns: repeat(auto-fit, minmax(27rem, 1fr));
  gap: 1.5rem;
  align-items: flex-start;
}

.footer .grid .box {
  text-align: center;
  padding: 2rem;
  border: var(--border);
}

.footer .grid .box h3 {
  font-size: 2.2rem;
  margin-bottom: 1.5rem;
  color: var(--white);
  text-transform: capitalize;
}

.footer .grid .box a,
.footer .grid .box p {
  display: block;
  padding: 0.7rem 0;
  font-size: 1.7rem;
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
  font-size: 1.8rem;
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
  padding: 0.5rem 0;
}

.footer .grid .box .dateinfo p,
.footer .grid .box .dateinfo samp {
  font-size: 1.6rem;
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
  font-size: 2rem;
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

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 991px) {
  html {
    font-size: 55%;
  }
}

@media (max-width: 768px) {
  .header-container {
    flex-direction: column;
    gap: 1.5rem;
  }
  
  .header-center {
    order: 3;
    width: 100%;
  }
  
  .navbar {
    justify-content: center;
    flex-wrap: wrap;
    gap: 1.5rem;
  }
  
  .header-left,
  .header-right {
    width: 100%;
    display: flex;
    justify-content: center;
  }
  
  .nav-link {
    font-size: 1.8rem;
    padding: 0.5rem;
  }
  
  .hero .slide .content h3 {
    font-size: 5rem;
  }

  .hero .slide .image {
    flex: 1 1 100%;
  }

  .contact .row {
    flex-direction: column;
  }
}

@media (max-width: 450px) {
  html {
    font-size: 50%;
  }

  .title {
    font-size: 3rem;
  }
  
  .navbar {
    gap: 1rem;
  }
  
  .nav-link {
    font-size: 1.6rem;
    padding: 0.3rem;
  }
  
  .auth-btn {
    padding: 0.6rem 1.5rem;
    font-size: 1.4rem;
  }

  .hero .slide .content h3 {
    font-size: 4rem;
  }

  .category .box-container .box img {
    max-height: 10rem;
  }
}
</style>