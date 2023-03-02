<script setup>
import {cart} from "../stores/cart.js"
</script>

<template>
  <div class="p-4 flex justify-end bg-gray-100">
    <button @click="cart.setIsOpened(true)">Open cart</button>
  </div>
</template>
