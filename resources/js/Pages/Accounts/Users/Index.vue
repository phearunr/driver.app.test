<template>
    <AppLayout title="Users">
        <template #header>
            <div class="flex items-center space-x-2">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Users </h2>
            </div>
        </template>

        <div class="p-8 mx-auto max-w-7xl">
        <section class="flex flex-col p-4 mb-4 space-y-4 bg-white shadow sm:rounded lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:p-2">
            <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-2">

            <select  v-model="query.role" aria-label="store" id="type" class="pr-10 pl-3 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-44 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option v-for="role in allRoles" :value="role.value"> {{ role.label }}</option>
            </select>

            <select  v-model="query.status" aria-label="status" id="type" class="pr-10 pl-3 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-44 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option v-for="status in allStatuses" :value="status.value"> {{ status.label }}</option>
            </select>

            <select v-model="query.month" aria-label="Media date" id="date" class="pr-10 pl-3 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-44 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option v-for="month in allMonths" :value="month.value">{{ month.label }}</option>
            </select>

            <button @click="filter()" type="button" class="inline-flex items-center px-4 h-11 font-medium text-gray-700 bg-white rounded border border-gray-300 shadow-sm lg:h-9 lg:text-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Filter
            </button>
            </div>

            <div class="flex flex-col">
            <label for="search" class="text-sm font-medium text-gray-700 sr-only">Search</label>
            <input v-model="query.term" @keydown.enter="filter()" type="search" id="search" class="w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm lg:w-64 focus:ring-blue-500 focus:border-blue-500" placeholder="Search for..." autocomplete="off"/>
            </div>
        </section>

        <section class="flex flex-col items-center space-y-4 mb-4 md:space-y-0 md:flex-row md:justify-between">
            <div class="flex flex-row space-x-2">
                <select-action
                    v-if="$page.props.user.permissions.includes('bulk action users')"
                    :actions="actions"
                    @execute="executeAction">
                </select-action>
                <button
                    v-if="$page.props.user.permissions.includes('add user')"
                   @click="createUserForm"
                    class="inline-flex items-center px-4 h-11 font-medium text-gray-700 bg-white rounded border border-gray-300 shadow-sm lg:h-9 lg:text-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            </div>
            <pagination :pagination="entries.meta"></pagination>
        </section>

        <section class="mb-4">
            <table class="min-w-full bg-white shadow table-fixed sm:rounded">
            <thead>
            <tr class="border-b border-gray-200">
                <th class="px-2 w-10 text-center">
                <input type="checkbox" @change="toggleSelectAll" class="w-6 h-6 text-blue-600 rounded border-gray-300 lg:w-4 lg:h-4 focus:ring-blue-500">
                </th>
                <th class="text-left">
                <span class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500">
                    Name
                </span>
                </th>
                <th class="hidden text-left lg:table-cell">
                <span class="inline-block p-2 font-normal text-blue-600 lg:text-sm">
                    Roles
                </span>
                </th>

                <th class="hidden w-28 text-left lg:table-cell">
                    <span class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500">
                        Status
                    </span>
                </th>
                <th class="hidden w-32 text-left lg:table-cell">
                    <span class="p-2 font-normal text-blue-600 lg:text-sm">
                        Last login at
                    </span>
                </th>
                <th class="hidden w-32 text-left lg:table-cell">
                    <span class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500">
                         Date
                    </span>
                </th>
            </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
            <tr class="align-top group" v-for="(entry, index) in entries.data" :key="entry.id">
                <td class="p-2 w-10 text-center">
                <input type="checkbox" v-model="entry.selected" class="w-6 h-6 text-blue-600 rounded border-gray-300 lg:w-4 lg:h-4 focus:ring-blue-500">
                </td>
                <td class="p-2 text-left">
                <div class="flex space-x-4">
                    <div class="overflow-hidden flex-shrink-0 w-12 h-12 bg-gray-100 rounded lg:w-16 lg:h-16">
                    <img :src="entry.profile_photo_url" class="object-cover">
                    </div>
                    <div>
                    <span class="text-sm font-semibold text-blue-600 break-all rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        {{ entry.name }}
                    </span>
                    <p class="text-xs text-gray-500 break-all">
                        {{ entry.email }}
                    </p>
                    <p class="text-xs text-blue-400 break-all">
                        Verified_at: {{ entry.email_verified_at }}
                    </p>

                    <div
                        class="flex items-center mt-2 space-x-2 md:invisible group-hover:visible">
                        <Link
                            v-if="$page.props.user.permissions.includes('edit user')"
                            :href="route('accounts.users.edit', entry.id)"
                            class="text-xs text-blue-600 rounded hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Edit
                        </Link>
                        <span class="text-xs text-gray-300">|</span>
                        <button
                            v-if="$page.props.user.permissions.includes('delete user')"
                            @click="deleteEnty(entry.id)"
                            class="text-xs text-red-600 rounded hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Delete
                        </button>
                        <span class="text-xs text-gray-300">|</span>
                        <button
                            v-if="$page.props.user.permissions.includes('view user')"
                            @click="ViewEntry(entry.id)"
                            class="text-xs text-blue-600 rounded hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            View
                        </button>
                    </div>
                    </div>
                </div>
                </td>
                <td class="hidden p-2 text-left lg:table-cell">
                    <a v-if="entry.roles"
                        href="#"
                        class="text-blue-600 rounded lg:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        {{ entry.role }}
                   </a>
                </td>
                <td class="hidden p-2 text-left lg:table-cell">
                <a href="#" class="text-blue-600 rounded lg:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                     {{ entry.status }}
                </a>
                </td>
                <td class="hidden p-2 text-left lg:table-cell">
                    <span class="text-gray-600 lg:text-sm">
                        {{  entry.last_activity }}
                    </span>
                </td>
                <td class="hidden p-2 text-left lg:table-cell">
                    <span class="text-gray-600 lg:text-sm">
                        {{  entry.created_at}}
                    </span>
                </td>
            </tr>

            <tr class="align-top" v-if="!entries.data.length">
                <td colspan="4" class="p-2 text-sm text-gray-700">
                No media files found.
                </td>
            </tr>
            </tbody>
            </table>
        </section>

        <section class="flex flex-col items-center space-y-4 mb-4 md:space-y-0 md:flex-row md:justify-between">
            <select-action :actions="actions" @execute="executeAction"></select-action>
            <pagination :pagination="entries.meta"></pagination>
        </section>
        </div>

  </AppLayout>
    <!-- Create Form Modal -->
    <JetDialogModal :show="isCreateUserForm" @close="isCreateUserForm = false">
    <template #title>
        Create User
    </template>

    <template #content>
        <AuthenticationCard>
            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="name" value="Name" />
                    <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mt-4">
                    <InputLabel for="email" value="Email" />
                    <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="Password" />
                    <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password_confirmation" value="Confirm Password" />
                    <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
            </form>
        </AuthenticationCard>
    </template>

    <template #footer>
        <JetSecondaryButton @click="closeModal">
            Cancel
        </JetSecondaryButton>
        <JetButton
            class="ml-3"
            :class="{'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="storeCreateNewUser">
            Save
        </JetButton>
    </template>
    </JetDialogModal>
<!-- / Create Form Modal -->

<!-- View Form Modal -->
<JetDialogModal :show="isViewUser" @close="isViewUser= false">
    <template #title>
        View User
    </template>

    <template #content>
        <div>
            <div>
                <div class="mb-4">
                    Last login at: <span class="text-sm text-gray-600"> {{ form.last_activity  }}</span>
                </div>
                <div class="mb-2">
                    Date: <span class="text-sm text-gray-600"> {{ form.created_at  }}</span>
                </div>
                <div class="mb-4">
                    Status: <span class="text-sm text-gray-600"> {{ form.status  }}</span>
                </div>
            </div>
            <table class="border-collapse w-full">
                <tbody>
                    <tr class="bg-white lg:hover:bg-gray-100 flex lg:table-row flex-row lg:flex-row flex-wrap lg:flex-no-wrap mb-10 lg:mb-0">
                        <td class="w-full lg:w-auto p-3 text-gray-800 text-left border border-b block lg:table-cell relative lg:static">
                            Name:{{ form.name  }}
                        </td>
                    </tr>
                    <tr class="bg-white lg:hover:bg-gray-100 flex lg:table-row flex-row lg:flex-row flex-wrap lg:flex-no-wrap mb-10 lg:mb-0">
                        <td class="w-10 lg:w-auto p-3 text-gray-800 text-left border border-b block lg:table-cell relative lg:static">
                            Email: {{ form.email  }}
                        </td>

                    </tr>
                    <tr class="bg-white lg:hover:bg-gray-100 flex lg:table-row flex-row lg:flex-row flex-wrap lg:flex-no-wrap mb-10 lg:mb-0">
                        <td class="w-full  lg:w-auto p-3 text-gray-800 text-left border border-b block lg:table-cell relative lg:static">
                            Email Verified at: {{ form.email_verified_at  }}
                        </td>
                    </tr>
                    <tr class="bg-white lg:hover:bg-gray-100 flex lg:table-row flex-row lg:flex-row flex-wrap lg:flex-no-wrap mb-10 lg:mb-0">
                        <td class="w-full  lg:w-auto p-3 text-gray-800 text-left border border-b block lg:table-cell relative lg:static">
                            Role:  {{ form.role  }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </template>

    <template #footer>
        <JetSecondaryButton @click="closeModal">
            Cancel
        </JetSecondaryButton>
    </template>
    </JetDialogModal>
<!-- / View Form Modal -->

</template>

<script>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { pickBy } from 'lodash';
import AppLayout from '@/Layouts/AppLayout.vue';

import JetButton from '@/Jetstream/Button.vue';
import JetDialogModal from '@/Jetstream/DialogModal.vue';

import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue';
import JetAuthenticationCard from '@/Jetstream/AuthenticationCard.vue';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import JetInput from '@/Jetstream/Input.vue';
import JetLabel from '@/Jetstream/Label.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

import Pagination from '@/Components/Pagination.vue';
import SelectAction from '@/Components/SelectAction.vue';

export default {
    components: {
        Link,
        AppLayout,
        Pagination,
        SelectAction,
        JetButton,
        JetDialogModal,
        JetSecondaryButton,
        JetAuthenticationCard,
        JetInput,
        JetLabel,
        InputError,
        PrimaryButton,
        TextInput,
        InputLabel
    },
    props: {
        entries: Object,
        statuses: Array,
        roles: Array,
        months: Array,
        queryParams: Object,
    },
    data() {
        return {
            isViewUser: false,
            isCreateUserForm: false,
            form: useForm({
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                terms: false,
            }),

            actions: [
                { id: null, label: 'Bulk actions' },
                { id: 'delete', label: 'Delete' },
                { id: 'export-to-xlsx', label: 'Export .xlsx' },
            ],
            query: {
                status: this.queryParams.status,
                term: this.queryParams.term,
                role: this.queryParams.role,
                month: this.queryParams.month,
            }
        };
    },
    methods: {
        createUserForm() {
            this.isCreateUserForm = true;
        },
        storeCreateNewUser() {
            this.form.post(route('accounts.users.store'), {
                onSuccess: () => this.closeModal(),
                onFinish: () => this.form.reset('password', 'password_confirmation')
            });
        },
        ViewEntry(id) {
            axios.get(route('accounts.users.show', id))
                .then(response => {
                    this.form.id = id;
                    this.form = response.data;
                    // this.form.log_name = response.data.log_name;
                    // this.form.description = response.data.description;
                    // this.form.subject = response.data.subject;
                    // this.form.properties = response.data.properties;
                    // this.form.changes = response.data.changes;
                    // this.form.causer = response.data.causer ?? null;
                    // this.form.event = response.data.event;
                    // this.form.created_at = response.data.created_at;
                    this.isViewUser = true;
                })
                .catch(error => {
                    console.log(error);
                }
            );
        },
        deleteEnty(entryId) {
            if (confirm(`Are you sure?`)) {
                this.$inertia.delete(route('accounts.users.destroy'), {
                    preserveState: false,
                    data: {
                        entryIds: [entryId],
                    },
                });
            }
        },
        toggleSelectAll(e) {
            this.entries.data.forEach(entry => entry.selected = e.target.checked);
        },
        executeAction(actionId) {
            const entryIds = this.entries.data.filter(entry => entry.selected)
                .map(entry => entry.id);
            if (!entryIds.length) return;

            switch (actionId) {
                case 'delete':
                    if (confirm(`Are you sure?`)) {
                        this.$inertia.delete(route('accounts.users.destroy'), {
                            preserveState: false,
                            data: {
                                entryIds: entryIds,
                            },
                        });
                    }
                    break;

                case 'export-to-xlsx':
                    if (confirm(`Are you wish bulk items to .xlsx with selected?`)) {
                        window.open(`/accounts/users/export/bulk-action?entryIds=${entryIds}`, '_blank');
                    }
                    break;
            }
        },
        filter() {
            this.$inertia.get(route('accounts.users.index'), pickBy(this.query), {
                preserveState: true,
            });
        },
        closeModal: function() {
            this.isCreateUserForm = false;
            this.isViewUser = false;
            // Clear all errors
            this.form.clearErrors();
            this.form.reset(
                'name',
                'email',
                'password',
                'password_confirmation'
            );

        },
        finishSubmit: function(response) {
            // const toaster = createToaster();
            // toaster.success(response.message);
            this.closeModal();
        }
    },
    computed: {
        allRoles() {
            return [
                { value: null, label: 'Any role' },
                ...this.roles,
            ];
        },
        allStatuses() {
            return [
                { value: null, label: 'Any status' },
                ...this.statuses,
            ];
        },
        allMonths() {
            return [
                { value: null, label: 'Any date' },
                ...this.months,
            ];
        },
    },
};
</script>

