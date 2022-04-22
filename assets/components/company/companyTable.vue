<template>
  <div id="q-app">
    <div class="q-pa-md">
      <div class="row q-col-gutter-sm">
        <div class="col">
          <q-table
              flat
              bordered
              class="statement-table"
              :rows="rows"
              :columns="columns"
              row-key="__index"
              :filter="filter"
              virtual-scroll
          >
            <template v-slot:top-left="props">
              <q-btn color="primary" icon="add" label="Berria" @click="addRow" />
            </template>
            <template v-slot:top-right="props">
              <q-input
                  outlined
                  dense
                  debounce="300"
                  v-model="filter"
                  placeholder="Filtratu"
              >
                <template v-slot:append>
                  <q-icon name="search" />
                </template>
              </q-input>
            </template>

            <template #body-cell-actions="props">
              <q-td>
                <q-btn dense round flat color="grey" @click="editRow(props.row)" icon="edit"></q-btn>
                <q-btn dense round flat color="grey" @click="removeRow(props.row)" icon="delete"></q-btn>
              </q-td>
            </template>

<!--            <template v-slot:bottom>-->
<!--              <div class="pagination-total q-mt-sm flex flex-center">-->
<!--                <div class="text-medium-regular">-->
<!--                  Total {{ rows.length }}-->
<!--                  <span v-if="totalRecord > 1">emaitza</span>-->
<!--                  <span v-else>emaitz</span>-->
<!--                </div>-->
<!--              </div>-->
<!--              <div class="pagination-container q-my-sm flex flex-center">-->
<!--                <q-pagination-->
<!--                    v-model="page"-->
<!--                    :max="5"-->
<!--                    input-->
<!--                    input-class="text-orange-10"-->
<!--                />-->
<!--              </div>-->
<!--            </template>-->


          </q-table>
        </div>
      </div>
      <q-dialog v-model="show_dialog">
        <q-card style="width: 600px; max-width: 60vw">
          <q-card-section>
            <q-btn round flat dense icon="close" class="float-right" color="grey-8" v-close-popup></q-btn>
            <div class="text-h6" v-if="isDialogActionEdit">Editatu</div>
            <div class="text-h6" v-else>Berria</div>
          </q-card-section>
          <q-separator inset></q-separator>
          <q-card-section class="q-pt-none">
            <q-form class="q-gutter-md">
              <q-list>
                <q-item>
                  <q-item-section>
                    <q-item-label class="q-pb-xs">Izena</q-item-label>
                    <q-input dense outlined v-model="editedItem.name" />
                  </q-item-section>
                </q-item>
              </q-list>
            </q-form>
          </q-card-section>
          <q-card-section>
            <q-card-actions align="right">
              <q-btn
                  flat
                  label="Cancel"
                  color="warning"
                  dense
                  v-close-popup
              ></q-btn>
              <q-btn
                  flat
                  label="Eguneratu"
                  color="primary"
                  dense
                  v-close-popup
                  @click="updateRow"
                  v-if="isDialogActionEdit"
              ></q-btn>
              <q-btn
                  flat
                  label="Sortu"
                  color="primary"
                  dense
                  v-close-popup
                  @click="saveRow"
                  v-if="!isDialogActionEdit"
              ></q-btn>
            </q-card-actions>
          </q-card-section>
        </q-card>
      </q-dialog>

    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import {useCompanyStore} from "../../store/companyStore";
import Swal from 'sweetalert2'

const columns = [
  { name:  'id',    label: 'id',      align: 'left',  field: 'id',      sortable: true },
  { name: 'name',   label: 'izena',   align: 'left',  field: 'name',    sortable: true },
  { name: 'actions',label: '',        align: 'center'}
]
const defaultItem = {
  id: '',
  name: ''
}

export default {
  name: "companyTable",
  props: {
    rows: {
      type: Array
    }
  },
  setup(props, context) {
    const filter = ref('')
    const store = useCompanyStore();
    const defaultItem = {
      name: '',
    }
    let isDialogActionEdit = ref(false);
    const tableData = props.rows;
    return {
      columns,
      filter,
      show_dialog: ref(false),
      editedIndex: -1,
      editedItem: defaultItem,
      currencyColumns: columns,
      pagination: {
        page: 1,
      },
      page: 1,
      totalRecord: 0,
      pageCount: 1,
      tableData,
      store,
      isDialogActionEdit
    }
  },
  methods: {
    addRow() {
      this.isDialogActionEdit = false;
      this.show_dialog = true;
    },
    saveRow(item) {
      console.log('SAVEROW')
      console.log(item);
      this.store.addCompany(this.editedItem)
    },
    editRow(item) {
      this.isDialogActionEdit = true;
      this.editedIndex = this.rows.findIndex((v, i) =>v.id === item.id)
      this.editedItem = Object.assign({}, item);
      this.show_dialog = true;
    },
    updateRow() {
      this.store.updateCompany(this.editedItem);
    },
    removeRow(item) {
      Swal.fire({
        title: 'Ziur zaude ezabatu nahi duzula?',
        showCancelButton: true,
        cancelButtonText: 'Ezeztatu',
        confirmButtonText: 'Onartu',
      }).then((result) => {
        if (result.isConfirmed) {
          this.editedIndex = this.rows.findIndex((v, i) =>v.id === item.id)
          this.editedItem = Object.assign({}, item);
          this.store.removeCompany(this.editedItem);
        }
      })
    }
  }
}
</script>

<style scoped>

</style>
