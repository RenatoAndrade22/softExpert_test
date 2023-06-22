<template>
    <div id="products-types">
        <notifications></notifications>
        <b-container>
            <b-row class="mt-4 mb-2">
                <b-col cols="10">
                  <h3>Products types</h3>
                </b-col>
                <b-col cols="2">
                  <b-button variant="primary" v-b-modal.product-type-modal>New +</b-button>
                </b-col>
            </b-row>

            <b-table striped hover :items="productTypes" :fields="fields">
                <template v-slot:cell(status)="data">
                    <p v-if="data.item.status">Active</p>
                    <p v-if="!data.item.status">inactive</p>
                </template>
                <template v-slot:cell(actions)="data">
                    <b-button size="sm" @click="editItem(data.item)">Edit</b-button>
                    <b-button size="sm" variant="danger" @click="deleteItem(data.item)" style="margin-left: 6px;">Delete</b-button>
                </template>
            </b-table>

            <b-modal id="product-type-modal" :title="form.id ? 'Update product type' : 'New product type'" @hidden="modalHidden">

                <b-form-input
                    v-model="form.name"
                    placeholder="* Product type name"
                    class="mt-2"
                />

                <money 
                    v-model="form.tax_percentage" 
                    v-bind="money" 
                    class="form-control mt-2"  
                /> 

                <b-form-group class="mt-2">
                    <b-form-radio v-model="form.status" name="status" :value="true">&nbsp;<span>Active</span></b-form-radio>
                    <b-form-radio v-model="form.status" name="status" :value="false">&nbsp;<span>Inactive</span></b-form-radio>
                </b-form-group>

                <p v-if="form_required" style="color:red;">fill in all mandatory fields (*)</p>

                <template #modal-footer>
                    <div class="d-flex justify-content-end">
                      <b-button variant="secondary" @click="$bvModal.hide('product-type-modal')" class="me-2">Cancel</b-button>
                      <b-button variant="danger" @click="record">Save</b-button>
                    </div>
                </template>

            </b-modal>

            <b-modal id="delete-product-type-modal" title="Product type" hide-cancel-button>
                <p>This type of product has registered products, to delete remove the products or just leave this type of product inactive.</p>
                <template #modal-footer>
                    <div class="d-flex justify-content-end">
                      <b-button variant="danger" @click="$bvModal.hide('delete-product-type-modal')">ok</b-button>
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
            productTypes: [],
        
            fields: [
                { key: "name", label: "Name" },
                { key: "tax_percentage", label: "Tax percentage" },
                { key: "product_count", label: "Products" },
                { key: "status", label: "Status" },
                { key: "actions", label: "Actions" }
            ],
            money: {
                decimal: ',',
                thousands: '.',
                prefix: ' ',
                suffix: '%',
                precision: 2,
                masked: false
            },
            form:{
                id: null,
                tax_percentage: 0,
                name: null,
                status: true
            },

            form_required: false,

        }
    },

    created(){
        this.getProductTypes()
    },

    methods:{

        editItem(item){
            this.form.id = item.id
            this.form.tax_percentage = item.tax_percentage
            this.form.name = item.name
            this.form.status = item.status
            this.$bvModal.show('product-type-modal')
        },

        deleteItem(item){
            if(item.product_count > 0){
                this.$bvModal.show('delete-product-type-modal')
                return false
            }

            this.$http.delete('http://localhost:8080/product-type/'+item.id).then(resp=> {
                this.productTypes = this.$c(this.productTypes).filter((p)=>{
                    return p.id != item.id
                }).all()
                notify('Deleted product type!');
            }).catch(error => {
                notify({
                    text: error.response.data.message,
                    theme: 'red'
                })
            })
        },

        getProductTypes(){
            this.$http.get('http://localhost:8080/product-types')
            .then(resp=> {
                this.productTypes = resp.data.data
            }).catch(error => {
                notify({
                    text: error.response.data.message,
                    theme: 'red'
                })
            })
        },

        modalHidden(){
            this.form.id = null
            this.form.tax_percentage = 0
            this.form.name = null
        },

        record(){
            if(this.validate()){

                this.form_required = false

                if(this.form.id){

                    this.$http.put('http://localhost:8080/product-type/'+this.form.id, this.form)
                    .then(resp=> {

                        this.productTypes = this.$c(this.productTypes).map((item)=>{
                            if(item.id == this.form.id){
                                item.name = resp.data.data.name
                                item.tax_percentage = resp.data.data.tax_percentage
                                item.status = resp.data.data.status
                            }
                            return item
                        }).all()

                        this.$bvModal.hide('product-type-modal')
                        notify('Updated product type!');
                    }).catch(error => {
                        notify({
                            text: error.response.data.message,
                            theme: 'red'
                        })
                    })

                }else{
                    this.$http.post('http://localhost:8080/product-type', this.form)
                    .then(resp=> {
                        this.productTypes.unshift(resp.data.data); 
                        this.$bvModal.hide('product-type-modal')
                        notify('Registered product type!');
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
            if(!this.form.name || this.form.tax_percentage == 0){
                return false
            }
            return true
        }
    }
  };
  </script>
  
  <style>
  
  </style>
  