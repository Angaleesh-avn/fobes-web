import './bootstrap';

import Vue from 'vue/dist/vue.js';
import '../app.css'

import KanbanBoard from './components/Company/ApplicationKanbanBoard.vue';

Vue.component("application-kanban-board", KanbanBoard);

const app = new Vue({
    el: '#app'
});
