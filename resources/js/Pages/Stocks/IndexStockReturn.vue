<template>
    <AppLayout title="Stock Returns">
        <template #header>
            <div class="flex items-center space-x-2">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Stock Returns </h2>
            </div>
        </template>

        <div class="p-8 mx-auto max-w-7xl">
        <section class="flex flex-col p-4 mb-4 space-y-4 bg-white shadow sm:rounded lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:p-2">
            <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-2">

            <select
                v-if="$page.props.user.permissions.includes('stock store handle')"
                v-model="query.store"
                aria-label="store" id="type"
                class="pr-10 pl-3 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-44 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option v-for="store in allStores" :value="store.value"> {{ store.label }}</option>
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
                v-if="$page.props.user.permissions.includes('stock bulk action')"
                :actions="actions"
                @execute="executeAction">
            </select-action>
            <!--
                <button type="button" class="inline-flex items-center px-4 h-11 font-medium text-gray-700 bg-white rounded border border-gray-300 shadow-sm lg:h-9 lg:text-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>
            -->
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
                    Buyer
                </span>
                </th>
                <th class="hidden text-left lg:table-cell">
                <span class="inline-block p-2 font-normal text-blue-600 lg:text-sm">
                    Store
                </span>
                </th>
                <th class="hidden text-left lg:table-cell">
                <span class="inline-block p-2 font-normal text-blue-600 lg:text-sm">
                    Status
                </span>
                </th>
                <th class="text-left lg:table-cell">
                    <span class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500">
                        Total Qty
                    </span>
                </th>
                <th class="hidden text-left lg:table-cell">
                    <span class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500">
                         Date
                    </span>
                </th>
                <th class="hidden w-28 text-left lg:table-cell">
                    <span class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500">
                        Authorized by
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
                    <img :src="entry.preview_url" class="object-cover">
                    </div>
                    <div>
                    <span class="text-sm font-semibold text-blue-600 break-all rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        {{ entry.buyer_name }}
                    </span>
                    <p class="text-xs text-gray-500 break-all">
                        {{ entry.order_sn }}
                    </p>

                    <div class="flex items-center mt-2 space-x-2 md:invisible group-hover:visible">
                        <button
                            v-if="$page.props.user.permissions.includes('edit return stock')"
                            @click="editItems(entry.id)"
                            class="text-xs text-blue-600 rounded hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            Edit
                        </button>
                        <span class="text-xs text-gray-300">|</span>
                        <button
                            v-if="$page.props.user.permissions.includes('delete return stock')"
                            @click="deleteEnty(entry.id)"
                            class="text-xs text-red-600 rounded hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Delete
                        </button>
                        <span class="text-xs text-gray-300">|</span>

                        <button
                            v-if="$page.props.user.permissions.includes('view return stock')"
                            @click="show(entry.id)"
                            class="text-xs text-blue-600 rounded hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            View
                        </button>
                    </div>
                    </div>
                </div>
                </td>
                <td class="hidden p-2 text-left lg:table-cell">
                <span class="text-gray-600 lg:text-sm">
                    {{  entry.store_name ?? entry.store_id }}
                </span>
                </td>
                <td class="hidden p-2 text-left lg:table-cell">
                <a href="#" class="text-blue-600 rounded lg:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    {{ entry.order_state }}
                </a>
                </td>

                <td class="p-2 text-left lg:table-cell">
                    <span class="text-gray-600 lg:text-sm">
                        {{  entry.total_quantity ?? 0 }}
                    </span>
                </td>
                <td class="hidden p-2 text-left lg:table-cell">
                    <span class="text-gray-600 lg:text-sm">
                        {{  entry.created_at }}
                    </span>
                    </td>
                <td class="hidden p-2 text-left lg:table-cell">
                    <span class="text-gray-600 lg:text-sm">
                        {{  entry.updated_by.name}}
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
<!-- Return Form Modal -->
<JetDialogModal :show="isOpen" @close="isOpen = false">
<template #title>
     Stock Returns
</template>

<template #content>
    <form @submit.prevent="submit">
        <fieldset class="mb-4">
            <p
                v-if="form.errors[`products.0.return_quantity`]"
                class=" text-left text-xs mb-2 text-red-600">
                {{ form.errors[`products.0.return_quantity`]}}
            </p>
            <legend class="flex items-center space-x-2">
                <div class="space-x-2">
                    <span class="text-sm font-medium text-gray-400">Order Number:</span>
                    <span>{{ form.order_sn }}, </span>
                </div>
                <div class="space-x-2">
                    <span class="text-sm font-medium text-gray-400">Total Quantity:</span>
                    <span>{{ form.total_quantity }}</span>
                </div>
                <div v-if="form.errors['order_sn']" class="pl-0 text-xs mt-1 text-red-600">
                    {{ form.errors['order_sn'] }}
                </div>
            </legend>

            <table class="min-w-full table-fixed ">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left text-xs mt-1 text-gray-600 lg:table-cell">Product Name</th>
                        <th class="w-20 text-center text-xs mt-1 text-gray-600 lg:table-cell">Unit Price</th>
                        <th class=" w-20 text-center text-xs mt-1 text-gray-600 lg:table-cell">QTY</th>
                        <th class=" w-20 text-center text-xs mt-1 text-gray-600 lg:table-cell">Return QTY</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="(product, index) in form.products" key="index" class="align-top group gap-4">

                        <td class="pt-2 lg:table-cell">
                            <input disabled="disabled" v-model="product.name" aria-label="`products ${index + 1} id`" type="text" class="w-full bg-gray-50 focus:ring-yellow-500 focus:border-yellow-500 block sm:text-sm border-gray-300" placeholder="product name">
                        </td>
                        <td class="pt-2 lg:table-cell">
                            <input disabled="disabled" v-model="product.unit_price" aria-label="`products ${index + 1} unit_price`" type="number" step="any" class="w-full bg-gray-50 focus:ring-yellow-500 focus:border-yellow-500 block sm:text-sm border-gray-300" placeholder="unit price">
                        </td>
                        <td class="pt-2 lg:table-cell">
                            <input disabled="disabled" v-model="product.quantity" aria-label="`products ${index + 1} quantity`" type="number" class="w-full bg-gray-50 focus:ring-yellow-500 focus:border-yellow-500 block sm:text-sm border-gray-300" placeholder="qty">
                        </td>
                        <td class="pt-2 lg:table-cell">
                            <input v-if="!isViewAction"
                                :class="{'border-red-300':form.errors[`products.${index}.return_quantity`]}"
                                v-model="product.return_quantity"
                                aria-label="`products ${index + 1} return_quantity`"
                                type="number"
                                class="w-full focus:ring-yellow-500 focus:border-yellow-500 block sm:text-sm border-gray-300"
                                placeholder="return qty">
                                <input v-else
                                disabled="disabled"
                                v-model="product.return_quantity"
                                aria-label="`products ${index + 1} return_quantity`"
                                type="number"
                                class="w-full bg-gray-50 focus:ring-yellow-500 focus:border-yellow-500 block sm:text-sm border-gray-300"
                                placeholder="return qty">
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="w-full mt-2">
                <textarea v-model="form.remarked" disabled="disabled" class="w-full bg-gray-50 focus:ring-yellow-500 focus:border-yellow-500 block sm:text-sm border-gray-300" placeholder="remarked"></textarea>
            </div>

        </fieldset>
    </form>
</template>

<template #footer>
    <JetSecondaryButton @click="closeModal">
        Cancel
    </JetSecondaryButton>
    <JetButton v-if="!isViewAction" class="ml-3" :class="{
        'opacity-25': form.processing }" :disabled="form.processing" @click="updateReturnStock()">
        Save
    </JetButton>
</template>
    </JetDialogModal>
      <!-- / Return Form Modal -->
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { pickBy } from 'lodash';

// import { createToaster } from "@meforma/vue-toaster";

import JetButton from '@/Jetstream/Button.vue';

import JetDialogModal from '@/Jetstream/DialogModal.vue';

import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue';
import JetAuthenticationCard from '@/Jetstream/AuthenticationCard.vue';

import JetInput from '@/Jetstream/Input.vue';
import JetLabel from '@/Jetstream/Label.vue';

import Pagination from '@/Components/Pagination.vue';
import SelectAction from '@/Components/SelectAction.vue';

export default {
    components: {
        AppLayout,
        Pagination,
        SelectAction,
        JetButton,
        JetDialogModal,
        JetSecondaryButton,
        JetAuthenticationCard,
        JetInput,
        JetLabel
    },
    props: {
        entries: Object,
        statuses: Array,
        stores: Array,
        months: Array,
        queryParams: Object,
    },
    data() {
        return {
            isOpen: false,
            isViewAction: false,
            form: this.$inertia.form({
                order_id: null,
                order_sn: null,
                order_state: null,
                buyer_id: null,
                buyer_name: null,
                store_id: null,
                store_name: null,
                total_quantity: null,
                products: [],
                remarked: null
            }),

            actions: [
                { id: null, label: 'Bulk actions' },
                // { id: 'delete', label: 'Delete' },
                { id: 'export-to-xlsx', label: 'Export .xlsx' },
            ],
            query: {
                status: this.queryParams.status,
                term: this.queryParams.term,
                store: this.queryParams.store,
                month: this.queryParams.month,
            }
        };
    },
    methods: {
        editItems(entry) {

            axios.get(route('stocks.stockreturnstemp.show', entry)).then(res => {

                let result = res.data;
                this.form.order_id = result.order_id;
                this.form.order_sn = result.order_sn;
                this.form.order_state = result.order_state;

                this.form.buyer_id = result.buyer_id;

                this.form.buyer_name = result.buyer_name;

                this.form.store_id = result.store_id;
                this.form.store_name = result.store_name;
                this.form.total_quantity = result.total_quantity;
                this.form.remarked = result.remarked;
                this.form.products = Object.assign({}, result.products);

            }).catch(err => {
                console.error(err)
            }).finally(() => {
                this.isOpen = true
                this.isViewAction = false;
            });

        },
        show(entry) {

            axios.get(route('stocks.stockreturnstemp.show', entry)).then(res => {

                let result = res.data;
                this.form.order_id = result.order_id;
                this.form.order_sn = result.order_sn;
                this.form.order_state = result.order_state;

                this.form.buyer_id = result.buyer_id;
                this.form.buyer_name = result.buyer_name;

                this.form.store_id = result.store_id;
                this.form.store_name = result.store_name;
                this.form.total_quantity = result.total_quantity;
                this.form.remarked = result.remarked;
                this.form.products = Object.assign({}, result.products);

            }).catch(err => {
                console.error(err)
            }).finally(() => {
                this.isOpen = true;
                this.isViewAction = true;
            });

        },
        updateReturnStock() {
            this.form.put(route('stocks.stockreturnstemp.update'), {
                onSuccess: (page) => {
                    this.finishSubmit({ message: 'sucessed!.' });
                },
                onError: (errors) => {
                    console.log(errors);
                },
            });
        },
        deleteEnty(entryId) {
            if (confirm(`Are you sure?`)) {
                this.$inertia.delete(route('stocks.stockreturnstemp.destroy'), {
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
                        this.$inertia.delete(route('stocks.stockreturnstemp.destroy'), {
                            preserveState: false,
                            data: {
                                entryIds: entryIds,
                            },
                        });
                    }
                    break;

                case 'export-to-xlsx':
                    if (confirm(`Are you wish  bulk items selected to export .xlsx?`)) {
                       window.open(`/stocks/stock-returns-tem-bulk-export?entryIds=${entryIds}`, '_blank');
                    }
                    break;
            }
        },
        filter() {
            this.$inertia.get(route('stocks.stockreturnstemp.index'), pickBy(this.query), {
                preserveState: true,
            });
        },
        openModal: function() {
            this.isOpen = true;
        },
        closeModal: function() {
            this.isOpen = false;
            // Clear all errors
            this.form.clearErrors()
        },
        finishSubmit: function(response) {
            // const toaster = createToaster();
            // toaster.success(response.message);
            this.closeModal();
        }
    },
    computed: {
        allStores() {
            return [
                { value: null, label: 'Any store' },
                ...this.stores,
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

