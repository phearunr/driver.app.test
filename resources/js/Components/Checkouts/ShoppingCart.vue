<script setup>
import {ref} from 'vue';
import {Dialog, DialogOverlay, DialogTitle, TransitionChild, TransitionRoot} from '@headlessui/vue';

const props = defineProps({
  isOpened: Boolean
});

const emit = defineEmits(['toggle']);

function setIsOpened(value) {
  emit('toggle', value);
}
</script>

<template>
  <button class="fixed right-6 bottom-6 p-4 bg-black rounded-full hover:bg-gray-800" @click="setIsOpened(true)">
    <span
      class="inline-flex absolute top-0 left-0 justify-center items-center -m-1 w-6 h-6 text-xs font-bold leading-none text-white bg-red-600 rounded-full">1</span>
    <svg id="cart-icon" class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
         xmlns="http://www.w3.org/2000/svg">
      <path
        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
        stroke-linecap="round" stroke-linejoin="round"
        stroke-width="2"/>
    </svg>
  </button>

  <TransitionRoot :show="props.isOpened" appear as="template">
    <Dialog
      :open="props.isOpened"
      class="overflow-hidden fixed inset-0 z-10" @close="setIsOpened(false)">
      <TransitionChild
        as="template"
        enter="transition-opacity ease-in-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="transition-opacity ease-in-out duration-300"
        leave-from="opacity-100"
        leave-to="opacity-0">
        <DialogOverlay class="absolute inset-0 bg-black bg-opacity-40"/>
      </TransitionChild>

      <TransitionChild
        as="template"
        enter="transform ease-in-out transition-transform duration-300"
        enter-from="translate-x-full"
        enter-to="translate-x-0"
        leave="transform ease-in-out transition-transform duration-300"
        leave-from="translate-x-0"
        leave-to="translate-x-full">
        <div class="flex fixed inset-y-0 right-0 flex-col w-full max-w-sm bg-white">
          <div class="flex justify-between items-center p-4 shadow">
            <DialogTitle class="text-xl font-bold">Cart summary</DialogTitle>
            <button class="p-1 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black"
                    @click="setIsOpened(false)">
              <svg id="close-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
              </svg>
            </button>
          </div>

          <div class="flex flex-col flex-1">
            <div class="relative flex-1">
              <div class="overflow-y-scroll absolute inset-0">
                <div class="flex flex-col divide-y divide-gray-100">
                  <div v-for="(item, index) in new Array(100)" :key="index" class="flex p-4 space-x-4">
                    <div class="flex flex-col w-24">
                      <img alt="" class="mb-0.5" src="https://via.placeholder.com/150">
                      <button
                        class="px-3 py-2 text-xs font-semibold tracking-wider text-center uppercase rounded cursor-pointer focus:ring-1 focus:ring-black">
                        Remove
                      </button>
                    </div>
                    <div>
                      <div class="text-xl font-semibold leading-tight">Some product title</div>
                      <div class="mt-2 leading-normal text-gray-800">10 x $30 = $300 (incl. VAT)</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="px-4 py-6 bg-gray-50">
              <div class="text-xl font-semibold text-center">Subtotal</div>
              <div class="text-xl font-semibold text-center">$320 (incl. VAT)</div>
              <div class="mt-4 text-center text-gray-600">To calculate the shipping costs go to the checkout page</div>

              <div class="flex flex-col mt-4 space-y-2">
                <button
                  ref="checkoutButtonRef"
                  class="p-3 text-white bg-black rounded-full hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">Checkout
                </button>
                <button
                  class="p-3 text-gray-500 bg-white rounded-full hover:text-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">Continue shopping
                </button>
              </div>
            </div>
          </div>
        </div>
      </TransitionChild>
    </Dialog>
  </TransitionRoot>
</template>
