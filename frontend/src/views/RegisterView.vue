<!-- src/views/RegisterView.vue -->
<template>
  <div class="auth-container">
    <h2>Create Account</h2>
    <form @submit.prevent="handleRegister" class="auth-form">
      <input v-model="name" type="text" placeholder="Full Name" required :disabled="auth.loading" />
      <input v-model="email" type="email" placeholder="Email" required :disabled="auth.loading" />
      <input
        v-model="password"
        type="password"
        placeholder="Password"
        required
        :disabled="auth.loading"
        minlength="6"
      />
      <input
        v-model="password_confirmation"
        type="password"
        placeholder="Confirm Password"
        required
        :disabled="auth.loading"
      />

      <button type="submit" :disabled="auth.loading">
        {{ auth.loading ? 'Creating...' : 'Register' }}
      </button>

      <p v-if="auth.error" class="error">{{ auth.error }}</p>

      <p class="link">Already have an account? <router-link to="/login">Log in</router-link></p>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')

const handleRegister = async () => {
  const success = await auth.register(
    name.value,
    email.value,
    password.value,
    password_confirmation.value,
  )
  if (success) {
    router.push('/dashboard')
  }
}
</script>

<style scoped>
.auth-container {
  max-width: 400px;
  margin: 50px auto;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-family: sans-serif;
}
.auth-form input {
  width: 100%;
  padding: 10px;
  margin: 8px 0;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}
.auth-form button {
  width: 100%;
  padding: 10px;
  background: #42b883;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 10px;
}
.auth-form button:disabled {
  background: #ccc;
  cursor: not-allowed;
}
.error {
  color: red;
  margin-top: 10px;
}
.link {
  text-align: center;
  margin-top: 15px;
}
.link a {
  color: #42b883;
  text-decoration: none;
}
</style>
