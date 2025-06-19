<script setup lang="ts">

import { RouterView } from 'vue-router'
import 'primeicons/primeicons.css';
import "tailwindcss";
// import "tailwindcss-primeui";

import Navbar from '@/components/Navbar.vue';
</script>

<template>
  <Navbar />
  <RouterView />
</template>
