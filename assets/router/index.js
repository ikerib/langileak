import {createRouter, createWebHashHistory, createWebHistory, RouteRecordRaw} from 'vue-router'
import Employee from '../pages/employee/Employee.vue'
import EmployeeContracts from "../pages/employee/employeeContracts";
import Group from "../pages/group/Group";
import Company from "../pages/company/Company";

const routes = [
    {
        path: '/',
        name: 'Langileak',
        component: Employee
    },
    {
        path: '/udalak',
        name: 'Company',
        component: Company
    },
    {
        path: '/group',
        name: 'Group',
        component: Group
    },
    {
        path: '/langilea/:id',
        name: 'employeeContract',
        component: EmployeeContracts
    },
]
const router = createRouter({
    history: createWebHashHistory(),
    routes
})
export default router;
