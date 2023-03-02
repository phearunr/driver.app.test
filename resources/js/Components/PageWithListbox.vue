<script setup>
import BaseListbox from "./BaseListbox.vue";
import {ref} from "vue";

const people = [
  {value: 1, label: "Wade Cooper"},
  {value: 2, label: "Arlene Mccoy"},
  {value: 3, label: "Devon Webb"},
  {value: 4, label: "Tom Cook"},
  {value: 5, label: "Tanya Fox"},
  {value: 6, label: "Hellen Schmidt"},
];

const roles = [
  {value: 1, label: "Marketing"},
  {value: 2, label: "Front desk"},
  {value: 3, label: "Support"},
];

const form = ref({
  person_id: null,
  role_ids: []
});
</script>

<template>
  <div class="p-4 mx-auto max-w-2xl">
    <form
      action="#"
      class="flex flex-col space-y-6"
    >
      <BaseListbox
        placeholder="Select person"
        v-model="form.person_id"
        :options="people"
      />

      <BaseListbox
        placeholder="Select roles"
        v-model="form.role_ids"
        :options="roles"
        multiple
      />
    </form>
  </div>
</template>
