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
        <employee-contract-table :rows="store.getValidContracts" :onartua="true"></employee-contract-table>
      </div>
    </div>

    <div class="row">&nbsp;</div>
    <div class="row">
      <div class="col-12">
        <h6>Ez onartua</h6>
        <employee-contract-table :rows="store.getInvalidContracts" :onartua="false"></employee-contract-table>
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
  created() {
    const store = useEmployeeStore();
    store.fetchEmployee(this.$route.params.id);
  },
  setup(props, context) {
    const store = useEmployeeStore();
    return {
      langilea: computed(() => store.employee),
      // validContracts: computed(() => store.employee),
      validContracts: computed( () => store.employee.contracts.filter(
        c => c.isValid === true
      )),
      invalidContracts: computed( () => store.employee.contracts.filter(
          c => c.isValid === false
      )),
      store
    }
  }
}
</script>

<style scoped>

</style>
