<template>
    <div class="space-x-2 flex">
        <select v-model="actionId" aria-label="Action" name="action" class="pr-10 pl-3 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-48 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            <option v-for="(action, index) in actions" :value="action.id" :key="index">{{ action.label }}</option>
        </select>

        <button @click="$emit('execute', actionId)" type="button" class="inline-flex items-center px-4 h-11 font-medium text-gray-700 bg-white rounded border border-gray-300 shadow-sm lg:h-9 lg:text-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Apply
        </button>
    </div>
</template>

<script>
export default {
    name: 'SelectAction',
    props: {
        pagers:Array,
        actions: Array
    },
    data() {
        return {
            pagerId: 15,
            actionId: null
        }
    }
};
</script>

