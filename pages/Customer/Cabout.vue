<template>
  <div class="page">
    <!-- Header section -->
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
            <NuxtLink to="/Customer/Ccart" class="nav-link">Cart</NuxtLink>
            <NuxtLink to="/Customer/Cabout" class="nav-link active">About</NuxtLink>
          </nav>
        </div>

        <!-- Right section with auth/user -->
        <div class="header-right">
          <template v-if="isAuthenticated">
            <!-- User Profile when logged in -->
            <div class="user-profile">
              <div class="dropdown">
                <div class="user-info user-name">
                  <i class="fas fa-user-circle user-icon"></i>
                  <span>{{ user?.name }}</span>
                  <i
                    class="fas fa-chevron-down"
                    style="font-size: 1.2rem; margin-left: 0.5rem"
                  ></i>
                </div>
                <div class="dropdown-content">
                  <NuxtLink to="/Customer/Cprofile">
                    <i class="fas fa-user"></i> My Profile
                  </NuxtLink>
                  <NuxtLink to="/Customer/Ccart">
                    <i class="fas fa-shopping-cart"></i> Cart
                  </NuxtLink>
                  <NuxtLink to="/Customer/Csettings">
                    <i class="fas fa-cog"></i> Settings
                  </NuxtLink>
                  <a href="#" @click.prevent="handleLogout">
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

    <!-- Main Content -->
    <main class="about-main">
      <div class="about-container">
        <!-- Hero Section -->
        <section class="hero-section">
          <h1 class="main-title">
            <i class="fas fa-coffee"></i> Why Choose Kape Na!
          </h1>
          <p class="subtitle">Computer-Only Coffee Ordering</p>
        </section>

        <!-- Main Content Sections -->
        <section class="content-section">
          <div class="section-card">
            <h2 class="section-title">
              <i class="fas fa-laptop"></i> Designed Specifically for Computer Users
            </h2>
            <p class="section-text">
              Our system is built exclusively for laptop and desktop computers, offering a superior ordering experience that's faster and more efficient than mobile apps.
            </p>
          </div>

          <div class="section-card">
            <h2 class="section-title">
              <i class="fas fa-desktop"></i> Big Screen Advantages
            </h2>
            <ul class="feature-list">
              <li><i class="fas fa-check-circle"></i> <strong>Full Menu Display</strong> - See all options at once, no scrolling required</li>
              <li><i class="fas fa-check-circle"></i> <strong>High-Quality Images</strong> - View coffee and food items in crisp detail</li>
              <li><i class="fas fa-check-circle"></i> <strong>Easy Customization</strong> - All options visible, no hidden menus</li>
              <li><i class="fas fa-check-circle"></i> <strong>Simpler Navigation</strong> - Click through categories instantly</li>
            </ul>
          </div>

          <div class="section-card">
            <h2 class="section-title">
              <i class="fas fa-building"></i> Perfect for Office & Work Environments
            </h2>
            <ul class="feature-list">
              <li><i class="fas fa-check-circle"></i> <strong>Order from Your Desk</strong> - No need to use your personal phone</li>
              <li><i class="fas fa-check-circle"></i> <strong>Team Orders Made Easy</strong> - Share the link with colleagues</li>
              <li><i class="fas fa-check-circle"></i> <strong>Work Computer Friendly</strong> - No app installation needed (IT approved)</li>
              <li><i class="fas fa-check-circle"></i> <strong>Quick Access</strong> - Bookmark for daily coffee orders</li>
            </ul>
          </div>

          <div class="section-card">
            <h2 class="section-title">
              <i class="fas fa-clock"></i> Time-Saving Features
            </h2>
            <ul class="feature-list">
              <li><i class="fas fa-check-circle"></i> <strong>Tab-Friendly</strong> - Keep open in a browser tab while working</li>
              <li><i class="fas fa-check-circle"></i> <strong>Quick Reorder</strong> - Access past orders with one click</li>
              <li><i class="fas fa-check-circle"></i> <strong>Copy/Paste Orders</strong> - Easy for team coordination</li>
              <li><i class="fas fa-check-circle"></i> <strong>Multiple Windows</strong> - Compare items side-by-side</li>
            </ul>
          </div>

          <div class="section-card highlight">
            <h2 class="section-title">
              <i class="fas fa-star"></i> Why Computer-Only is Better:
            </h2>
            <ul class="feature-list">
              <li><i class="fas fa-bolt"></i> <strong>Faster Processing</strong> - Computers handle complex orders quicker</li>
              <li><i class="fas fa-users"></i> <strong>Better for Group Orders</strong> - Easier to coordinate with colleagues</li>
              <li><i class="fas fa-briefcase"></i> <strong>Professional Experience</strong> - Designed for work environments</li>
              <li><i class="fas fa-plug"></i> <strong>No Battery Concerns</strong> - Always powered, always ready</li>
            </ul>
          </div>

          <div class="section-card">
            <h2 class="section-title">
              <i class="fas fa-list"></i> Ideal Use Cases:
            </h2>
            <div class="use-cases-grid">
              <div class="use-case-item">
                <i class="fas fa-briefcase"></i>
                <h3>Office Coffee Runs</h3>
                <p>One person orders for the whole team</p>
              </div>
              <div class="use-case-item">
                <i class="fas fa-home"></i>
                <h3>Work-from-Home Professionals</h3>
                <p>Order between meetings</p>
              </div>
              <div class="use-case-item">
                <i class="fas fa-graduation-cap"></i>
                <h3>Students with Laptops</h3>
                <p>Study breaks made easy</p>
              </div>
              <div class="use-case-item">
                <i class="fas fa-handshake"></i>
                <h3>Business Meetings</h3>
                <p>Pre-order coffee for client meetings</p>
              </div>
              <div class="use-case-item">
                <i class="fas fa-redo"></i>
                <h3>Regular Customers</h3>
                <p>Set up your daily routine</p>
              </div>
            </div>
          </div>

          <div class="section-card">
            <h2 class="section-title">
              <i class="fas fa-cogs"></i> Technical Benefits:
            </h2>
            <ul class="feature-list">
              <li><i class="fas fa-check-circle"></i> <strong>Wider Screen Layout</strong> - More information visible</li>
              <li><i class="fas fa-check-circle"></i> <strong>Faster Loading</strong> - Desktop browsers are more powerful</li>
              <li><i class="fas fa-check-circle"></i> <strong>Better Security</strong> - Workplace-grade computers</li>
              <li><i class="fas fa-check-circle"></i> <strong>Printer Integration</strong> - Print receipts or order summaries</li>
            </ul>
          </div>

          <div class="section-card">
            <h2 class="section-title">
              <i class="fas fa-user-friends"></i> Perfect For:
            </h2>
            <div class="perfect-for-list">
              <div class="perfect-item">
                <i class="fas fa-building"></i>
                <span>Corporate Teams - Department coffee orders</span>
              </div>
              <div class="perfect-item">
                <i class="fas fa-store"></i>
                <span>Small Businesses - Office supply management</span>
              </div>
              <div class="perfect-item">
                <i class="fas fa-laptop-code"></i>
                <span>Freelancers - Professional coffee breaks</span>
              </div>
              <div class="perfect-item">
                <i class="fas fa-book"></i>
                <span>Students - Library or study session fuel</span>
              </div>
              <div class="perfect-item">
                <i class="fas fa-computer"></i>
                <span>Anyone Working on a Computer</span>
              </div>
            </div>
          </div>

          <div class="section-card">
            <h2 class="section-title">
              <i class="fas fa-sync-alt"></i> How It Works:
            </h2>
            <div class="how-it-works">
              <div class="steps-section">
                <h3>From Your Computer:</h3>
                <ol class="steps-list">
                  <li><strong>Open Browser</strong> → Chrome, Firefox, Edge, Safari</li>
                  <li><strong>Visit Our Site</strong> → No downloads needed</li>
                  <li><strong>Browse Full Menu</strong> → See everything clearly</li>
                  <li><strong>Place Order</strong> → Customize as needed</li>
                  <li><strong>Pickup at Shop</strong> → Grab and go!</li>
                </ol>
              </div>
              <div class="best-for-section">
                <h3>Best For:</h3>
                <ul class="best-for-list">
                  <li><i class="fas fa-sun"></i> <strong>Morning Routines</strong> - Order while checking emails</li>
                  <li><i class="fas fa-coffee"></i> <strong>Afternoon Breaks</strong> - Quick pick-me-up planning</li>
                  <li><i class="fas fa-handshake"></i> <strong>Meeting Prep</strong> - Coffee for important discussions</li>
                  <li><i class="fas fa-moon"></i> <strong>End-of-Day Treat</strong> - Reward yourself before heading home</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="section-card highlight">
            <h2 class="section-title">
              <i class="fas fa-check-double"></i> Computer-Exclusive Features:
            </h2>
            <div class="features-grid">
              <div class="feature-item">
                <i class="fas fa-image"></i>
                <span>Larger Product Images - See what you're ordering</span>
              </div>
              <div class="feature-item">
                <i class="fas fa-columns"></i>
                <span>Side-by-Side Comparison - Compare items easily</span>
              </div>
              <div class="feature-item">
                <i class="fas fa-keyboard"></i>
                <span>Keyboard Navigation - Faster than tapping</span>
              </div>
              <div class="feature-item">
                <i class="fas fa-window-restore"></i>
                <span>Multiple Tabs - Browse while keeping order open</span>
              </div>
              <div class="feature-item">
                <i class="fas fa-copy"></i>
                <span>Copy/Paste Functionality - Share orders with colleagues</span>
              </div>
            </div>
          </div>

          <div class="section-card commitment">
            <h2 class="section-title">
              <i class="fas fa-heart"></i> Our Commitment:
            </h2>
            <p class="commitment-text">
              "Premium coffee ordering, optimized for where you work and study best."
            </p>
            <p class="commitment-subtext">
              No mobile distractions. No tiny screens. Just efficient coffee ordering from your workstation.
            </p>
          </div>

          <div class="section-card tip">
            <h2 class="section-title">
              <i class="fas fa-lightbulb"></i> Pro Tip:
            </h2>
            <p class="tip-text">
              Bookmark our site on your work computer for daily access. Set up your favorite orders and make coffee breaks a seamless part of your workday!
            </p>
          </div>
        </section>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useNuxtApp } from '#app'

const router = useRouter()
const { $axios } = useNuxtApp()

const user = ref(null)
const isAuthenticated = ref(false)

onMounted(async () => {
  // Check if user is authenticated
  try {
    const response = await $axios.get('/user')
    if (response.data) {
      user.value = response.data
      isAuthenticated.value = true
    }
  } catch (error) {
    isAuthenticated.value = false
  }
})

const handleLogout = async () => {
  try {
    await $axios.post('/logout')
    user.value = null
    isAuthenticated.value = false
    router.push('/home')
  } catch (error) {
    console.error('Logout error:', error)
    user.value = null
    isAuthenticated.value = false
    router.push('/home')
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Anton&display=swap');
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css');

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

.page {
  background: var(--bg);
  color: var(--white);
  min-height: 100vh;
}

/* Header styles */
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
}

.header-center {
  flex: 1;
  display: flex;
  justify-content: center;
}

.navbar {
  display: flex;
  gap: 2rem;
}

.nav-link {
  color: var(--white);
  font-size: 1.4rem;
  text-decoration: none;
  transition: color 0.3s;
  padding: 0.5rem 1rem;
}

.nav-link:hover,
.nav-link.active {
  color: var(--main-color);
}

.header-right {
  flex: 0 0 auto;
}

.user-profile {
  position: relative;
}

.dropdown {
  position: relative;
  display: inline-block;
}

.user-info {
  display: flex;
  align-items: center;
  color: var(--white);
  cursor: pointer;
  font-size: 1.4rem;
}

.user-icon {
  font-size: 2rem;
  margin-right: 0.5rem;
  color: var(--main-color);
}

.dropdown-content {
  display: none;
  position: absolute;
  right: 0;
  background-color: var(--black);
  min-width: 200px;
  box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
  z-index: 1;
  margin-top: 0.5rem;
  border-radius: 0.5rem;
  overflow: hidden;
}

.dropdown:hover .dropdown-content {
  display: block;
}

.dropdown-content a {
  color: var(--white);
  padding: 1rem 1.5rem;
  text-decoration: none;
  display: block;
  font-size: 1.2rem;
  transition: background-color 0.3s;
}

.dropdown-content a:hover {
  background-color: var(--main-color);
  color: var(--black);
}

.dropdown-content a i {
  margin-right: 0.5rem;
  width: 20px;
}

.auth-buttons {
  display: flex;
  gap: 1rem;
}

.auth-btn {
  padding: 0.8rem 1.5rem;
  background-color: transparent;
  color: var(--white);
  border: 2px solid var(--main-color);
  border-radius: 0.5rem;
  font-size: 1.4rem;
  text-decoration: none;
  transition: all 0.3s;
}

.auth-btn:hover {
  background-color: var(--main-color);
  color: var(--black);
}

.auth-btn.register {
  background-color: var(--main-color);
  color: var(--black);
}

.auth-btn.register:hover {
  background-color: transparent;
  color: var(--white);
}

/* Main Content */
.about-main {
  padding: 3rem 2rem;
}

.about-container {
  max-width: 1200px;
  margin: 0 auto;
}

.hero-section {
  text-align: center;
  margin-bottom: 4rem;
  padding: 3rem 0;
}

.main-title {
  font-size: 4rem;
  color: var(--white);
  margin-bottom: 1rem;
  text-transform: uppercase;
}

.main-title i {
  color: var(--main-color);
  margin-right: 1rem;
}

.subtitle {
  font-size: 2rem;
  color: var(--main-color);
  font-weight: 300;
}

.content-section {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.section-card {
  background: rgba(34, 34, 34, 0.8);
  border-radius: 1rem;
  padding: 2.5rem;
  box-shadow: var(--box-shadow);
  border: 2px solid rgba(211, 173, 127, 0.3);
  transition: transform 0.3s, box-shadow 0.3s;
}

.section-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 0.8rem 1.5rem rgba(0, 0, 0, 0.2);
  border-color: var(--main-color);
}

.section-card.highlight {
  background: rgba(211, 173, 127, 0.1);
  border-color: var(--main-color);
}

.section-card.commitment {
  background: linear-gradient(135deg, rgba(211, 173, 127, 0.2), rgba(34, 34, 34, 0.8));
  text-align: center;
}

.section-card.tip {
  background: rgba(255, 193, 7, 0.1);
  border-color: #ffc107;
}

.section-title {
  font-size: 2.5rem;
  color: var(--main-color);
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.section-title i {
  font-size: 2rem;
}

.section-text {
  font-size: 1.6rem;
  line-height: 1.8;
  color: var(--white);
}

.feature-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.feature-list li {
  font-size: 1.5rem;
  color: var(--white);
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  line-height: 1.6;
}

.feature-list li i {
  color: var(--main-color);
  margin-top: 0.3rem;
  flex-shrink: 0;
}

.use-cases-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  margin-top: 1.5rem;
}

.use-case-item {
  background: rgba(211, 173, 127, 0.1);
  padding: 2rem;
  border-radius: 0.8rem;
  text-align: center;
  border: 2px solid rgba(211, 173, 127, 0.3);
  transition: all 0.3s;
}

.use-case-item:hover {
  transform: translateY(-5px);
  border-color: var(--main-color);
  background: rgba(211, 173, 127, 0.2);
}

.use-case-item i {
  font-size: 3rem;
  color: var(--main-color);
  margin-bottom: 1rem;
}

.use-case-item h3 {
  font-size: 1.8rem;
  color: var(--white);
  margin-bottom: 0.5rem;
}

.use-case-item p {
  font-size: 1.3rem;
  color: var(--light-color);
}

.perfect-for-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.perfect-item {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  font-size: 1.5rem;
  color: var(--white);
  padding: 1rem;
  background: rgba(211, 173, 127, 0.1);
  border-radius: 0.5rem;
}

.perfect-item i {
  color: var(--main-color);
  font-size: 2rem;
  width: 30px;
}

.how-it-works {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 3rem;
  margin-top: 1.5rem;
}

.steps-section h3,
.best-for-section h3 {
  font-size: 2rem;
  color: var(--main-color);
  margin-bottom: 1.5rem;
}

.steps-list {
  list-style: decimal;
  padding-left: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.steps-list li {
  font-size: 1.5rem;
  color: var(--white);
  line-height: 1.8;
}

.best-for-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.best-for-list li {
  font-size: 1.5rem;
  color: var(--white);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.best-for-list li i {
  color: var(--main-color);
  font-size: 1.8rem;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-top: 1.5rem;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  font-size: 1.4rem;
  color: var(--white);
  padding: 1.5rem;
  background: rgba(211, 173, 127, 0.1);
  border-radius: 0.5rem;
  border: 2px solid rgba(211, 173, 127, 0.3);
}

.feature-item i {
  color: var(--main-color);
  font-size: 2rem;
  width: 30px;
}

.commitment-text {
  font-size: 2.5rem;
  color: var(--main-color);
  font-style: italic;
  margin-bottom: 1rem;
  line-height: 1.6;
}

.commitment-subtext {
  font-size: 1.6rem;
  color: var(--white);
  line-height: 1.8;
}

.tip-text {
  font-size: 1.8rem;
  color: var(--white);
  line-height: 1.8;
  text-align: center;
}

/* Responsive Design */
@media (max-width: 768px) {
  .header-container {
    flex-direction: column;
    gap: 1rem;
  }

  .navbar {
    flex-wrap: wrap;
    justify-content: center;
    gap: 1rem;
  }

  .main-title {
    font-size: 2.5rem;
  }

  .subtitle {
    font-size: 1.5rem;
  }

  .section-title {
    font-size: 2rem;
  }

  .section-card {
    padding: 1.5rem;
  }

  .use-cases-grid,
  .features-grid {
    grid-template-columns: 1fr;
  }

  .how-it-works {
    grid-template-columns: 1fr;
  }
}
</style>








