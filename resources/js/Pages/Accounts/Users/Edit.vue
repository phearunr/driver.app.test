<template>
    <AppLayout title="User Edit">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User Edit
            </h2>
        </template>
        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div v-if="users">
                    <UpdateProfileInformationForm :user="users" />
                    <SectionBorder />
                </div>
                <div v-if="$page.props.jetstream.canUpdatePassword">
                    <UpdatePasswordForm :user="users" class="mt-10 sm:mt-0" />
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

import SectionBorder from '@/Components/SectionBorder.vue';

import UpdatePasswordForm from '@/Pages/Accounts/Users/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Accounts/Users/Partials/UpdateProfileInformationForm.vue';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
    users: Object
});
</script>

