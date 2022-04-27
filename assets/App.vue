<template>
  <div class="q-pa-md">
    <q-layout view="lHh Lpr lff" >
      <q-header elevated class="bg-cyan-8">
        <q-toolbar>
          <q-btn flat @click="drawer = !drawer" round dense icon="menu" />
          <q-toolbar-title>Langileak</q-toolbar-title>
        </q-toolbar>

      </q-header>

      <q-drawer
          v-model="drawer"

          :width="200"
          :breakpoint="400"
      >
        <q-scroll-area style="height: calc(100% - 150px); margin-top: 150px; border-right: 1px solid #ddd">
          <q-list padding>

            <q-item clickable v-ripple active-class="my-menu-link" to="/" exact>
              <q-item-section avatar>
                <q-icon name="engineering" />
              </q-item-section>

              <q-item-section>
                Langileak
              </q-item-section>
            </q-item>

            <q-item clickable v-ripple active-class="my-menu-link" to="/group" exact>
              <q-item-section avatar>
                <q-icon name="group" />
              </q-item-section>

              <q-item-section>
                Taldeak
              </q-item-section>
            </q-item>

          </q-list>
        </q-scroll-area>

        <q-img class="absolute-top" src="https://cdn.quasar.dev/img/material.png" style="height: 150px">
          <div class="absolute-bottom bg-transparent">
            <q-avatar size="56px" class="q-mb-sm">
              <img src="https://cdn.quasar.dev/img/avatar.png" alt="" />
            </q-avatar>
            <div class="text-weight-bold">Ruth Gonzalez</div>
            <div>@rgonzalez</div>
          </div>
        </q-img>
      </q-drawer>

      <q-page-container>
        <router-view></router-view>
      </q-page-container>
    </q-layout>
  </div>
</template>

<script>
import { ref } from 'vue'

export default {
  setup () {
    return {
      drawer: ref(false)
    }
  }
}
</script>

<style lang="scss">
  .my-menu-link {
    color: white;
    background: #F2C037;
  }
</style>
