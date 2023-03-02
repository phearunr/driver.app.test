<template>
    <Multiselect
    v-model="value"
    mode="tags"
    placeholder="Choose your stack"
    :close-on-select="false"
    :filter-results="false"
    :min-chars="1"
    :resolve-on-load="false"
    :delay="0"
    :searchable="true"
    :options="mechanics"
  />
  {{ mechanics }}
  </template>

<script>
import Multiselect from '@vueform/multiselect'
import {throttle} from "lodash";

export default {
    props: {
      mechanics: {
        type: Array,
        default: () => []
      },
      consultants: {
        type: Array,
        default: () => []
      }
    },
    components: {
        Multiselect,
    },
    data() {
        return {
            value: null,
            selectedMechanic: undefined,
            selectedConsultant: undefined,
            options: [
                'Batman',
                'Robin',
                'Joker',
            ],
            form: this.$inertia.form({
          client_name: '',
          car: '',
          description: '',
          mechanic_id: '',
          consultant_id: ''
        }),
        }
    },
    methods: {
        onSearchMechanicsChange: throttle(function (term) {

            this.$inertia.get('/test', {term}, {
            preserveState: true,
            preserveScroll: true,
            replace: true
            });

      }, 300),
      onSelectedMechanic(consultant) {
        this.form.consultant_id = consultant.id;
      },
    // handleTagCreate: async (option, select$) => {
    //         // Do not allow create tags above 67
    //         if (parseInt(option.value) > 67) {
    //             alert(`${option.value} is not allowed. Option must by <= 67.`)

    //             // If returns `false` the tag will not be added
    //             return false
    //         }

    //         // Async request (eg. for validating)
    //         await new Promise((resolve, reject) => {
    //             setTimeout(() => {
    //                 resolve()
    //             }, 1000)
    //         })

    //         // Modifying option label
    //         option.label = option.label + ' - confirmed'

    //         return option
    //     }
    }
}
</script>

<style src="@vueform/multiselect/themes/default.css">

</style>
