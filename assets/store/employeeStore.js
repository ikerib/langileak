import {defineStore} from "pinia";
import axios from "axios";
import {DateTime} from "luxon";

export const useEmployeeStore = defineStore({
    id: 'employee',
    state: () => {
        return {
            groups: [],
            employees: [],
            employee: {},
            result: '',
            totalDays: 0,
            years: 0,
            months: 0,
            days: 0
        }
    },
    getters: {
        getEmployees(state) {
            return state.employees;
        },
        getTaldeak(state) {
            return state.groups;
        }
    },
    actions: {
        async addEmployee ( employee ) {
            console.log("addEmployee")
            const emp = {
                name: employee.name,
                start: employee.start,
                end: employee.end,
            }
            await axios.post('/api/employees.json', emp)
                .then( (response) => {
                    this.employees.push(employee);
                    console.log("addEmployee - ok")
                })
                .catch((error) => {
                    console.log(error);
                    console.log("addEmployee - error")
                });
        },
        async removeEmployee ( employee ) {
            const url = '/api/employees/' + employee.id;
            await  axios.delete(url, employee)
                .then((response) => {
                    const i = this.employees.findIndex(s => s.id === employee.id);
                    if ( i > -1 ) this.employees.splice(i, 1);
                })
                .catch((error) => {
                    console.log(error);
                    return error;
                });

        },
        async removeContract ( contract ) {
            const url = '/api/contracts/' + contract.id;
            await  axios.delete(url, contract)
                .then((response) => {
                    const i = this.employee.contracts.findIndex(s => s.id === contract.id);
                    if ( i > -1 ) this.employee.contracts.splice(i, 1);
                })
                .catch((error) => {
                    console.log(error);
                    return error;
                });

        },
        async fetchEmployees() {
            console.log('fetchEmployees')
            await axios.get('/api/employees.json')
                .then((response) => {
                    this.employees = response.data;
                })
                .catch((error) => {
                    console.log(error);
                    throw error.message;
                })
        },
        async fetchGroups() {
            console.log('fetchGroups')
            await axios.get('/api/groups.json')
                .then((response) => {
                    this.groups = response.data;
                })
                .catch((error) => {
                    console.log(error);
                    throw error.message;
                })
        },
        async fetchEmployee(id) {
            await axios.get('/api/employees/'+id+'.json')
                .then((response) => {
                    let myData = response.data;
                    let totalYears = 0;
                    let totalMonths = 0;
                    let totalDays = 0;
                    let totalTotalDays = 0;
                    myData.contracts.map( data => {
                        const dateStart = DateTime.fromISO(data.startDate, { locale: "es" });
                        const dateEnd = DateTime.fromISO(data.endDate, { locale: "es" });
                        const dDiff = dateEnd.diff(dateStart, ["days"]).toObject()
                        data.totalDays = Math.round(dDiff.days);
                        data.years = Math.floor(data.totalDays / 365)
                        data.months = Math.floor((data.totalDays-(data.years*365))/30.4)
                        data.days = Math.floor(data.totalDays - (data.years * 365) - (data.months * 30.4));
                        return data;
                    });
                    myData.contracts.forEach( data => {
                        totalTotalDays = totalTotalDays + (data.totalDays ? data.totalDays :0)
                    });
                    totalYears = Math.floor(totalTotalDays / 365);
                    totalMonths = Math.floor((totalTotalDays - (totalYears*365))/30.4)
                    totalDays = Math.floor(totalTotalDays-(totalYears*365)-(totalMonths*30.4))

                    this.totalDays = totalTotalDays;
                    this.years = totalYears;
                    this.months = totalMonths;
                    this.days = totalDays;

                    const i = myData.contracts.findIndex(s => s.name === 'TOTAL');
                    if ( i > -1 ) myData.contracts.splice(i, 1);
                    myData.contracts.push({
                        name: 'TOTAL',
                        totalDays: totalTotalDays,
                        years: totalYears,
                        months: totalMonths,
                        days: totalDays
                    });
                    this.employee = myData;
                })
                .catch((error) => {
                    console.log(error);
                    throw error.message;
                })
        },
        async updateEmployee(employee) {

            console.log('sployeeStore => updateEmployee')
            const i = employee.contracts.findIndex(s => s.name === 'TOTAL');
            if ( i > -1 ) employee.contracts.splice(i, 1);
            console.log(employee)
            const url = '/api/employees/' + employee.id;
            await  axios.put(url, employee)
                .then((response) => {
                    let editedIndex = this.employees.findIndex((v, i) =>v.id === employee.id)
                    //this.employees.splice(editedIndex, 1, employee)
                    this.fetchEmployee(employee.id)
                })
                .catch((error) => {
                    console.log(error);
                    return error;
                });
        },
        async updateContract ( contract ){
            console.log('>>> employeeStore => updateContract')
            console.log(contract)
            console.log('employeeStore => updateContract >>> ')
            let data = {};
            const dateStart = DateTime.fromFormat(contract.startDate, 'yyyy/MM/dd')
            const dateEnd = DateTime.fromFormat(contract.endDate, 'yyyy/MM/dd')
            const dDiff = dateEnd.diff(dateStart, ["days"]).toObject()

            data.name = contract.name;
            data.startDate = dateStart.toISO();
            data.endDate = dateEnd.toISO();
            data.totalDays = Math.round(dDiff.days);
            data.years = Math.floor(data.totalDays / 365)
            data.months = Math.floor((data.totalDays-(data.years*365))/30.4)
            data.days = Math.floor(data.totalDays - (data.years * 365) - (data.months * 30.4));
            this.employee.contracts.map( item => {
                if ( item.id === contract.id ) {
                    item.name = data.name
                    item.startDate = data.startDate
                    item.endDate = data.endDate
                    item.totalDays = data.totalDays
                    item.years = data.years
                    item.months = data.months
                    item.days = data.days
                    item.taldea = contract.taldea
                    return item;
                }
            })
            console.log('>>> before update employee')
            console.log(this.employee)
            console.log('before update employee >>>')
            return  this.updateEmployee(this.employee)
        },
        async addContract ( contract ) {
            // DELETE LAS ELEMENT
            this.employee.contracts.pop();
            let data = {};
            const dateStart = DateTime.fromFormat(contract.startDate, 'yyyy/MM/dd')
            let dateEnd = null;
            if ((contract.endDate !== "")) {
                dateEnd = DateTime.fromFormat(contract.endDate, 'yyyy/MM/dd')
            } else {
                data.endDate = null; // ez badago amaiera data, kalkuloak egin gaurko egunarekin
                dateEnd = DateTime.now();
                data.totalDays = 0;
                data.years = 0;
                data.months = 0;
                data.days = 0;
            }
            const dDiff = dateEnd.diff(dateStart, ["days"]).toObject()
            data.name = contract.name;
            data.startDate = dateStart.toISO();
            data.endDate = dateEnd.toISO();
            data.totalDays = Math.round(dDiff.days);
            data.years = Math.floor(data.totalDays / 365)
            data.months = Math.floor((data.totalDays-(data.years*365))/30.4)
            data.days = Math.floor(data.totalDays - (data.years * 365) - (data.months * 30.4));
            data.taldea = contract.taldea;

            this.employee.contracts.push(data)
            this.updateEmployee(this.employee);
        },
    }
});
