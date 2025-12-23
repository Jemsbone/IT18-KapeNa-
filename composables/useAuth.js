/**
 * Authentication Composable
 * Manages user authentication state and provides auth-related methods
 */
export const useAuth = () => {
  const user = useState('user', () => null)
  const isAuthenticated = computed(() => !!user.value)
  const { $axios } = useNuxtApp()
  const router = useRouter()

  /**
   * Fetch current authenticated user
   */
  const fetchUser = async () => {
    try {
      const response = await $axios.get('/user')
      user.value = response.data
      return response.data
    } catch (error) {
      user.value = null
      return null
    }
  }

  /**
   * Login user
   */
  const login = async (credentials) => {
    try {
      const response = await $axios.post('/login', credentials)
      await fetchUser()
      return response.data
    } catch (error) {
      throw error
    }
  }

  /**
   * Register user
   */
  const register = async (userData) => {
    try {
      const response = await $axios.post('/register', userData)
      // Only fetch user if they're logged in (not if OTP verification is required)
      if (response.data.token || !response.data.requires_verification) {
        await fetchUser()
      }
      return response.data
    } catch (error) {
      throw error
    }
  }

  /**
   * Logout user
   */
  const logout = async () => {
    try {
      await $axios.post('/logout')
      user.value = null
      await router.push('/home')
    } catch (error) {
      // Even if logout fails, clear local state
      user.value = null
      await router.push('/home')
    }
  }

  return {
    user: readonly(user),
    isAuthenticated,
    fetchUser,
    login,
    register,
    logout,
  }
}

