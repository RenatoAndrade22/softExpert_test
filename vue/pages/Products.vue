<template>
    <div id="products">
        <notifications></notifications>
        <b-container>
            <b-row class="mt-4 mb-2">
                <b-col cols="10">
                  <h3>Products</h3>
                </b-col>
                <b-col cols="2">
                  <b-button variant="primary" v-b-modal.product-modal>New +</b-button>
                </b-col>
            </b-row>

            <b-table striped hover :items="products" :fields="fields">
                <template v-slot:cell(actions)="data">
                  <b-button size="sm" @click="editItem(data.item)">Edit</b-button>
                  <b-button size="sm" variant="danger" @click="deleteItem(data.item)" style="margin-left: 6px;">Delete</b-button>
                </template>
            </b-table>

            <b-modal id="product-modal" :title="form.id ? 'Update product' : 'New product'" @ok="record" @hidden="modalHidden">

                <b-form-select 
                    v-model="form.products_type_id" 
                    :options="optionsProductTypes"
                    value-field="id"
                    text-field="name"
                    class="form-control"  
                />

                <b-form-input
                    v-model="form.name"
                    placeholder="* Product name"
                    class="mt-2"
                />

                <money 
                    v-model="form.price" 
                    v-bind="money" 
                    class="form-control mt-2"  
                /> 

                <p v-if="form_required" style="color:red;">fill in all mandatory fields (*)</p>
                
                <template #modal-footer>
                    <div class="d-flex justify-content-end">
                      <b-button variant="secondary" @click="$bvModal.hide('product-modal')" class="me-2">Cancel</b-button>
                      <b-button variant="danger" @click="record">Save</b-button>
                    </div>
                </template>
            </b-modal>

            <b-modal id="delete-product-modal" title="Product" hide-cancel-button>
                <p>This product cannot be deleted as there are sales with it, or just leave this product inactive.</p>
                <template #modal-footer>
                    <div class="d-flex justify-content-end">
                      <b-button variant="danger" @click="$bvModal.hide('delete-product-modal')">ok</b-button>
                    </div>
                </template>
            </b-modal>

        </b-container>
    </div>
  </template>
  
  <script>
  import Table from "../components/Table.vue"
  import {Money} from 'v-money'

  export default {
    name: "Products",
    components: {
        Table,
        Money,
    },
    data(){
        return{
            products: [],
            optionsProductTypes: [
                {
                    name: '(*) Select product type',
                    id: null,
                }
            ],
            fields: [
                { key: "name", label: "Name" },
                { key: "price_formatted", label: "Price" },
                { key: "total_sales", label: "Sales" },
                { key: "actions", label: "Actions" }
            ],

            money: {
                decimal: ',',
                thousands: '.',
                prefix: '$ ',
                suffix: '',
                precision: 2,
                masked: false
            },
            form:{
                id: null,
                price: 0,
                name: null,
                products_type_id: null,
            },

            form_required: false,

        }
    },

    created(){
        this.getProducts()
        this.getProductTypes()
    },

    methods:{

        editItem(item){
            this.form.id = item.id
            this.form.price = item.price_formatted
            this.form.name = item.name
            this.form.products_type_id = item.products_type_id
            this.$bvModal.show('product-modal')
        },

        deleteItem(item){

            if(item.total_sales > 0){
                this.$bvModal.show('delete-product-modal')
                return false
            }

            this.$http.delete('http://localhost:8080/product/'+item.id).then(resp=> {
                this.products = this.$c(this.products).filter((p)=>{
                    return p.id != item.id
                }).all()
                notify('Deleted product!');
            }).catch(error => {
                notify({
                    text: error.response.data.message,
                    theme: 'red'
                })
            })

        },

        getProducts(){
            this.$http.get('http://localhost:8080/products')
            .then(resp=> {
                this.products = resp.data.data
            }).catch(error => {
                notify({
                    text: error.response.data.message,
                    theme: 'red'
                })
            })
        },

        getProductTypes(){
            this.$http.get('http://localhost:8080/product-type-active')
            .then(resp=> {
                this.optionsProductTypes.push(...resp.data.data)
            }).catch(error => {
                notify({
                    text: error.response.data.message,
                    theme: 'red'
                })
            })
        },

        modalHidden(){
            this.form.id = null
            this.form.products_type_id = null
            this.form.name = null
            this.form.price = 0
        },

        record(){
            if(this.validate()){

                this.form_required = false

                if(this.form.id){

                    this.$http.put('http://localhost:8080/product/'+this.form.id, this.form)
                    .then(resp=> {
                        console.log('resp', resp)
                        this.products = this.$c(this.products).map((item)=>{
                            if(item.id == this.form.id){
                                item.name = resp.data.data.name
                                item.price = resp.data.data.price_formatted
                                item.price_formatted = resp.data.data.price_formatted
                            }
                            return item
                        }).all()

                        this.$bvModal.hide('product-modal')

                        notify('Updated product!');

                    }).catch(error => {
                        notify({
                            text: error.response.data.message,
                            theme: 'red'
                        })
                    })

                }else{
                    this.$http.post('http://localhost:8080/product', this.form)
                    .then(resp=> {
                        this.products.unshift(resp.data.data); 
                        this.$bvModal.hide('product-modal')
                        notify('Registered product!');
                    }).catch(error => {
                        notify({
                            text: error.response.data.message,
                            theme: 'red'
                        })
                    })
                }

                
            }else{
                
                this.form_required = true
                return false

            }
        },

        validate(){
            if(!this.form.name || this.form.price == 0 || !this.form.products_type_id){
                return false
            }
            return true
        }
    }
  };
  </script>
  
  <style>
  
  </style>
  