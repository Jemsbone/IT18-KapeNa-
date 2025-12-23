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

    <!-- Error Messages -->
    <div v-if="errorMessage" class="error-message">
      {{ errorMessage }}
    </div>

    <!-- Settings Section -->
    <section class="settings-section">
      <h1 class="title">Account Settings</h1>

      <div class="settings-container">
        <!-- Danger Zone - Delete Account -->
        <div class="danger-zone">
          <h2>
            <i class="fas fa-exclamation-triangle"></i> Danger Zone
          </h2>

          <div class="warning-box">
            <i class="fas fa-info-circle"></i>
            <p>
              <strong>Warning:</strong> Deleting your account is permanent and
              cannot be undone.
            </p>
          </div>
          <ul class="warning-list">
            <li>
              All your personal information will be permanently deleted
            </li>
            <li>
              Your order history will be preserved but disassociated from your
              account
            </li>
            <li>You will be immediately logged out</li>
            <li>
              You will need to create a new account to use our services again
            </li>
          </ul>
          <form @submit.prevent="handleDeleteAccount">
            <div class="form-group">
              <label for="password">
                <i class="fas fa-lock"></i> Confirm your password to delete
                your account
              </label>
              <input
                v-model="password"
                type="password"
                id="password"
                name="password"
                placeholder="Enter your current password"
                required
              />
            </div>
            <button type="button" class="delete-btn" @click="showConfirmationModal">
              <i class="fas fa-trash-alt"></i> Delete My Account
            </button>
          </form>
          <NuxtLink to="/Customer/Cprofile" class="cancel-btn">
            <i class="fas fa-arrow-left"></i> Cancel and Go Back to Profile
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- Confirmation Modal -->
    <div
      v-if="showModal"
      class="modal"
      @click.self="closeConfirmationModal"
    >
      <div class="modal-content">
        <div class="modal-header">
          <i class="fas fa-exclamation-triangle"></i>
          <h3>Are You Absolutely Sure?</h3>
        </div>
        <div class="modal-body">
          <p>
            This action cannot be undone. This will permanently delete your
            account and remove all your personal data from our servers.
          </p>
          <p style="margin-top: 1rem">
            <strong>Please type your password to confirm.</strong>
          </p>
        </div>
        <div class="modal-buttons">
          <button
            class="modal-btn modal-btn-cancel"
            @click="closeConfirmationModal"
          >
            <i class="fas fa-times"></i> Cancel
          </button>
          <button
            class="modal-btn modal-btn-delete"
            @click="confirmDelete"
            :disabled="isDeleting"
          >
            <i class="fas fa-check"></i>
            {{ isDeleting ? 'Deleting...' : 'Yes, Delete My Account' }}
          </button>
        </div>
      </div>
    </div>

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
import { ref, onMounted } from 'vue'
import { useHead } from '#imports'

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
const { user, isAuthenticated, logout, fetchUser } = useAuth()
const router = useRouter()
const { $axios } = useNuxtApp()

// State
const password = ref('')
const showModal = ref(false)
const isDeleting = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

// Redirect if not authenticated
onMounted(async () => {
  if (!isAuthenticated.value) {
    await fetchUser()
  }

  if (!isAuthenticated.value) {
    await router.push('/Auth/login')
  }
})

// Show confirmation modal
const showConfirmationModal = () => {
  if (!password.value) {
    showMessage('Please enter your password first.', 'error')
    return
  }
  showModal.value = true
}

// Close confirmation modal
const closeConfirmationModal = () => {
  showModal.value = false
}

// Confirm delete account
const confirmDelete = async () => {
  if (!password.value) {
    showMessage('Please enter your password first.', 'error')
    return
  }

  isDeleting.value = true

  try {
    // TODO: Replace with actual delete account endpoint when available
    // const response = await $axios.delete('/user/delete', {
    //   data: { password: password.value }
    // })

    // For now, simulate the deletion
    await new Promise((resolve) => setTimeout(resolve, 2000))

    // Clear local storage
    if (process.client) {
      localStorage.clear()
    }

    // Logout user
    await logout()

    showMessage('Your account has been successfully deleted.', 'success')

    // Redirect to home after a delay
    setTimeout(() => {
      router.push('/home')
    }, 2000)
  } catch (error) {
    console.error('Delete account error:', error)
    showMessage(
      error.response?.data?.message ||
        'Failed to delete account. Please try again.',
      'error'
    )
    isDeleting.value = false
    closeConfirmationModal()
  }
}

// Handle delete account form submission
const handleDeleteAccount = () => {
  showConfirmationModal()
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

// Show message
const showMessage = (message, type = 'success') => {
  if (type === 'success') {
    successMessage.value = message
    errorMessage.value = ''
  } else {
    errorMessage.value = message
    successMessage.value = ''
  }

  setTimeout(() => {
    successMessage.value = ''
    errorMessage.value = ''
  }, 5000)
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

.error-message {
  background-color: rgba(231, 76, 60, 0.2);
  color: #e74c3c;
  padding: 1rem 2rem;
  margin: 1rem auto;
  max-width: 1200px;
  border-radius: 0.5rem;
  font-size: 1.6rem;
  text-align: center;
  border: 1px solid #e74c3c;
}

/* Settings Section Styles */
.settings-section {
  min-height: 70vh;
  padding: 2rem;
}

.settings-container {
  max-width: 800px;
  margin: 2rem auto;
}

.settings-card {
  background-color: var(--black);
  border: var(--border);
  border-radius: 1rem;
  padding: 3rem;
  margin-bottom: 3rem;
}

.settings-card h2 {
  font-size: 2.8rem;
  color: var(--main-color);
  margin-bottom: 2rem;
  text-align: center;
  border-bottom: 2px solid var(--main-color);
  padding-bottom: 1rem;
}

.danger-zone {
  background-color: rgba(231, 76, 60, 0.1);
  border: 2px solid var(--red);
  border-radius: 1rem;
  padding: 3rem;
}

.danger-zone h2 {
  color: var(--red);
  border-bottom-color: var(--red);
  font-size: 2.8rem;
  margin-bottom: 2rem;
  text-align: center;
  border-bottom: 2px solid var(--red);
  padding-bottom: 1rem;
}

.warning-box {
  background-color: rgba(241, 196, 15, 0.1);
  border-left: 4px solid #f1c40f;
  padding: 2rem;
  margin: 2rem 0;
  border-radius: 0.5rem;
}

.warning-box i {
  color: #f1c40f;
  font-size: 2.5rem;
  margin-right: 1rem;
  vertical-align: middle;
}

.warning-box p {
  font-size: 1.6rem;
  color: var(--white);
  line-height: 1.8;
  display: inline;
}

.warning-list {
  list-style: none;
  margin: 2rem 0;
  padding: 0;
}

.warning-list li {
  font-size: 1.6rem;
  color: var(--light-color);
  padding: 1rem 0;
  padding-left: 3rem;
  position: relative;
}

.warning-list li::before {
  content: '⚠';
  position: absolute;
  left: 0;
  color: #f1c40f;
  font-size: 2rem;
}

.delete-form {
  margin-top: 3rem;
}

.form-group {
  margin-bottom: 2rem;
}

.form-group label {
  display: block;
  font-size: 1.6rem;
  color: var(--white);
  margin-bottom: 1rem;
}

.form-group input {
  width: 100%;
  padding: 1.5rem;
  font-size: 1.6rem;
  background-color: rgba(255, 255, 255, 0.1);
  border: 1px solid var(--light-color);
  border-radius: 0.5rem;
  color: var(--white);
  transition: all 0.3s;
}

.form-group input:focus {
  border-color: var(--red);
  background-color: rgba(255, 255, 255, 0.15);
}

.form-group input::placeholder {
  color: var(--light-color);
}

.delete-btn {
  width: 100%;
  padding: 1.5rem;
  font-size: 1.8rem;
  background-color: var(--red);
  color: var(--white);
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.3s;
  text-align: center;
  display: block;
  border: none;
}

.delete-btn:hover {
  background-color: #c0392b;
  letter-spacing: 0.1rem;
}

.cancel-btn {
  display: block;
  text-align: center;
  margin-top: 1.5rem;
  font-size: 1.6rem;
  color: var(--main-color);
  transition: all 0.3s;
}

.cancel-btn:hover {
  color: var(--white);
  text-decoration: underline;
}

/* Modal Styles */
.modal {
  display: flex;
  position: fixed;
  z-index: 9999;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.8);
  align-items: center;
  justify-content: center;
}

.modal-content {
  background-color: var(--black);
  margin: 10% auto;
  padding: 3rem;
  border: var(--border);
  border-radius: 1rem;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
}

.modal-header {
  text-align: center;
  margin-bottom: 2rem;
}

.modal-header i {
  font-size: 6rem;
  color: var(--red);
  margin-bottom: 1rem;
}

.modal-header h3 {
  font-size: 2.5rem;
  color: var(--white);
}

.modal-body {
  font-size: 1.6rem;
  color: var(--light-color);
  text-align: center;
  margin-bottom: 2rem;
  line-height: 1.8;
}

.modal-buttons {
  display: flex;
  gap: 1rem;
}

.modal-btn {
  flex: 1;
  padding: 1.5rem;
  font-size: 1.8rem;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.3s;
  text-align: center;
  border: none;
}

.modal-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.modal-btn-cancel {
  background-color: var(--light-color);
  color: var(--white);
}

.modal-btn-cancel:hover:not(:disabled) {
  background-color: #555;
}

.modal-btn-delete {
  background-color: var(--red);
  color: var(--white);
}

.modal-btn-delete:hover:not(:disabled) {
  background-color: #c0392b;
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

  .modal-buttons {
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
}
</style>

