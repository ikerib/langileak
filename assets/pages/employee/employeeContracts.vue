<template>
  <q-page padding>
    <div class="row">
      <div class="col-1">
        <q-btn color="primary" icon="arrow_back" label="" :to="{name: 'Langileak'}" />
      </div>
      <div class="col-11">
        <h4>{{langilea.name}}</h4>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <employee-contract-table :rows="langilea.contracts"></employee-contract-table>
      </div>
    </div>
  </q-page>



</template>

<script>
import {useEmployeeStore} from "../../store/employeeStore";
import {computed} from "vue";
import employeeContractTable from "../../components/employee/employeeContractTable";

export default {
  name: "employeeContracts",
  components: {employeeContractTable},
  setup(props, context) {
    const store = useEmployeeStore();
    return {
      langilea: computed(() => store.employee)
    }
  },
  created() {
    const store = useEmployeeStore();
    store.fetchEmployee(this.$route.params.id);
  }
}
</script>

<style scoped>

</style>
