<template>
  <q-page padding>
    <h5>Udalen zerrenda</h5>
    <div class="row">
      <div class="col-12">
        <company-table :rows="companies"></company-table>
      </div>
    </div>
  </q-page>
</template>

<script>
import companyTable from "../../components/company/companyTable";
import {useCompanyStore} from "../../store/companyStore";
import {computed} from "vue";
import { storeToRefs } from 'pinia'

export default {
  components: { companyTable },
  setup() {
    const store = useCompanyStore();
    store.fetchCompanies()
    const companies = computed(() => store.getCompanies)

    return {
      companies
    }
  }
}

</script>
