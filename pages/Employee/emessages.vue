<template>
  <div class="page-shell">

    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo">
        <span>Kape Na!</span>
        <i class="fas fa-coffee"></i>
      </div>
      <nav class="nav-menu">
        <NuxtLink to="/Employee/edashboard" class="nav-item">
          <i class="fas fa-home"></i>
          <span>Dashboard</span>
        </NuxtLink>
        <NuxtLink to="/Employee/eorders" class="nav-item">
          <i class="fas fa-shopping-cart"></i>
          <span>Orders</span>
        </NuxtLink>
        <NuxtLink to="/Employee/emessages" class="nav-item active">
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
        <h1>Customer Messages & Feedback</h1>
        <div class="employee-info">
          <i class="fas fa-user-tie"></i>
          <span>{{ employeeName }}</span>
        </div>
      </div>

      <!-- Success/Error Messages -->
      <div v-if="successMessage" class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ successMessage }}
      </div>
      <div v-if="errorMessage" class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> {{ errorMessage }}
      </div>

      <!-- Stats Cards -->
      <div class="stats-container">
        <div class="stat-card">
          <i class="fas fa-envelope"></i>
          <div class="stat-info">
            <h3>{{ messages.length }}</h3>
            <p>Total Messages</p>
          </div>
        </div>
        <div class="stat-card">
          <i class="fas fa-envelope-open"></i>
          <div class="stat-info">
            <h3>{{ unreadCount }}</h3>
            <p>Unread Messages</p>
          </div>
        </div>
        <div class="stat-card">
          <i class="fas fa-check-circle"></i>
          <div class="stat-info">
            <h3>{{ readCount }}</h3>
            <p>Read Messages</p>
          </div>
        </div>
      </div>

      <!-- Messages Container -->
      <div class="messages-container">
        <div class="messages-header">
          <h2>All Messages</h2>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="loading-state">
          <i class="fas fa-spinner fa-spin"></i>
          <p>Loading messages...</p>
        </div>

        <!-- Messages List -->
        <template v-else>
          <div
            v-for="message in messages"
            :key="message.id"
            class="message-item"
            :class="{ unread: !message.is_read }"
          >
            <div class="message-header">
              <div class="message-user-info">
                <h3>
                  <i class="fas fa-user-circle"></i>
                  {{ message.name }}
                  <span v-if="!message.is_read" class="unread-badge">NEW</span>
                </h3>
                <p>
                  <i class="fas fa-envelope"></i> {{ message.email }}
                  <span v-if="message.userId" class="user-id">
                    <i class="fas fa-user"></i> User ID: {{ message.userId }}
                  </span>
                </p>
              </div>
              <div class="message-date">
                <i class="far fa-clock"></i>
                {{ message.date }}
                <br />
                {{ message.time }}
              </div>
            </div>

            <div class="message-content">
              {{ message.message }}
            </div>

            <div class="message-actions">
              <button
                v-if="!message.is_read"
                type="button"
                class="action-btn mark-read-btn"
                @click="markRead(message.id)"
              >
                <i class="fas fa-check"></i> Mark as Read
              </button>
              <button
                type="button"
                class="action-btn delete-btn"
                @click="deleteMessage(message.id)"
              >
                <i class="fas fa-trash"></i> Delete
              </button>
            </div>
          </div>

          <div v-if="!messages.length && !isLoading" class="no-messages">
            <i class="fas fa-inbox"></i>
            <p>No messages yet. Customer messages will appear here.</p>
          </div>
        </template>
      </div>
    </main>
  </div>
</template>

<script setup>
// Imports
import { computed, ref, onMounted } from 'vue'
import { useHead } from '#imports'
import { useRouter } from 'vue-router'
import { useNuxtApp } from '#app'

// Router and Axios
const router = useRouter()
const { $axios } = useNuxtApp()

// Reactive Data
const employeeName = ref('Employee')
const successMessage = ref('')
const errorMessage = ref('')
const messages = ref([])
const isLoading = ref(false)

useHead({
  title: 'Customer Messages - Kape Na!',
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

// Load employee data
const loadEmployeeData = () => {
  const employeeData = localStorage.getItem('employee_data')
  if (employeeData) {
    try {
      const data = JSON.parse(employeeData)
      employeeName.value = data.name || 'Employee'
    } catch (error) {
      console.error('Error parsing employee data:', error)
    }
  }
}

// Fetch messages from API
const fetchMessages = async () => {
  isLoading.value = true
  try {
    const { $axios } = useNuxtApp()
    const response = await $axios.get('/message')
    
    // Debug: Log raw API response
    console.log('Raw API response:', response.data)
    
    messages.value = response.data.map((msg) => {
      // Properly convert is_read to boolean
      // Handle various formats: 0/1, "0"/"1", true/false, null/undefined
      let isRead = false
      if (msg.is_read !== undefined && msg.is_read !== null) {
        if (typeof msg.is_read === 'boolean') {
          isRead = msg.is_read
        } else if (typeof msg.is_read === 'number') {
          isRead = msg.is_read === 1
        } else if (typeof msg.is_read === 'string') {
          isRead = msg.is_read === '1' || msg.is_read.toLowerCase() === 'true'
        }
      }
      
      // Debug: Log each message's is_read value
      console.log(`Message ${msg.id}: is_read = ${msg.is_read} (type: ${typeof msg.is_read}), converted to: ${isRead}`)
      
      return {
        id: msg.id,
        name: msg.name,
        email: msg.email,
        userId: msg.user_id || null,
        message: msg.message || msg.msg,
        date: formatDate(msg.created_at),
        time: formatTime(msg.created_at),
        is_read: isRead,
      }
    })
    
    // Debug: Log final messages array
    console.log('Processed messages:', messages.value)
  } catch (error) {
    console.error('Error fetching messages:', error)
    errorMessage.value = 'Failed to load messages. Please refresh the page.'
    setTimeout(() => {
      errorMessage.value = ''
    }, 5000)
  } finally {
    isLoading.value = false
  }
}

// Format date for display
const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: '2-digit',
  })
}

// Format time for display
const formatTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
  })
}

const unreadCount = computed(() => messages.value.filter((m) => !m.is_read).length)
const readCount = computed(() => messages.value.filter((m) => m.is_read).length)

const markRead = async (id) => {
  try {
    const { $axios } = useNuxtApp()
    await $axios.put(`/message/${id}/read`)
    const msg = messages.value.find((m) => m.id === id)
    if (msg) msg.is_read = true
    successMessage.value = 'Message marked as read.'
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)
  } catch (error) {
    console.error('Error marking message as read:', error)
    errorMessage.value = 'Failed to update message status.'
    setTimeout(() => {
      errorMessage.value = ''
    }, 3000)
  }
}

const deleteMessage = async (id) => {
  if (!confirm('Are you sure you want to delete this message?')) {
    return
  }
  
  try {
    const { $axios } = useNuxtApp()
    await $axios.delete(`/message/${id}`)
    messages.value = messages.value.filter((m) => m.id !== id)
    successMessage.value = 'Message deleted successfully.'
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)
  } catch (error) {
    console.error('Error deleting message:', error)
    errorMessage.value = 'Failed to delete message.'
    setTimeout(() => {
      errorMessage.value = ''
    }, 3000)
  }
}

const handleLogout = async () => {
  try {
    await $axios.post(
      '/employee/logout',
      {},
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('employee_token')}`,
        },
      }
    )
  } catch (error) {
    console.error('Logout error:', error)
  } finally {
    localStorage.removeItem('employee_token')
    localStorage.removeItem('employee_data')
    window.location.href = '/Auth/employee_login'
  }
}

// Fetch messages on component mount
onMounted(() => {
  // Check if user is logged in
  const token = localStorage.getItem('employee_token')
  if (!token) {
    window.location.href = '/Auth/employee_login'
    return
  }
  
  loadEmployeeData()
  fetchMessages()
})
</script>

<style>
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
  --mark-read-btn: #27ae60;
  --unread-bg: #fff3cd;
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

.employee-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.employee-info i {
  font-size: 2.5rem;
  color: var(--main-color);
}

.employee-info span {
  font-size: 1.1rem;
  color: var(--light-color);
}

/* Stats Cards */
.stats-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: var(--white);
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: var(--box-shadow);
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.stat-card i {
  font-size: 3rem;
  color: var(--main-color);
}

.stat-info h3 {
  font-size: 2rem;
  color: var(--black);
  margin-bottom: 0.3rem;
}

.stat-info p {
  color: var(--light-color);
  font-size: 1rem;
}

/* Messages Container */
.messages-container {
  background: var(--white);
  padding: 2rem;
  border-radius: 8px;
  box-shadow: var(--box-shadow);
}

.messages-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid var(--bg);
}

.messages-header h2 {
  font-size: 1.8rem;
  color: var(--black);
}

/* Message Item */
.message-item {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  transition: all 0.3s;
}

.message-item:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.message-item.unread {
  background: var(--unread-bg);
  border-left: 4px solid var(--main-color);
}

.message-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.message-user-info {
  flex: 1;
}

.message-user-info h3 {
  font-size: 1.3rem;
  color: var(--black);
  margin-bottom: 0.3rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.unread-badge {
  background: var(--delete-btn);
  color: var(--white);
  padding: 0.2rem 0.6rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

.message-user-info p {
  color: var(--light-color);
  font-size: 0.95rem;
}

.user-id {
  margin-left: 1rem;
}

.message-date {
  color: var(--light-color);
  font-size: 0.9rem;
  text-align: right;
}

.message-content {
  background: #f9f9f9;
  padding: 1rem;
  border-radius: 6px;
  margin: 1rem 0;
  line-height: 1.6;
  color: var(--black);
  font-size: 1rem;
}

.message-actions {
  display: flex;
  gap: 0.8rem;
  justify-content: flex-end;
}

.action-btn {
  padding: 0.6rem 1.2rem;
  border-radius: 5px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.3s;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.mark-read-btn {
  background: var(--mark-read-btn);
  color: var(--black);
}

.mark-read-btn:hover {
  background: #229954;
  color: var(--black);
  transform: translateY(-2px);
}

.delete-btn {
  background: var(--delete-btn);
  color: var(--white);
}

.delete-btn:hover {
  background: #c0392b;
  transform: translateY(-2px);
}

.no-messages {
  text-align: center;
  padding: 3rem;
  color: var(--light-color);
}

.no-messages i {
  font-size: 4rem;
  color: var(--light-color);
  margin-bottom: 1rem;
}

.no-messages p {
  font-size: 1.2rem;
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

  .stats-container {
    grid-template-columns: 1fr;
  }

  .message-header {
    flex-direction: column;
    gap: 0.5rem;
  }

  .message-date {
    text-align: left;
  }
}
</style>

