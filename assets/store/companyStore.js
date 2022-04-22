import {defineStore} from "pinia";
import axios from "axios";

export const useCompanyStore = defineStore({
    id: 'company',
    state: () => {
        return {
            companies: []
        }
    },
    getters: {
        getCompanies(state) {
            return state.companies;
        }
    },
    actions: {
        async addCompany ( company ) {
            await axios.post('/api/companies.json', company)
                .then( (response) => {
                    this.companies.push(company);
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        async removeCompany ( company ) {
            const url = '/api/companies/' + company.id;
            await  axios.delete(url, company)
                .then((response) => {
                    const i = this.companies.findIndex(s => s.id === company.id);
                    if ( i > -1 ) this.companies.splice(i, 1);
                })
                .catch((error) => {
                    console.log(error);
                    return error;
                });
        },
        async fetchCompanies() {
            await axios.get('/api/companies.json')
                .then((response) => {
                    this.companies = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        async updateCompany(company) {
            const url = '/api/companies/' + company.id;
            await  axios.put(url, company)
                .then((response) => {
                    let editedIndex = this.companies.findIndex((v, i) =>v.id === company.id)
                    this.companies.splice(editedIndex, 1, company)
                })
                .catch((error) => {
                    console.log(error);
                    return error;
                });
        }
    }
});
