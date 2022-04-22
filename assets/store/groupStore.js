import {defineStore} from "pinia";
import axios from "axios";

export const useGroupStore = defineStore({
    id: 'group',
    state: () => {
        return {
            groups: []
        }
    },
    getters: {
        getGroups(state) {
            return state.groups;
        }
    },
    actions: {
        async addGroup ( group ) {
            console.log('addGroup')
            console.log(group)
            await axios.post('/api/groups.json', group)
                .then( (response) => {
                    this.groups.push(group);
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        async removeGroup ( group ) {
            const url = '/api/groups/' + group.id;
            await  axios.delete(url, group)
                .then((response) => {
                    const i = this.groups.findIndex(s => s.id === group.id);
                    if ( i > -1 ) this.groups.splice(i, 1);
                })
                .catch((error) => {
                    console.log(error);
                    return error;
                });
        },
        async fetchGroups() {
            await axios.get('/api/groups.json')
                .then((response) => {
                    this.groups = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        async updateGroup(group) {
            const url = '/api/groups/' + group.id;
            await  axios.put(url, group)
                .then((response) => {
                    let editedIndex = this.groups.findIndex((v, i) =>v.id === group.id)
                    this.groups.splice(editedIndex, 1, group)
                })
                .catch((error) => {
                    console.log(error);
                    return error;
                });
        }
    }
});
