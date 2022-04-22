<template>
  <q-page padding>
    <h5>Taldeen zerrenda</h5>
    <div class="row">
      <div class="col-12">
        <group-table :rows="groups"></group-table>
      </div>
    </div>
  </q-page>
</template>

<script>
import GroupTable from "../../components/group/groupTable";

import {computed} from "vue";
import {useGroupStore} from "../../store/groupStore";
export default {
  name: "Group",
  components: {GroupTable},
  setup() {
    const store = useGroupStore();
    store.fetchGroups()
    const groups = computed(() => store.getGroups)

    return {
      groups
    }
  }
}
</script>

<style scoped>

</style>
