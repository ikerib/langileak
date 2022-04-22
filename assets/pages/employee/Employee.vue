<template>
  <q-page padding>
    <h4>Langileen zerrenda</h4>
    <div class="row">
      <div class="col-12">
        <langileak-table :rows="langileak"></langileak-table>
      </div>
    </div>
  </q-page>
</template>

<script>
  import langileakTable from "../../components/employee/employeeTable.vue";
  import {useEmployeeStore} from "../../store/employeeStore";
  import {computed} from "vue";
  import { storeToRefs } from 'pinia'

  export default {
    components: {langileakTable},
    setup() {
      const store = useEmployeeStore();
      const { getEmployees } = storeToRefs(store)

      store.fetchEmployees();

      return {
        langileak: computed(() => store.getEmployees)
      }
    }
  }

</script>
