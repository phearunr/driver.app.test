<template>
    <!-- List of recipe items  -->
    <div class="mt-4">
        <table class="min-w-full bg-white table-fixed border border-gray-200">
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
                    <th class="lg:table-cell">
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
                            Drop-off QTY
                        </span>
                    </th>
                    <th class="w-10 lg:table-cell">
                        <button
                            @click="addItem()"
                            class="inline-flex items-center px-1 h-4 font-medium text-blue-700 bg-white rounded border border-gray-300 shadow-sm lg:h-5 lg:text-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                          </svg>

                            <!-- <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-3 h-3"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 4.5v15m7.5-7.5h-15"
                                />
                            </svg> -->
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr
                    v-for="(item, index) in items"
                    :key="index"
                    class="align-top group"
                >
                    <td class="p-2">
                        {{ index + 1 }}
                    </td>
                    <td class=" text-sm p-2">
                        <DropdownSearch v-if="item.isEdit" />
                        <span v-else>
                            {{ item.product_name }}
                        </span>
                    </td>
                    <td class="p-2 text-center w-16">
                        {{ item.order_quantity }}
                    </td>
                    <td class="p-2 w-16">
                        <input
                            @keydown ="UpdateStatusItem(index)"
                            v-model="item.drop_off_quantity"
                            type="number"
                            class="w-full text-center h-4 px-1 rounded border-gray-300 shadow-sm lg:h-6 lg:text-sm focus:ring-blue-500 focus:border-blue-500"
                        />
                    </td>

                    <td class="p-2">
                        <button @click="removeItem(index)"
                            class="inline-flex items-center px-1  py-2 h-8 font-medium text-red-700 bg-white lg:h-5 lg:text-sm hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                        <svg
                            v-if="item.drop_off_quantity_status == 1"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>

                        <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                          </svg>


                        </button>
                    </td>
                </tr>
                <tr class="align-top" v-if="!items.length">
                    <td colspan="4" class="p-2 text-sm text-gray-700">
                        No items found.
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="border-t border-gray-200">
                    <td colspan="2" class="border-r p-2 text-right">Total Quantity:</td>
                    <td class="border-r p-2  w-28 text-center">{{ total_order_quantity }}</td>
                    <td class="p-2 w-28 text-center">{{ total_drop_off_quantity }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <!-- / List of recipe items  -->
</template>

<script setup>

import { computed } from "vue";
import DropdownSearch from '@/Components/DropdownSearch.vue';

const props = defineProps({
     items: Array,
});

const addItem = () => {
    props.items.push({
        product_name: null,
        quantity: 1,
        isEdit: true,
    });
};

const UpdateStatusItem = (index) => {
    props.items[index].order_quantity ==  props.items[index].drop_off_quantity
    ? props.items[index].drop_off_quantity_status = 1 : props.items[index].drop_off_quantity_status = 0;

};

const removeItem = (index) => {
    props.items.splice(index, 1);
};

const total_order_quantity = computed(() => {
    let total = 0;
    props.items.map((item) => {
        total += item.order_quantity;
    });
    return total;
});

const total_drop_off_quantity = computed(() => {
    let total = 0;
    props.items.map((item) => {
        total += item.drop_off_quantity;
    });
    return total;
});

computed(() => {
    this.$emit("updateValue", props.items);
});

</script>
