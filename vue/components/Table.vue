<template>
    <div>
      <b-table striped hover :items="items" :fields="fields">
        <template v-slot:cell(actions)="data">
          <b-button size="sm" @click="editItem(data.item)">Edit</b-button>
          <b-button size="sm" variant="danger" @click="deleteItem(data.item)" style="margin-left: 6px;">Delete</b-button>
        </template>
      </b-table>
    </div>
  </template>
  
  <script>
  export default {
    name: "Table",

    props:{
      fields:{
        type: Array,
        default: []
      },
      items:{
        type: Array,
        default: []
      }
    },

    data() {
      return {
        
      };
    },
    methods: {
      editItem(item) {
        // Lógica para editar o item
      },
      deleteItem(item) {
        // Lógica para excluir o item
      }
    }
  };
  </script>
  