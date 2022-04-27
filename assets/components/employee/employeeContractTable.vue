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
              virtual-scroll
              :rows-per-page-options="[0]"
              :pagination.sync="pagination"
              hide-bottom
          >
            <template v-slot:top-left="props">
              <q-btn color="primary" icon="add" label="Berria" @click="addRow"/>
            </template>

            <template v-slot:body-cell-taldea="props">
              <q-td key="taldea" :props="props" v-if="props.row.name!=='TOTAL' && !!props.row.taldea ">
                {{props.row.taldea.name}}
              </q-td>
              <q-td v-else></q-td>
            </template>

            <template #body-cell-actions="props">
              <q-td>
                <q-btn v-if="props.row.name!=='TOTAL'" dense round flat color="grey" @click="editRow(props.row)" icon="edit"></q-btn>
                <q-btn v-if="props.row.name!=='TOTAL'" dense round flat color="grey" @click="removeRow(props.row)" icon="delete"></q-btn>
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
            <div class="text-h6" v-else>Kontratu berria</div>
          </q-card-section>
          <q-separator inset></q-separator>
          <q-card-section class="q-pt-none">
            <q-form class="q-gutter-md">
              <q-list>

                <q-item>
                  <q-item-section>
                    <q-item-label class="q-pb-xs">* Udala</q-item-label>
                    <q-input dense outlined v-model="editedItem.name"/>
                  </q-item-section>
                </q-item>

                <q-item>
                  <q-item-section>
                    <q-select
                        outlined
                        v-model="editedItem.taldea"
                        :options="taldeak"
                        option-value="id"
                        option-label="name"
                        label="Taldea" />
                  </q-item-section>
                </q-item>

                <q-item>
                  <q-item-section>
                    <q-item-label class="q-pb-xs">* Hasi</q-item-label>
                    <q-input dense outlined
                             v-model="editedItem.startDate"
                             mask="date"
                             :rules="['date']"
                    >
                      <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer">
                          <q-popup-proxy ref="qDateProxy" cover transition-show="scale" transition-hide="scale">
                            <q-date v-model="editedItem.startDate" first-day-of-week="1" @input="console.log('clickclick')">
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
                    <q-item-label class="q-pb-xs">Amaitu</q-item-label>
                    <q-input dense outlined
                             v-model="editedItem.endDate"
                             mask="date"
                             :rules="['date']"
                    >
                      <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer">
                          <q-popup-proxy ref="qDateProxy" cover transition-show="scale" transition-hide="scale">
                            <q-date v-model="editedItem.endDate" first-day-of-week="1" @input="$refs.qDateProxy.hide()">
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
                    <q-checkbox left-label v-model="editedItem.isValid" label="Onartzen da (Markatu baiezkoa bada)" />
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
                  :disabled="(editedItem.name==='') || (editedItem.taldea==='') || (editedItem.startDate==='')"
              ></q-btn>
              <q-btn
                  flat
                  label="Sortu"
                  color="primary"
                  dense
                  v-close-popup
                  @click="saveRow"
                  v-if="!isDialogActionEdit"
                  :disabled="(editedItem.name==='') || (editedItem.taldea==='') || (editedItem.startDate==='')"
              ></q-btn>
            </q-card-actions>
          </q-card-section>
        </q-card>
      </q-dialog>

    </div>
  </div>

</template>

<script>
import {ref} from 'vue'
import {computed} from 'vue'
import {useEmployeeStore} from "../../store/employeeStore";
import Swal from 'sweetalert2'
import {date} from 'quasar'
import {Notify} from "quasar";
import { useQuasar } from 'quasar'
import {DateTime} from "luxon";


const columns = [
  {name: 'name', label: 'Udala', align: 'left', field: 'name'},
  {name: 'taldea', label: 'Taldea', align: 'left', field: 'taldea', },
  {name: 'lanaldia', label: 'Lanaldia', align: 'left', field: 'lanaldia', },
  {
    name: 'startDate',
    label: 'Hasi',
    align: 'center',
    field: 'startDate',
    format: val => date.formatDate(val, 'YYYY/MM/DD')
  },
  {
    name: 'endDate',
    label: 'Amaitu',
    align: 'center',
    field: 'endDate',
    format: val => date.formatDate(val, 'YYYY/MM/DD')
  },
  {name: 'totalDays', label: 'Totala', align: 'center', field: 'totalDays'},
  {name: 'years', label: 'Urteak', align: 'center', field: 'years'},
  {name: 'months', label: 'Hilabeteak', align: 'center', field: 'months'},
  {name: 'days', label: 'Egunak', align: 'center', field: 'days'},
  {name: 'actions', label: '', align: 'center'}
]
const defaultItem = ref({
  name: '',
  taldea: '',
  startDate: '',
  endDate: '',
  years: 0,
  months: 0,
  days: 0,
  isValid: true
})
export default {
  name: "employeeContractTable",
  props: {
    onartua: Boolean,
    rows: {
      type: Array,
    }

  },
  setup(props, context) {
    const store = useEmployeeStore();
    store.fetchGroups()

    const $q = useQuasar()

    let isDialogActionEdit = ref(false);
    const tableData = props.rows;
    const onartua = props.onartua;
    return {
      taldeak: computed(() => store.getTaldeak),
      columns,
      store,
      show_dialog: ref(false),
      editedIndex: -1,
      editedItem: ref(defaultItem),
      pagination: {
        page: 1,
        sortBy: 'startDate',
        descending: true,
        rowsPerPage: 0 // 0 means all rows
      },
      page: 1,
      totalRecord: 0,
      pageCount: 1,
      tableData,
      onartua,
      isDialogActionEdit
    }
  },
  methods: {
    addRow() {
      console.log("KAIXO")
      console.log(defaultItem)
      this.editedItem = {
        name: '',
        taldea: '',
        startDate: '',
        endDate: '',
        years: 0,
        months: 0,
        days: 0,
        isValid: this.onartua
      }
      // this.editedItem = Object.assign({}, defaultItem);
      this.isDialogActionEdit = false;
      this.show_dialog = true;
    },
    saveRow(item) {
      console.log('SAVEROW')
      console.log(this.editedItem);
      if (this.editedItem.startDate !== "") {
        this.store.addContract(this.editedItem);
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Hasiera data beharrezkoa da!'
        })
      }

    },
    editRow(item) {
      console.log("EDITROW")
      console.log(item)

      this.editedIndex = this.rows.findIndex((v, i) =>v.id === item.id)
      this.editedItem = Object.assign({}, item,
          {
            startDate:DateTime.fromISO(item.startDate, { locale: "es" }).toFormat('yyyy/MM/dd')
          },
          {
            endDate:DateTime.fromISO(item.endDate, { locale: "es" }).toFormat('yyyy/MM/dd')
          }
      );
      this.isDialogActionEdit = true;
      console.log("editItem")
      console.log(this.editedItem)
      this.show_dialog = true;
    },
    showRow(item) {
      this.editedIndex = this.rows.findIndex((v, i) => v.id === item.id)
      this.editedItem = Object.assign({}, item);
      this.$router.push({name: 'employeeContract', params: {id: item.id}})
    },
    updateRow() {
      console.log('>>employeeContractTable => updateRow')
      console.log(this.editedItem)
      console.log('employeeContractTable => updateRow >>>')
      this.store.updateContract(this.editedItem);
    },
    removeRow(item) {
      Swal.fire({
        title: 'Ziur zaude ezabatu nahi duzula?',
        showCancelButton: true,
        cancelButtonText: 'Ezeztatu',
        confirmButtonText: 'Onartu',
      }).then((result) => {
        if (result.isConfirmed) {
          this.editedIndex = this.rows.findIndex((v, i) => v.id === item.id)
          this.editedItem = Object.assign({}, item);
          this.store.removeContract(this.editedItem);
        }
      })
    }
  }
}
</script>

<style scoped>

</style>
