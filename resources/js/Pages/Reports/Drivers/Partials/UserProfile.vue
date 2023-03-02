<script setup>
const props = defineProps({
    user: Array,
    date: String
});
</script>

<template>
    <div class="flex items-center">
        <div class="w-10 h-10 flex-shrink-0 mr-2 sm:mr-3">
            <img class="rounded-full" :src="user.profile_photo_url">
        </div>
        <div>
            <p class="font-medium text-gray-800">{{user.name}}</p>
            <p class="text-xs text-gray-500 break-all">
                At: {{ date }}
            </p>
        </div>
    </div>
</template>
