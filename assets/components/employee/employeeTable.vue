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
              :pagination.sync="pagination"
              virtual-scroll
          >
            <template v-slot:body-cell-name="cellProperties">
              <q-td :props="cellProperties">
                <router-link :to="{ name: 'employeeContract', params: { id: cellProperties.row.id }}">{{cellProperties.value}}</router-link>
<!--                <a href="#/offer/">{{ cellProperties.value }}</a>-->
<!--                this.$router.push({name: 'employeeContract', params: {id: item.id}})-->

              </q-td>
            </template>
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
                <q-btn size="sm" dense round flat color="grey" @click="showRow(props.row)" icon="visibility"></q-btn>
                <q-btn size="sm" dense round flat color="grey" @click="editRow(props.row)" icon="edit"></q-btn>
                <q-btn size="sm" ºdense round flat color="grey" @click="removeRow(props.row)" icon="delete"></q-btn>
              </q-td>
            </template>
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

                <q-item>
                  <q-item-section>
                    <q-item-label class="q-pb-xs">Abizenak</q-item-label>
                    <q-input dense outlined v-model="editedItem.surname" />
                  </q-item-section>
                </q-item>

                <q-item>
                  <q-item-section>
                    <q-item-label class="q-pb-xs">Lanean hasi</q-item-label>
                    <q-input dense outlined
                             v-model="editedItem.dateStartWorking"
                             mask="date"
                             :rules="['date']"
                             @click="$refs.qDateProxy.show()"
                    >
                      <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer">
                          <q-popup-proxy ref="qDateProxy" cover transition-show="scale" transition-hide="scale">
                            <q-date v-model="editedItem.dateStartWorking" first-day-of-week="1">
                              <div class="row items-center justify-end">
                                <q-btn v-close-popup label="Itxi" color="primary" flat />
                              </div>
                            </q-date>
                          </q-popup-proxy>
                        </q-icon>
                      </template>
                    </q-input>
                  </q-item-section>
                </q-item>

                <q-item>
                  <q-item-section>
                    <q-item-label class="q-pb-xs">Langilearen Kodea</q-item-label>
                    <q-input dense outlined v-model="editedItem.code" />
                  </q-item-section>
                </q-item>

                <q-item>
                  <q-item-section>
                    <q-item-label class="q-pb-xs">Egunak</q-item-label>
                    <q-input dense outlined v-model.number="editedItem.days" type="number" />
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
import {useEmployeeStore} from "../../store/employeeStore";
import Swal from 'sweetalert2'
import {date} from "quasar";

const columns = [
  { name:  'id',              label: 'id',          align: 'left',    field: 'id',                sortable: true ,  classes: 'hidden', headerClasses: 'hidden'},
  { name: 'name',             label: 'Langilea',       align: 'left',    field: 'name',              sortable: true },
  { name: 'dateStartWorking', label: 'Lanean hasi', align: 'right',  field: 'dateStartWorking',   sortable: true,   format: val => date.formatDate(val, 'YYYY/MM/DD'),  style: 'max-width: 100px', headerClasses: '', headerStyle: 'max-width: 100px'},
  { name: 'years',            label: 'U',           align: 'center',  field: 'years',             sortable: false,  style: 'max-width: 20px', headerClasses: '', headerStyle: 'max-width: 20px'},
  { name: 'months',           label: 'H',           align: 'center',  field: 'months',            sortable: false,  style: 'max-width: 20px', headerClasses: '', headerStyle: 'max-width: 20px'},
  { name: 'days',             label: 'E',           align: 'center',  field: 'days',              sortable: false,  style: 'max-width: 20px', headerClasses: '', headerStyle: 'max-width: 20px'},
  { name: 'dateTeoric',       label: 'Teorikoa',    align: 'right',  field: 'dateTeoric',         sortable: true,   format: val => date.formatDate(val, 'YYYY/MM/DD'),  style: 'max-width: 100px', headerClasses: '', headerStyle: 'max-width: 100px'},
  { name: 'dateTriennium',    label: 'Hirurtekoa',  align: 'right',  field: 'dateTriennium',      sortable: true,   format: val => date.formatDate(val, 'YYYY/MM/DD'),  style: 'max-width: 100px', headerClasses: '', headerStyle: 'max-width: 100px'},
  { name: 'datePayroll',      label: 'Nomina',      align: 'right',  field: 'datePayroll',        sortable: true,   format: val => date.formatDate(val, 'YYYY/MM/DD'),  style: 'max-width: 100px', headerClasses: '', headerStyle: 'max-width: 100px'},
  { name: 'numberTrienniums', label: 'Z',           align: 'center',  field: 'numberTrienniums',  sortable: true,  style: 'max-width: 20px', headerClasses: 'bg-primary text-white', headerStyle: 'max-width: 20px'},
  { name: 'numberDaysOf',     label: 'O',           align: 'center',  field: 'numberDaysOf',      sortable: true,  style: 'max-width: 20px', headerClasses: 'bg-primary text-white', headerStyle: 'max-width: 20px'},
  { name: 'actions',          label: '',            align: 'center'}
]
const defaultItem = {
  id: '',
  name: '',
  surname: '',
  code: '',
  dateStartWorking: '',
  years: 0,
  months: 0,
  days: 0,
  dateTeoric: '',
  dateTriennium: '',
  datePayroll: '',
  numberTrienniums: 0,
  numberDaysOf: 0
}

export default {
  name: "employeeTable",
  props: {
    rows: {
      type: Array
    }
  },
  setup(props, context) {
    const filter = ref('')
    const store = useEmployeeStore();
    const defaultItem = ref({
      name: '',
    })
    let isDialogActionEdit = ref(false);
    const tableData = props.rows;
    return {
      columns,
      filter,
      show_dialog: ref(false),
      editedIndex: -1,
      editedItem: ref(defaultItem),
      currencyColumns: columns,
      pagination: {
        page: 1,
        rowsPerPage: 20
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
      this.editedItem = defaultItem;
      this.isDialogActionEdit = false;
      this.show_dialog = true;
    },
    saveRow(item) {
      console.log('SAVEROW')
      console.log(item);
      console.log(this.editedItem);
      this.store.addEmployee(this.editedItem)
    },
    editRow(item) {
      this.isDialogActionEdit = true;
      this.editedIndex = this.rows.findIndex((v, i) =>v.id === item.id)
      this.editedItem = Object.assign({}, item);
      this.show_dialog = true;
    },
    showRow(item) {
      this.editedIndex = this.rows.findIndex((v, i) =>v.id === item.id)
      this.editedItem = Object.assign({}, item);
      this.$router.push({name: 'employeeContract', params: {id: item.id}})

    },
    updateRow() {
      console.log('updateRow')
      console.log(this.editedItem);
      this.store.updateEmployee(this.editedItem);
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
          this.store.removeEmployee(this.editedItem);
        }
      })
    }
  }
}
</script>

<style scoped>
a:link, a:hover, a:visited {
  text-transform: uppercase;
  text-decoration: none;
  color: #000000;
}
</style>
