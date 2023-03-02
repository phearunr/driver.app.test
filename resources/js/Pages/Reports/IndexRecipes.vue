<template>
    <AppLayout title="Recipes">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Recipes
            </h2>
        </template>

        <div class="py-5">
            <div class="p-8 mx-auto max-w-7xl">
                <section class="flex flex-col p-4 mb-4 space-y-4 bg-white shadow sm:rounded lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:p-2">
                    <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-2">

                    <select v-model="query.authorizedBy" aria-label="status" id="type" class="pr-10 pl-3 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-44 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option v-for="authorizedBy in allAuthorizedBys" :value="authorizedBy.value"> {{ authorizedBy.label }}</option>
                    </select>

                    <!-- <select v-model="query.status" aria-label="status" id="type" class="pr-10 pl-3 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-44 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option v-for="status in allStatuses" :value="status.value"> {{ status.label }}</option>
                    </select> -->

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
                        <select
                            v-model="query.pager"
                            @change="filter()"
                            aria-label="pager"
                            class="pr-10 pl-3  w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-20 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option v-for="pager in allPagers" :value="pager.value"> {{ pager.label }}</option>
                        </select>
                        <select-action :actions="actions" @execute="executeAction"></select-action>
                        <!-- <button
                           @click="createNewForm"
                            class="inline-flex items-center px-4 h-11 font-medium text-gray-700 bg-white rounded border border-gray-300 shadow-sm lg:h-9 lg:text-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button> -->
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
                                Numbers
                            </span>
                        </th>
                        <th class="hidden text-left lg:table-cell">
                            <span class="hidden p-2 font-normal text-blue-600 lg:text-sm">
                                Order Numbers
                            </span>
                        </th>
                        <th class="hidden text-left lg:table-cell">
                            <span class="p-2 font-normal text-blue-600 lg:text-sm">
                                Order Dates
                            </span>
                        </th>
                        <th class="text-left lg:table-cell">
                            <span class="p-2 font-normal text-blue-600 lg:text-sm">
                                Rates
                            </span>
                        </th>
                        <th class="hidden text-left lg:table-cell">
                            <span class="p-2 font-normal text-blue-600 lg:text-sm">
                                Total QTY
                            </span>
                        </th>
                        <th class="text-left lg:table-cell">
                            <span class="p-2 font-normal text-blue-600 lg:text-sm">
                                Grand Total($)
                            </span>
                        </th>
                        <th class="text-left lg:table-cell">
                            <span class="p-2 font-normal text-blue-600 lg:text-sm">
                                Grand Total(៛)
                            </span>
                        </th>
                        <th class="hidden w-24 text-left lg:table-cell">
                            <span class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500">
                                 Date
                            </span>
                        </th>

                        <th class="hidden text-left lg:table-cell">
                            <span class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500">
                                Downloads
                            </span>
                        </th>
                        <th class="hidden text-left lg:table-cell">
                            <span class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500">
                                Authorized by
                            </span>
                        </th>
                        <!-- <th class="hidden w-28 text-left lg:table-cell">
                            <span class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500">
                                Status
                            </span>
                        </th> -->

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
                                {{ entry.recipe_number }}
                            </span>

                            <div class="flex items-center mt-2 space-x-2 md:invisible group-hover:visible">
                                <button @click="edit(entry.id)" class="text-xs text-blue-600 rounded hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Edit
                                </button>
                                <span class="text-xs text-gray-300">|</span>
                                <button @click="delete(entry.id)" class="text-xs text-red-600 rounded hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Export .xlsx
                                </button>
                                <span class="text-xs text-gray-300">|</span>
                                <button @click="view(entry.id)" class="text-xs text-blue-600 rounded hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    View
                                </button>
                            </div>

                            </div>
                        </div>
                        </td>
                        <td class="hidden p-2 text-left lg:table-cell">
                            <p class="text-sm text-red-400 break-all">
                                {{ entry.order_sn }}
                            </p>
                        </td>
                        <td class="hidden p-2 text-left lg:table-cell">
                            <span class="text-blue-600 lg:text-sm">
                                {{  entry.order_date}}
                            </span>
                        </td>
                        <td class="p-2 text-left lg:table-cell">
                            <span class="text-blue-600 lg:text-sm">
                                {{  entry.exchange_rate}}
                            </span>
                        </td>

                        <td class="hidden p-2 text-left lg:table-cell">
                            <span class="text-blue-600 lg:text-sm">
                                {{  entry.total_quantity}}
                            </span>
                        </td>
                        <td class="p-2 text-left lg:table-cell">
                            <span class="text-blue-600 lg:text-sm">
                                {{  entry.grand_total_price}}
                            </span>
                        </td>
                        <td class="p-2 text-left lg:table-cell">
                            <span class="text-blue-600 lg:text-sm">
                                {{  entry.grand_total_price_riel}}
                            </span>
                        </td>
                        <td class="hidden p-2 text-right lg:table-cell">
                            <span class="text-gray-600 lg:text-xs">
                                {{  entry.created_at}}
                            </span>
                        </td>
                        <td class="hidden p-2 text-center lg:table-cell">
                            <span class="text-blue-600 lg:text-sm">
                                {{  entry.downloaded}}
                            </span>
                        </td>
                        <td class="hidden p-2 text-left lg:table-cell">
                            <span class="text-gray-600 lg:text-sm">
                                {{  entry.authorizedby.name ?? null}}
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
        <DialogModal :show="isViewForm" @close="closeModal">
            <template #title>
                Recipe
            </template>

            <template #content>
                <div class="flex flex-row justify-between mb-4">
                    <div>
                        <div>អតិថិជនៈ <span class="text-sm text-gray-600">{{ form.buyer_name }}</span></div>
                        <div>លេខទូរស័ព្ទៈ <span class="text-sm text-gray-600">{{ form.bayer_mobile }}</span> </div>
                        <div>អាសយដ្ឋានៈ <span class="text-sm text-gray-600">{{form.buyer_address }}</span></div>
                    </div>
                    <div>
                        <div>លេខរៀងៈ  <span class="text-sm text-gray-600">{{ form.recipe_number }}</span> </div>
                        <div>លេខយោងៈ<span class="text-sm text-gray-600">{{ form.order_sn }}</span></div>
                        <div>អត្រាប្ដូរប្រាក់ $1= KHR <span class="text-lg text-red-600"> {{form.exchange_rate}} </span> </div>
                    </div>
                </div>
        <div class="wrap-table">

        <table class="border-collapse w-full">
            <thead>
                <tr>
                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300 hidden lg:table-cell">ល.រ</th>
                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300 hidden lg:table-cell">ឈ្មោះទំនិញ</th>
                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300 hidden lg:table-cell">ចំនួន</th>
                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300 hidden lg:table-cell">តម្លៃ​ឯកតា</th>
                    <th class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300 hidden lg:table-cell">សរុប</th>
                </tr>
            </thead>
            <tbody>
                <tr  v-for="(item, index) in form.recipe_items" :key="item.id"
                    class="bg-white lg:hover:bg-gray-100 flex lg:table-row flex-row lg:flex-row flex-wrap lg:flex-no-wrap mb-10 lg:mb-0">
                    <td class="w-full lg:w-auto p-3 text-gray-800 text-center border border-b block lg:table-cell relative lg:static">
                        {{ index+1  }}
                    </td>
                    <td class="w-full lg:w-auto p-3 text-gray-800  border border-b  text-sm text-justify block lg:table-cell relative lg:static">
                        {{ item.product_name }}
                    </td>
                    <td class="w-full lg:w-auto p-3 text-gray-800  border border-b text-center block lg:table-cell relative lg:static">
                       {{ item.quantity }}
                    </td>
                    <td class="w-full lg:w-auto p-3 text-gray-800  border border-b text-center block lg:table-cell relative lg:static">
                       {{ item.total_price }}
                    </td>
                    <td class="w-full lg:w-auto p-3 text-gray-800  border border-b text-center block lg:table-cell relative lg:static">
                      {{ item.total_price }}
                    </td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td class="w-full lg:w-auto p-3 text-gray-800 border border-b text-center block lg:table-cell relative lg:static">
                        សរុបរួម ($)
                      </td>
                      <td class="w-full lg:w-auto p-3 text-gray-800 border border-b text-center block lg:table-cell relative lg:static">
                        {{ form.grand_total_price }}
                      </td>

                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td class="w-full text-sm lg:w-auto p-3 text-gray-800 border border-b text-center block lg:table-cell relative lg:static">
                        សរុបរួម (KHR)
                      </td>
                    <td class="w-full text-sm lg:w-auto p-3 text-gray-800 border border-b text-center block lg:table-cell relative lg:static">
                        {{ form.grand_total_price_riel }}
                      </td>
                </tr>

                <tr>
                    <td colspan="3"><b>បរិយាយ:</b></td>
                </tr>
                <tr>
                    <td colspan="5" class="w-full h-10 lg:w-auto p-3 text-gray-800 border border-b text-center block lg:table-cell relative lg:static">
                    {{ form.comment }}
                    </td>
                </tr>

                </tbody>
                    </table>
                </div>
                </template>

                <template #footer>
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>
                    <!-- <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="submitForm">
                        Export .xlsx
                    </PrimaryButton> -->
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
        SecondaryButton
    },
    props: {
        entries: Object,
        authorizedBys: Array,
        statuses: Array,
        months: Array,
        pagers: Array,
        queryParams: Object,
    },
    data() {
        return {
            isViewForm: false,
            isEditForm: false,
            form: useForm({
                id: null,
                recipe_number: null,
                store_name: null,
                buyer_name: null,
                bayer_mobile: null,
                buyer_address: null,
                order_sn: null,
                order_date: null,
                exchange_rate: null,
                recipe_items: [],
                grand_total_price: null,
                grand_total_price_riel: null,
                comment: null
            }),

            actions: [
                { id: null, label: 'Bulk actions' },
                { id: 'export-to-xlsx', label: 'Export .xlsx' },
            ],
            query: {
                authorizedBy:this.queryParams.authorizedBy,
                status: this.queryParams.status,
                term: this.queryParams.term,
                month: this.queryParams.month,
                pager: this.queryParams.pager,
            }
        };
    },
    methods: {
        view(id) {
            this.isViewForm = true;
            axios.get('/reports/recipes/'+ id)
                .then(response => {
                    this.form.id = id;
                    this.form.recipe_number = response.data.recipe_number;
                    this.form.store_name = response.data.store_name;
                    this.form.buyer_name = response.data.buyer_name;
                    this.form.bayer_mobile = response.data.bayer_mobile;
                    this.form.buyer_address = response.data.buyer_address;
                    this.form.order_sn = response.data.order_sn;
                    this.form.order_date = response.data.order_date;
                    this.form.exchange_rate = response.data.exchange_rate;
                    this.form.recipe_items = response.data.recipe_items;
                    this.form.grand_total_price = response.data.grand_total_price;
                    this.form.grand_total_price_riel = response.data.grand_total_price_riel;
                    this.form.comment = response.data.comment;
                })
                .catch(error => {
                    console.log(error);
                }
            );
        },
        submitForm() {
            this.form.post(route('settings.currencies.store'), {
                onSuccess: () => this.closeModal(),
                //onFinish: () => this.form.reset('symbol', 'rates.*')
            });
        },
        edit(id) {
            this.isEditForm = true;
        },
        deleteEnty(entryId) {
            if (confirm(`Are you sure?`)) {
                this.$inertia.delete(route('settings.currencies.destroy'), {
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
                        this.$inertia.delete(route('settings.currencies.destroy'), {
                            preserveState: false,
                            data: {
                                entryIds: entryIds,
                            },
                        });
                    }
                    break;

                case 'export-to-xlsx':
                    if (confirm(`Are you wish bulk items to .xlsx with selected?`)) {
                        window.open(`/reports/recipes/export/bulk-action?entryIds=${entryIds}`, '_blank');
                    }
                    break;
            }
        },
        filter() {
            this.$inertia.get(route('reports.recipes.index'), pickBy(this.query), {
                preserveState: true,
            });
        },
        closeModal: function() {
            this.isViewForm = false;
            this.isEditForm = false;
            // this.form.reset('symbol', 'rates');
            // this.form.clearErrors('symbol', 'rates');
        },
        addRate: function() {
            this.form.rates.push({
                amount: 0,
                isDefault: 0
            });

        },
        removeRate: function(index) {
            this.form.rates.splice(index, 1);
        },
    },
    computed: {
        allAuthorizedBys() {
            return [
                { value: null, label: 'Authorized by' },
                ...this.authorizedBys,
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
        allPagers() {
            return [
                { value: null, label: '15' },
                ...this.pagers,
            ];
        },
    },
};
</script>

