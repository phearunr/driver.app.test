<template>
    <AppLayout title="Orders">
        <template #header>
            <div class="flex items-center space-x-2">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Orders</h2>
            </div>
        </template>
    <div class="p-8 mx-auto max-w-7xl">
      <section class="flex flex-col p-4 mb-4 space-y-4 bg-white shadow sm:rounded lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:p-2">
        <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-2">

          <select
            v-if="$page.props.user.permissions.includes('order store handle')"
            v-model="query.store"
            aria-label="store" id="type"
            class="pr-10 pl-3 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-44 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            >
            <option v-for="store in allStores" :value="store.value"> {{ store.label }}</option>
          </select>

          <select  v-model="query.status" aria-label="status" id="type" class="pr-10 pl-3 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-44 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            <option v-for="status in allStatuses" :value="status.value"> {{ status.label }}</option>
          </select>

          <select v-model="query.month" aria-label="month" id="date" class="pr-10 pl-3 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-44 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            <option v-for="month in allMonths" :value="month.value">{{ month.label }}</option>
          </select>

          <select v-model="query.rate" aria-label="rate" id="date" class="pr-10 pl-3 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-44 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            <option v-for="rate in allRates" :value="rate.value">{{ rate.label }}</option>
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
            <select-action
                v-if="$page.props.user.permissions.includes('order bulk action')"
                :actions="actions"
                @execute="executeAction"
            >
            </select-action>
            <button

                v-if="$page.props.user.permissions.includes('edit return stock')"
                type="button"
                @click="editExchangeRate(1)"
                class="inline-flex items-center px-4 h-11 font-medium text-gray-700 bg-white rounded border border-gray-300 shadow-sm lg:h-9 lg:text-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                Buyer
              </span>
            </th>
            <th class="hidden text-center lg:table-cell">
              <span class="inline-block p-2 font-normal text-blue-600 lg:text-sm">
                Store
              </span>
            </th>
            <th class="text-center lg:table-cell">
              <span class="p-2 font-normal text-blue-600 lg:text-sm">
                Status
              </span>
            </th>
            <th class="text-center lg:table-cell">
                <span class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500">
                    Date
                </span>
              </th>
            <th class="text-center lg:table-cell">
              <span class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500">
                Total QTY
              </span>
            </th>
            <th class="w-28 text-center lg:table-cell">
                <span class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500">
                    Delivery Fee
                </span>
            </th>
            <th class="w-28 text-center lg:table-cell">
                <span class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500">
                    Total Price
                </span>
            </th>
            <th class="w-28 text-center lg:table-cell">
                <span class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500">
                  Grand Total($)
                </span>
              </th>
                <th class=" w-40 text-center lg:table-cell">
                <span class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500">
                  Grand Total(៛)
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
                    <button @click="returnStock(entry.order_id)" class="text-xs text-red-600 rounded hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                      Return Stock
                    </button>
                    <span class="text-xs text-gray-300">|</span>
                    <a :href="route('stores.orders.download', {
                        'id': entry.order_id,
                        'store_id': entry.store_id,
                        'rate': query.rate
                    })" class="text-xs text-blue-600 rounded hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Export .xlsx
                    </a>
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
              <!-- <p class="text-xs text-gray-500 break-all">
                Confirm: {{ entry.confirm_cash_pay_time }}
              </p> -->
            </td>
            <td class="hidden p-2 text-right lg:table-cell">
              <span class="text-gray-600 lg:text-sm">
                {{  entry.order_date }}
              </span>
            </td>
            <td class="p-2 text-center lg:table-cell">
                <span class="text-gray-600 lg:text-sm">
                  {{  entry.total_quantity ?? 0 }}
                </span>
              </td>
            <td class="p-2 text-center lg:table-cell">
                <span class="text-gray-600 lg:text-sm">
                  {{  entry.total_delivery_fee ?? 0 }}
                </span>
            </td>
            <td class="p-2 text-center lg:table-cell">
                <span class="text-gray-600 lg:text-sm">
                  {{  entry.total_price ?? 0 }}
                </span>
            </td>
            <td class="p-2 text-center lg:table-cell">
                <span class="text-gray-600 lg:text-sm">
                  {{  entry.grand_total_price ?? 0 }}
                </span>
            </td>
            <td class="p-2 text-center lg:table-cell">
                <span class="text-gray-600 lg:text-sm">
                  {{  entry.grand_total_price_riel ?? 0 }}
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
<JetDialogModal :show="isStockReturn" @close="isStockReturn = false">
<template #title>
     Stock Returns
</template>

<template #content>
    <form @submit.prevent="submit">
        <fieldset class="mb-4">
            <div v-if="form.errors['order_sn']" class="text-xs mb-1 text-red-600">
                {{ form.errors['order_sn'] }}
            </div>
            <p v-else="form.errors[`products.0.return_quantity`]" class=" text-left text-xs mb-1 text-red-600">
                {{ form.errors[`products.0.return_quantity`]}}
            </p>
            <legend class="flex items-center space-x-2">
                <div class="space-x-2">
                    <span class="text-sm font-medium text-gray-400">Order no:</span>
                    <span>{{ form.order_sn }}, </span>
                </div>
                <div class="space-x-2">
                    <span class="text-sm font-medium text-gray-400">Total Quantity:</span>
                    <span>{{ form.total_quantity }}</span>
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
                            <input :class="{'border-red-300': form.errors[`products.${index}.return_quantity`] }" v-model="product.return_quantity" aria-label="`products ${index + 1} quantity`" type="number" class="w-full focus:ring-yellow-500 focus:border-yellow-500 block sm:text-sm border-gray-300"
                                placeholder="return qty">
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="w-full mt-2">
                <textarea v-model="form.remarked" class="w-full focus:ring-yellow-500 focus:border-yellow-500 block sm:text-sm border-gray-300" placeholder="remarked"></textarea>
            </div>

        </fieldset>
    </form>
</template>

<template #footer>
    <JetSecondaryButton @click="closeModal">
        Cancel
    </JetSecondaryButton>
    <JetButton
        class="ml-3" :class="{'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="sotreReturnStock()"
    >
        Save
    </JetButton>
</template>

</JetDialogModal>
<!-- / Return Form Modal -->

<!-- Export Form Modal -->
<JetDialogModal :show="isOpenExport" @close="isOpenExport = false">
<template #title>
     Stock Returns
</template>

<template #content>
    <form @submit.prevent="submit">
        <fieldset class="mb-4">
            <div class="w-full mt-2">
                <select
                    v-model="form.bulkExport" aria-label="store"
                    class="pr-10 pl-3 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-44 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option v-for="bulkExport in allbulkExports" :value="bulkExport.value"> {{ bulkExport.label }}</option>
                </select>
            </div>
        </fieldset>
    </form>
</template>

<template #footer>
    <JetSecondaryButton @click="closeModal">
        Cancel
    </JetSecondaryButton>
    <JetButton class="ml-3" :class="{'opacity-25': form.processing }" :disabled="form.processing" @click="BulkExport()">
        Export
    </JetButton>
</template>

    </JetDialogModal>
    <!-- / Export Form Modal -->
    <!-- Exchange Rates --->
    <DialogModal :show="true">

    </DialogModal>
    <JetDialogModal :show="isExchangeRateForm" @close="closeExchangeRateModal">
        <template #title>
            Currency
        </template>

        <template #content>
            <form @submit.prevent="submit">
                <div>
                    <label for="symbols">Name:</label>
                    <div class="mt-1">
                        <input
                            v-model="formCurrency.name"
                            disabled="disabled"
                            type="text" :class="{'border-red-500': formCurrency.errors.name }"
                            class="w-1/3 py-2 px-2 bg-gray-50 focus:ring-blue-600 block sm:text-sm border-gray-300"
                        />
                        <p v-if="formCurrency.errors.name" class="text-xs mt-1 text-red-600">{{formCurrency.errors.symbol}}</p>
                    </div>
                </div>
                <fieldset class="mt-2">
                    <div class="flex flex-row justify-between">
                        <div>
                            <legend class="flex item-center space-x-2">
                                <span>Exchange Rates</span>
                                <button @click="addRate()" type="button" class="bg-red-50 py-1 px-2 flex-shrink-0 text-sm leading-none font-medium text-red rounded-md border border-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-2 h-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </button>
                            </legend>
                        </div>
                    </div>
                    <div class="mt-2" v-for="(rate, index) in formCurrency.rates" key="index">
                        <div v-if="!rate.deleted" class="flex space-x-2 items-center">
                            <button @click="removeRate(index)" type="button" class="bg-white py-1 px-2 flex-shrink-0 text-xs leading-none font-medium text-red-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                            <div class="flex-1">
                                <input v-model="rate.amount"
                                :class="{'border-red-500': formCurrency.errors[`rates.${index}.amount`] }"
                                :aria-label="`rate ${index + 1} amount`"
                                type="number" placeholder="Amount"
                                class="w-full py-1 px-2 focus:ring-blue-500 block sm:text-sm border-gray-300" />
                            </div>
                            <!--
                                <div class="w-16 flex-shrink-0">
                                <input v-model="isDefault" value="radio-value" type="radio" />
                                </div>
                            -->
                        </div>
                        <p
                            v-if="formCurrency.errors[`rates.${index}.amount`]"
                            class="ml-10 text-xs mt-1 text-red-600">
                            {{ formCurrency.errors[`rates.${index}.amount`] }}
                        </p>
                    </div>
                </fieldset>
            </form>
        </template>

        <template #footer>
            <JetSecondaryButton @click="closeExchangeRateModal()">
                Cancel
            </JetSecondaryButton>
            <JetButton
                class="ml-3"
                :class="{ 'opacity-25': formCurrency.processing }"
                :disabled="formCurrency.processing"
                @click="submitExchangeRateForm">
                Save
            </JetButton>
        </template>

    </JetDialogModal>
    <!-- Exchange Rates --->

</template>

<script>
import { pickBy } from 'lodash';
import { Link,useForm } from '@inertiajs/inertia-vue3';

import AppLayout from '@/Layouts/AppLayout.vue';
import JetButton from '@/Jetstream/Button.vue';
import JetDialogModal from '@/Jetstream/DialogModal.vue';
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue';
import JetAuthenticationCard from '@/Jetstream/AuthenticationCard.vue';

import JetInput from '@/Jetstream/Input.vue';
import JetLabel from '@/Jetstream/Label.vue';

import Pagination from '@/Components/Pagination.vue';
import SelectAction from '@/Components/SelectAction.vue';
import Multiselect from 'vue-multiselect';

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
        Multiselect,
    },
    props: {
        entries: Object,
        statuses: Array,
        stores: Array,
        months: Array,
        rates: Array,
        pagers: Array,
        queryParams: Object,
    },
    data() {
        return {
            isStockReturn: false,
            isExchangeRateForm: false,
            pagerId: 15,
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
                remarked: null,
            }),
            formCurrency: useForm({
                id: null,
                name: null,
                rates: [],
            }),
            actions: [
                { id: null, label: 'Bulk actions' },
                { id: 'export-to-xlsx', label: 'Export .xlsx' },
            ],
            query: {
                status: this.queryParams.status,
                term: this.queryParams.term,
                store: this.queryParams.store,
                month: this.queryParams.month,
                rate: this.queryParams.rate,
                pager: this.queryParams.pager,
            }
        };
    },
    methods: {
        returnStock(order_id) {
            axios.get(route('stores.orders.show', order_id)).then(response => {
                console.log(order_id);
                this.form.order_id = response.data.order_id;
                this.form.order_sn = response.data.order_sn;
                this.form.order_state = response.data.order_state_id;
                this.form.buyer_id = response.data.buyer_id;
                this.form.buyer_name = response.data.buyer_name;
                this.form.store_id = response.data.store_id;
                this.form.store_name = response.data.store_name;
                this.form.total_quantity = response.data.total_quantity;
                this.form.products = response.data.products;

            }).catch(err => {
                console.error(err)
            }).finally(() => { this.isStockReturn = true });
        },
        sotreReturnStock() {
            this.form.post(route('stocks.stockreturnstemp.store'), {
                onSuccess: () => {
                    this.isStockReturn = false;
                },
                onError: (errors) => {
                    console.log(errors);
                },
            });
        },
        removeReturnStock(index) {
            this.form.products.splice(index, 1);
        },
        toggleSelectAll(e) {
            this.entries.data.forEach(entry => entry.selected = e.target.checked);
        },
        executeAction(actionId) {
            const entryIds = this.entries.data.filter(entry => entry.selected)
                .map(entry => entry.order_id);

            if (!entryIds.length) return;

            switch (actionId) {
                case 'export-to-xlsx':
                    if (confirm(`Are you wish  bulk items selected to export .xlsx?`)) {
                        window.open(`/stores/orders/export/bulk-action?entryIds=${entryIds}`, '_blank');
                    }
                    break;
            }
        },
        filter() {
            this.$inertia.get(route('stores.orders.index'), pickBy(this.query), {
                preserveState: true,
            });
        },
        openModal() {
            this.isOpen = true;
        },
        closeModal() {
            this.isStockReturn = false;
            this.isOpenExport = false;
            // Clear all errors
            this.form.clearErrors()
            this.form.reset('remarked')
        },
        editExchangeRate(id) {
            axios.get('/settings/currencies/'+ id)
                .then(response => {
                    this.formCurrency.id = id;
                    this.formCurrency.name = response.data.name;
                    this.formCurrency.rates = response.data.exchange_rates;
                    this.isExchangeRateForm = true;
                })
                .catch(error => {
                    console.log(error);
                }
            );
        },
        submitExchangeRateForm(){
            this.formCurrency.put(route('stores.orders.exchangeRate',
            this.formCurrency.id), {
                onSuccess: () => this.closeExchangeRateModal()
            });
        },
        addRate() {
            this.formCurrency.rates.push({
                amount: 0,
            });
        },
        removeRate(index) {
            if(this.formCurrency.rates[index].id){
                this.formCurrency.rates[index].deleted = 1;
                this.formCurrency.rates.push();
            }else{
                this.formCurrency.rates.splice(index, 1);
            }
        },
        closeExchangeRateModal() {
            this.isExchangeRateForm = false;
            this.formCurrency.reset('name', 'rates');
            this.formCurrency.clearErrors('name', 'rates');
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
        allRates() {
            return [
                { value: null, label: 'Any 1$ to Riel' },
                ...this.rates,
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

