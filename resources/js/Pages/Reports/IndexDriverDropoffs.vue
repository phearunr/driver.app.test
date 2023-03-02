<template>
    <AppLayout title="Driver Dropoffs">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Driver Dropoffs
            </h2>
        </template>

        <div class="py-5">
            <div class="p-8 mx-auto max-w-7xl">
                <section
                    class="flex flex-col p-4 mb-4 space-y-4 bg-white shadow sm:rounded lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:p-2"
                >
                    <div
                        class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-2"
                    >
                        <select
                            v-if="
                                $page.props.user.permissions.includes(
                                    'scan out by filter'
                                )
                            "
                            v-model="query.scanOutBy"
                            aria-label="scan out by"
                            id="type"
                            class="pr-10 pl-3 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-44 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option
                                v-for="scanOutBy in allScanOutBys"
                                :value="scanOutBy.value"
                            >
                                {{ scanOutBy.label }}
                            </option>
                        </select>

                        <select
                            v-if="
                                $page.props.user.permissions.includes(
                                    'dropoff by filter'
                                )
                            "
                            v-model="query.dropoffBy"
                            aria-label="scan out by"
                            id="type"
                            class="pr-10 pl-3 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-44 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option
                                v-for="dropoffBy in allDropoffBys"
                                :value="dropoffBy.value"
                            >
                                {{ dropoffBy.label }}
                            </option>
                        </select>

                        <select
                            v-model="query.status"
                            aria-label="status"
                            id="type"
                            class="pr-10 pl-3 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-44 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option
                                v-for="status in allStatuses"
                                :value="status.value"
                            >
                                {{ status.label }}
                            </option>
                        </select>

                        <select
                            v-model="query.month"
                            aria-label="Media date"
                            id="date"
                            class="pr-10 pl-3 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-44 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option
                                v-for="month in allMonths"
                                :value="month.value"
                            >
                                {{ month.label }}
                            </option>
                        </select>
                        <button
                            @click="filter()"
                            type="button"
                            class="inline-flex items-center px-4 h-11 font-medium text-gray-700 bg-white rounded border border-gray-300 shadow-sm lg:h-9 lg:text-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Filter
                        </button>
                    </div>
                    <div class="flex flex-col">
                        <label
                            for="search"
                            class="text-sm font-medium text-gray-700 sr-only"
                            >Search</label
                        >
                        <input
                            v-model="query.term"
                            @keydown.enter="filter()"
                            type="search"
                            id="search"
                            class="w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm lg:w-64 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Search for..."
                            autocomplete="off"
                        />
                    </div>
                </section>

                <section
                    class="flex flex-col items-center space-y-4 mb-4 md:space-y-0 md:flex-row md:justify-between"
                >
                    <div class="flex flex-row space-x-2">
                        <select
                            v-model="query.pager"
                            @change="filter()"
                            aria-label="pager"
                            class="pr-10 pl-3 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-20 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option
                                v-for="pager in allPagers"
                                :value="pager.value"
                            >
                                {{ pager.label }}
                            </option>
                        </select>
                        <select-action
                            v-if="
                                $page.props.user.permissions.includes(
                                    'driver bulk actions'
                                )
                            "
                            :actions="actions"
                            @execute="executeAction"
                        >
                        </select-action>
                        <button
                            v-if="
                                $page.props.user.permissions.includes(
                                    'recipe scanner'
                                )
                            "
                            @click="scannerForm"
                            class="inline-flex items-center px-4 h-11 font-medium text-gray-700 bg-white rounded border border-gray-300 shadow-sm lg:h-9 lg:text-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-4 h-4"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"
                                />
                            </svg>
                        </button>
                    </div>
                    <pagination :pagination="entries.meta"></pagination>
                </section>

                <section class="mb-4">
                    <table
                        class="min-w-full bg-white shadow table-fixed sm:rounded"
                    >
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-2 w-10 text-center">
                                    <input
                                        type="checkbox"
                                        @change="toggleSelectAll"
                                        class="w-6 h-6 text-blue-600 rounded border-gray-300 lg:w-4 lg:h-4 focus:ring-blue-500"
                                    />
                                </th>
                                <th class="text-left">
                                    <span
                                        class="flex w-44 relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500"
                                    >
                                        Order Numbers
                                    </span>
                                </th>
                                <th class="hidden text-left lg:table-cell">
                                    <span
                                        class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500"
                                    >
                                        Scan out by
                                    </span>
                                </th>
                                <th class="hidden text-left lg:table-cell">
                                    <span
                                        class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500"
                                    >
                                        Drop off by
                                    </span>
                                </th>
                                <th class="lg:table-cell">
                                    <span
                                        class="flex text-center te relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500"
                                    >
                                        Ordered QTY
                                    </span>
                                </th>

                                <th class="lg:table-cell">
                                    <span
                                        class="flex text-center te relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500"
                                    >
                                        Dropoffed QTY
                                    </span>
                                </th>
                                <th class="hidden text-left lg:table-cell">
                                    <span
                                        class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500"
                                    >
                                        Comments
                                    </span>
                                </th>
                                <th class="w-24 text-left lg:table-cell">
                                    <span
                                        class="flex relative z-10 items-center p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500"
                                    >
                                        Status
                                    </span>
                                </th>
                                <!-- <th>-</th> -->
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr
                                class="align-top group"
                                v-for="(entry, index) in entries.data"
                                :key="entry.id"
                            >
                                <td class="p-2 w-10 text-center">
                                    <input
                                        type="checkbox"
                                        v-model="entry.selected"
                                        class="w-6 h-6 text-blue-600 rounded border-gray-300 lg:w-4 lg:h-4 focus:ring-blue-500"
                                    />
                                </td>
                                <td class="p-2 text-left">
                                    <div class="flex space-x-4">
                                        <div>
                                            <span
                                                class="text-sm font-semibold text-red-600 break-all rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            >
                                                {{ entry.recipe_numbers }}
                                            </span>

                                            <div
                                                class="flex items-center mt-2 space-x-2 md:invisible group-hover:visible"
                                            >
                                                <button
                                                    @click="edit(entry.id)"
                                                    class="text-xs text-blue-600 rounded hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                >
                                                    Edit
                                                </button>
                                                <span
                                                    class="text-xs text-gray-300"
                                                    >|</span
                                                >
                                                <button
                                                    @click="delete entry.id"
                                                    class="text-xs text-red-600 rounded hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                >
                                                    Export .xlsx
                                                </button>
                                                <span
                                                    class="text-xs text-gray-300"
                                                    >|</span
                                                >
                                                <button
                                                    @click="view(entry.id)"
                                                    class="text-xs text-blue-600 rounded hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                >
                                                    View
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td
                                    class="hidden p-2 text-left lg:table-cell whitespace-nowrap"
                                >
                                    <UserProfile
                                        :user="entry.scanouter_by"
                                        :date="entry.scanouted_at"
                                    />
                                </td>
                                <td
                                    class="hidden p-2 text-left lg:table-cell whitespace-nowrap"
                                >
                                    <UserProfile
                                        :user="entry.dropoffer_by"
                                        :date="entry.dropoffed_at"
                                    />
                                </td>
                                <td class="text-center p-2 lg:table-cell">
                                    <span class="text-blue-600 lg:text-sm">
                                        {{ entry.ordered_quantity }}
                                    </span>
                                </td>

                                <td class="text-center p-2 lg:table-cell">
                                    <span class="text-blue-600 lg:text-sm">
                                        {{ entry.droffed_quantity }}
                                    </span>
                                </td>
                                <td class="hidden p-2 text-left lg:table-cell">
                                    <span class="text-blue-600 lg:text-sm">
                                        {{ entry.comments }}
                                    </span>
                                </td>
                                <td class="p-2 text-center lg:table-cell">
                                    <span class="text-red-600 lg:text-sm">
                                        {{ entry.status }}
                                    </span>
                                </td>
                                <!-- <td class="p-2 text-center lg:table-cell">
                                    <MenuDropDown @click="updateDropoffStatus(entry.id)"/>
                                </td> -->
                            </tr>
                            <tr class="align-top" v-if="!entries.data.length">
                                <td
                                    colspan="4"
                                    class="p-2 text-sm text-gray-700"
                                >
                                    No entries found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>

                <section
                    class="flex flex-col items-center space-y-4 mb-4 md:space-y-0 md:flex-row md:justify-between"
                >
                    <select-action
                        v-if="
                            $page.props.user.permissions.includes(
                                'driver bulk actions'
                            )
                        "
                        :actions="actions"
                        @execute="executeAction"
                    >
                    </select-action>
                    <pagination :pagination="entries.meta"></pagination>
                </section>
            </div>
        </div>
        <!--scanner-->
        <DialogModal :show="isScannerForm" @close="closeModal">
            <template #title> Recipe Scanner </template>
            <template #content>
                <!--panel search -->
                <div class="flex flex-grow justify-between">
                    <div class="flex flex-row">
                        <div class="flex flex-col">
                            <label
                                for="search"
                                class="text-sm font-medium text-gray-700 sr-only"
                                >Search</label
                            >
                            <input
                                v-model="term"
                                @keydown.enter="recipe_filter()"
                                type="search"
                                id="recipe_search"
                                class="w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm lg:w-64 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Search for..."
                                autocomplete="off"
                            />
                        </div>
                        <div class="ml-2">
                            <button
                                @click="scannerForm"
                                class="inline-flex items-center px-4 h-11 font-medium text-gray-700 bg-white rounded border border-gray-300 shadow-sm lg:h-9 lg:text-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-4 h-4"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M7.5 3.75H6A2.25 2.25 0 003.75 6v1.5M16.5 3.75H18A2.25 2.25 0 0120.25 6v1.5m0 9V18A2.25 2.25 0 0118 20.25h-1.5m-9 0H6A2.25 2.25 0 013.75 18v-1.5M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <!-- <div>
                        <select v-model="query.dropoffBy" aria-label="scan out by" id="type" class="pr-10 pl-3 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-44 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option v-for="dropoffBy in allDropoffBys" :value="dropoffBy.value"> {{ dropoffBy.label }}</option>
                        </select>
                    </div> -->
                </div>
                <!-- / panel search -->
                <ListProducs
                    :items="form.items"
                    @updateValue="form.items = $event"
                />
                <div class="mt-4">
                    <label for="Comments">Comments</label>
                    <textarea
                        v-model="form.comments"
                        class="w-full h-8 px-1 rounded border-gray-300 shadow-sm lg:h-10 lg:text-sm focus:ring-blue-500 focus:border-blue-500"
                    >
                    </textarea>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="onSubmitForm"
                >
                    Confirm
                </PrimaryButton>
            </template>
        </DialogModal>
        <!--scanner-->

        <!-- view drop off -->
        <DialogModal :show="isViewDropoffDetails" @close="closeModal">
            <template #title> Drop off Details </template>
            <template #content>
                <div class="flex flex-row justify-between">
                    <div>
                        <div>
                            <span class="text-sm"> Date</span>:
                            <span class="text-sm text-blue-600">{{
                                dropoff.date
                            }}</span>
                        </div>
                        <div>
                            <span class="text-sm"> Dropoffed By</span>:
                            <span class="text-blue-600">{{
                                dropoff.dropoffer_by
                            }}</span>
                        </div>
                        <div>
                            <span class="text-sm"> Dropoffed At</span>:
                            <span class="text-blue-600">{{
                                dropoff.dropoffed_at
                            }}</span>
                        </div>
                        <div>
                            <span class="text-sm"> Comments: </span>:
                            <span class="text-blue-600">{{
                                dropoff.comments
                            }}</span>
                        </div>
                    </div>
                    <div>
                        <div>
                            <span class="text-sm"> Status: </span>:
                            <span class="text-red-600">{{
                                dropoff.status
                            }}</span>
                        </div>
                        <div>
                            <span class="text-sm"> Recipe Numbers: </span>:
                            <span class="text-sm text-blue-600">{{
                                dropoff.recipe_numbers
                            }}</span>
                        </div>
                        <div>
                            <span class="text-sm">Scanouted By: </span>:
                            <span class="text-blue-600">{{
                                dropoff.scanouter_by
                            }}</span>
                        </div>
                        <div>
                            <span class="text-sm">Scanouted At: </span>:
                            <span class="text-sm text-blue-600">{{
                                dropoff.scanouted_at
                            }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <table
                        class="min-w-full bg-white table-fixed border border-gray-200"
                    >
                        <thead>
                            <tr class="border-b text-center border-gray-200">
                                <th class="w-8 text-left lg:table-cell">
                                    <span
                                        class="flex relative p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500"
                                    >
                                        No
                                    </span>
                                </th>
                                <th class="lg:table-cell">
                                    <span
                                        class="flex relative z-10 p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500"
                                    >
                                        Product Name
                                    </span>
                                </th>
                                <th class=" w-16 lg:table-cell">
                                    <span
                                        class="flex relative z-10 p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500"
                                    >
                                        Order QTY
                                    </span>
                                </th>
                                <th class="lg:table-cell">
                                    <span
                                        class="flex relative z-10 p-2 space-x-2 font-normal text-blue-600 group lg:text-sm focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500"
                                    >
                                        Drop off QTY
                                    </span>
                                </th>
                                <th class="w-10 lg:table-cell">-</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="(item, index) in dropoff.items"
                                :key="index"
                                class="align-top group"
                            >
                                <td class="p-2">
                                    {{ index + 1 }}
                                </td>
                                <td class="p-2  text-sm">
                                    {{ item.product_name }}
                                </td>
                                <td class="p-2 selection: text-center w-16">
                                    {{ item.order_quantity }}
                                </td>
                                <td class="p-2 text-center w-16">
                                    {{ item.drop_off_quantity }}
                                </td>

                                <td class="p-2">
                                    <button
                                        class="inline-flex items-center px-1 py-2 h-8 font-medium text-red-700 bg-white lg:h-5 lg:text-sm hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        <svg
                                            v-if="
                                                item.drop_off_quantity_status === 1
                                            "
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="w-4 h-4"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M4.5 12.75l6 6 9-13.5"
                                            />
                                        </svg>

                                        <svg
                                            v-else
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="w-4 h-4"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <!-- <tr class="align-top" v-if="!dropoff.items.length">
                                <td
                                    colspan="4"
                                    class="p-2 text-sm text-gray-700"
                                >
                                    No items found.
                                </td>
                            </tr> -->
                        </tbody>
                        <tfoot>
                            <tr class="border-t border-gray-200">
                                <td colspan="2" class="border-r p-2 text-right">
                                    Total Quantity:
                                </td>
                                <td class="border-r p-2 w-28 text-center">
                                    {{ dropoff.ordered_quantity }}
                                </td>
                                <td class="p-2 w-28 text-center">
                                    {{ dropoff.droffed_quantity }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
            </template>
        </DialogModal>
        <!-- / view drop off-->
    </AppLayout>
</template>

<script>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import { pickBy } from "lodash";

import AppLayout from "@/Layouts/AppLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import SelectAction from "@/Components/SelectAction.vue";
import DialogModal from "@/Components/DialogModal.vue";

import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import JetButton from "@/Components/Button.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import ListProducs from "@/Pages/Reports/Drivers/Partials/ListProducs.vue";
import UserProfile from "@/Pages/Reports/Drivers/Partials/UserProfile.vue";
import MenuDropDown from "@/Components/MenuDropDown.vue";

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
        ListProducs,
        UserProfile,
        MenuDropDown,
    },
    props: {
        entries: Object,
        dropoffBys: Array,
        scanOutBys: Array,
        statuses: Array,
        months: Array,
        pagers: Array,
        queryParams: Object,
    },
    data() {
        return {
            isViewDropoffDetails: false,
            isScannerForm: false,
            term: null,
            form: useForm({
                id: null,
                items: [],
                comments: null,
            }),
            dropoff: [],
            actions: [
                { id: null, label: "Bulk actions" },
                { id: "export-to-xlsx", label: "Export .xlsx" },
            ],
            query: {
                scanOutBy: this.queryParams.scanOutBy,
                dropoffBy: this.queryParams.dropoffBy,
                status: this.queryParams.status,
                term: this.queryParams.term,
                month: this.queryParams.month,
                pager: this.queryParams.pager,
            },
        };
    },
    methods: {
        scannerForm(id) {
            this.isScannerForm = true;
        },
        onSubmitForm() {
            this.form.post(route("drivers.dropoffs.store"), {
                preserveScroll: true,
                onSuccess: () => {
                    this.closeModal();
                    form.reset();
                }
            });
        },
        view(id) {
            axios.get(
                    route("drivers.dropoffs.show", {
                        id: id,
                    })
                )
                .then((response) => {
                    // console.log(response.data.data);
                    this.dropoff = response.data.data;
                    this.isViewDropoffDetails = true;

                    console.log(this.dropoff)
                })
                .catch((error) => {
                    alert("data not found!.");
                    console.log(error);
                });
        },
        updateDropoffStatus(id){
            //drivers.dropoffs.update
        },
        toggleSelectAll(e) {
            this.entries.data.forEach(
                (entry) => (entry.selected = e.target.checked)
            );
        },
        executeAction(actionId) {
            const entryIds = this.entries.data
                .filter((entry) => entry.selected)
                .map((entry) => entry.id);
            if (!entryIds.length) return;

            switch (actionId) {
                case "delete":
                    if (confirm(`Are you sure?`)) {
                        this.$inertia.delete(
                            route("settings.currencies.destroy"),
                            {
                                preserveState: false,
                                data: {
                                    entryIds: entryIds,
                                },
                            }
                        );
                    }
                    break;

                case "export-to-xlsx":
                    if (
                        confirm(
                            `Are you wish bulk items to .xlsx with selected?`
                        )
                    ) {
                        window.open(
                            `/reports/recipes/export/bulk-action?entryIds=${entryIds}`,
                            "_blank"
                        );
                    }
                    break;
            }
        },
        recipe_filter() {
            axios
                .get(
                    route("drivers.dropoffs.scanner.recipe_filter", {
                        term: this.term,
                    })
                )
                .then((response) => {
                    this.form.items.push(...response.data);
                    this.term = null;
                })
                .catch((error) => {
                    alert("data not found!.");
                    console.log(error);
                });
        },
        filter() {
            this.$inertia.get(
                route("drivers.dropoffs.index"),
                pickBy(this.query),
                {
                    preserveState: true,
                }
            );
        },
        closeModal: function () {
            this.isViewDropoffDetails = false;
            this.isScannerForm = false;
            this.term = null;
            this.form.reset("items", "comments");
            this.dropoff.reset('items');
            this.form.clearErrors("items");
        },
    },
    computed: {
        allScanOutBys() {
            return [{ value: null, label: "Scan out by" }, ...this.scanOutBys];
        },
        allDropoffBys() {
            return [{ value: null, label: "Dropoff by" }, ...this.dropoffBys];
        },
        allStatuses() {
            return [{ value: null, label: "Any status" }, ...this.statuses];
        },
        allMonths() {
            return [{ value: null, label: "Any date" }, ...this.months];
        },
        allPagers() {
            return [{ value: null, label: "15" }, ...this.pagers];
        },
    },
};
</script>
