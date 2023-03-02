<template>
    <AppLayout title="Currency">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Currency
            </h2>
        </template>

        <div class="py-5">
            <div class="p-8 mx-auto max-w-7xl">
                <section class="flex flex-col p-4 mb-4 space-y-4 bg-white shadow sm:rounded lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:p-2">
                    <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-2">
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
                            v-if="$page.props.user.permissions.includes('bulk action currency')"
                            :actions="actions"
                            @execute="executeAction">
                        </select-action>
                        <button
                            v-if="$page.props.user.permissions.includes('add currency')"
                           @click="createNewForm"
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
                        <span class="w-24 flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500">
                            Name
                        </span>
                        </th>
                        <th class="text-left lg:table-cell">
                        <span class="p-2 font-normal text-blue-600 lg:text-sm">
                            Exchange Rates
                        </span>
                        </th>

                        <th class="hidden w-28 text-left lg:table-cell">
                            <span class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500">
                                Status
                            </span>
                        </th>

                        <th class="hidden w-44 text-left lg:table-cell">
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
                            <div>
                            <span class="text-sm font-semibold text-blue-600 break-all rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                {{ entry.name }}
                            </span>

                            <div class="flex items-center mt-2 space-x-2 md:invisible group-hover:visible">
                                <button
                                    v-if="$page.props.user.permissions.includes('edit currency')"
                                    @click="editEnty(entry.id)"
                                    class="text-xs text-blue-600 rounded hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Edit
                                </button>
                                <span class="text-xs text-gray-300">|</span>
                                <button
                                    v-if="$page.props.user.permissions.includes('delete currency')"
                                    @click="deleteEnty(entry.id)"
                                    class="text-xs text-red-600 rounded hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Delete
                                </button>
                                <span class="text-xs text-gray-300">|</span>
                                <button
                                    v-if="$page.props.user.permissions.includes('view currency')"
                                    @click="show(entry.id)"
                                    class="text-xs text-blue-600 rounded hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    View
                                </button>
                            </div>

                            </div>
                        </div>
                        </td>
                        <td class="hidden p-2 text-left lg:table-cell">
                            <p class="text-xs text-red-400 break-all">
                                {{ entry.rates }}
                            </p>
                        </td>
                        <td class="hidden p-2 text-left lg:table-cell">
                            <span class="text-blue-600 lg:text-sm">
                                {{  entry.status}}
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
        </div>
        <DialogModal :show="isCreateForm" @close="closeModal">
            <template #title>
                Currency
            </template>
            <template #content>
                <form @submit.prevent="submit">
                    <div>
                        <label for="symbols">Name:</label>
                        <div class="mt-1">
                            <input
                                v-model="form.name"
                                type="text"
                                :class="{'border-red-500': form.errors.name }"
                                class="w-1/3 py-2 px-2 focus:ring-blue-600 block sm:text-sm border-gray-300"
                            />
                            <p v-if="form.errors.name" class="text-xs mt-1 text-red-600">{{form.errors.symbol}}</p>
                        </div>
                    </div>
                    <fieldset class="mt-2">
                        <div class="flex flex-row justify-between">
                            <div>
                                <legend class="flex item-center space-x-2">
                                    <span>Exchange Rates</span>
                                    <button
                                        @click="addRate()"
                                        type="button"
                                        class="bg-red-50 py-1 px-2 flex-shrink-0 text-sm leading-none font-medium text-red rounded-md border border-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-2 h-2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </button>
                                </legend>
                            </div>
                        </div>

                        <div class="mt-2" v-for="(rate, index) in form.rates" key="index">
                            <div v-if="!rate.deleted"
                                class="flex space-x-2 items-center">
                                <button
                                    @click="removeRate(index)"
                                    type="button"
                                    class="bg-white py-1 px-2 flex-shrink-0 text-xs leading-none font-medium text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                                <div class="flex-1">
                                    <input
                                        v-model="rate.amount"
                                        :class="{'border-red-500': form.errors[`rates.${index}.amount`] }"
                                        :aria-label="`rate ${index + 1} amount`"
                                        :max="4"
                                        type="number"
                                        placeholder="Amount"
                                        class="w-full py-1 px-2 focus:ring-blue-500 block sm:text-sm border-gray-300"
                                    />
                                </div>
                                <!-- <div class="w-16 flex-shrink-0">
                                    <input v-model="isDefault" value="radio-value" type="radio" />
                                </div> -->

                            </div>
                            <p
                                v-if="form.errors[`rates.${index}.amount`]"
                                class="ml-10 text-xs mt-1 text-red-600">
                                {{ form.errors[`rates.${index}.amount`] }}
                            </p>
                        </div>
                    </fieldset>
                </form>
            </template>
            <template #footer>
                <SecondaryButton @click="closeModal">
                    Cancel
                </SecondaryButton>
                <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="submitForm">
                    Save
                </PrimaryButton>
            </template>

        </DialogModal>

        <DialogModal :show="isEditForm" @close="closeModal">
            <template #title>
                Currency
            </template>
            <template #content>
                <form @submit.prevent="submit">
                    <div>
                        <label for="symbols">Name:</label>
                        <div class="mt-1">
                            <input
                                v-model="form.name"
                                type="text"
                                :class="{'border-red-500': form.errors.name }"
                                class="w-1/3 py-2 px-2 focus:ring-blue-600 block sm:text-sm border-gray-300"
                            />
                            <p v-if="form.errors.name" class="text-xs mt-1 text-red-600">{{form.errors.symbol}}</p>
                        </div>
                    </div>
                    <fieldset class="mt-2">
                        <div class="flex flex-row justify-between">
                            <div>
                                <legend class="flex item-center space-x-2">
                                    <span>Exchange Rates</span>
                                    <button
                                        @click="addRate()"
                                        type="button"
                                        class="bg-red-50 py-1 px-2 flex-shrink-0 text-sm leading-none font-medium text-red rounded-md border border-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-2 h-2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </button>
                                </legend>
                            </div>
                        </div>

                        <div class="mt-2" v-for="(rate, index) in form.rates" key="index">
                            <div
                                v-if="!rate.deleted"
                                class="flex space-x-2 items-center"
                            >
                                <button
                                    @click="removeRate(index)"
                                    type="button"
                                    class="bg-white py-1 px-2 flex-shrink-0 text-xs leading-none font-medium text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                                <div class="flex-1">
                                    <input
                                        v-model="rate.amount"
                                        :class="{'border-red-500': form.errors[`rates.${index}.amount`] }"
                                        :aria-label="`rate ${index + 1} amount`"
                                        type="number"
                                        placeholder="Amount"
                                        class="w-full py-1 px-2 focus:ring-blue-500 block sm:text-sm border-gray-300"
                                    />
                                </div>
                                <!-- <div class="w-16 flex-shrink-0">
                                    <input v-model="isDefault" value="radio-value" type="radio" />
                                </div> -->

                            </div>
                            <p
                                v-if="form.errors[`rates.${index}.amount`]"
                                class="ml-10 text-xs mt-1 text-red-600">
                                {{ form.errors[`rates.${index}.amount`] }}
                            </p>
                        </div>
                    </fieldset>
                </form>
            </template>
            <template #footer>
                <SecondaryButton @click="closeModal">
                    Cancel
                </SecondaryButton>
                <PrimaryButton class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="submitEditForm">
                    Save
                </PrimaryButton>
            </template>

        </DialogModal>
    </AppLayout>
</template>

<script>

import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { pickBy } from 'lodash';

import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import SelectAction from '@/Components/SelectAction.vue';
import DialogModal from '@/Components/DialogModal.vue';

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import JetButton from '@/Components/Button.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

export default {
    components: {
        Link,
        AppLayout,
        Pagination,
        SelectAction,
        JetButton,
        DialogModal,
        TextInput,
        PrimaryButton,
        InputLabel,
        InputError,
        SecondaryButton,
    },
    props: {
        entries: Object,
        statuses: Array,
        months: Array,
        queryParams: Object,
    },
    data() {
        return {
            isCreateForm: false,
            isEditForm: false,
            form: useForm({
                id: null,
                name: null,
                rates: [],
            }),

            actions: [
                { id: null, label: 'Bulk actions' },
                { id: 'delete', label: 'Delete' },
                { id: 'export-to-xlsx', label: 'Export .xlsx' },
            ],
            query: {
                status: this.queryParams.status,
                term: this.queryParams.term,
                month: this.queryParams.month,
            }
        };
    },
    methods: {
        createNewForm() {
            this.isCreateForm = true;
        },
        submitForm() {
            this.form.post(route('settings.currencies.store'), {
                onSuccess: () => this.closeModal(),
                //onFinish: () => this.form.reset('symbol', 'rates.*')
            });
        },
        editEnty(id){
            axios.get('/settings/currencies/'+ id)
                .then(response => {
                    this.form.id = id;
                    this.form.name = response.data.name;
                    this.form.rates = response.data.exchange_rates;
                    this.isEditForm = true;
                })
                .catch(error => {
                    console.log(error);
                }
            );
        },
        submitEditForm() {
            this.form.put(route('settings.currencies.update', this.form.id), {
                onSuccess: () => this.closeModal()
            });
        },
        deleteEnty(entryId) {
            if (confirm(`Are you sure?`)) {
                this.$inertia.delete(route('settings.currencies.destroy'), {
                    preserveState: false,
                    data: {
                        status: this.queryParams.status,
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
                        this.$inertia.delete(route('settings.currencies.destroy'), {
                            preserveState: false,
                            data: {
                                status: this.queryParams.status,
                                entryIds: entryIds,
                            },
                        });
                    }
                    break;

                case 'export-to-xlsx':
                    if (confirm(`Are you wish bulk items to .xlsx with selected?`)) {
                        window.open(`/settings/currencies/export/bulk-action?entryIds=${entryIds}`, '_blank');
                    }
                    break;
            }
        },
        filter() {
            this.$inertia.get(route('settings.currencies.index'), pickBy(this.query), {
                preserveState: true,
            });
        },
        closeModal: function() {
            this.isCreateForm = false;
            this.isEditForm = false;
            this.form.reset('name', 'rates');
            this.form.clearErrors('name', 'rates');
        },
        addRate: function() {
            this.form.rates.push({
                amount: 0,
            });
        },
        removeRate: function(index) {
            this.form.rates[index].deleted = 1;
            this.form.rates.push();
            //this.form.rates.splice(index, 1);
        },
    },
    computed: {
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

