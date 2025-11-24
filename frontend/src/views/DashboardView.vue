<template>
  <div>Dashboard View</div>
  <button
    @click="auth.logout();router.push('/login')">Logout</button>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()
</script>
