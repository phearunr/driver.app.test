import {ref} from "vue";

export const cart = ref({
  isOpened: false,
  setIsOpened(value) {
    this.isOpened = value;
  }
});
