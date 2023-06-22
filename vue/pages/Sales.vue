<template>
    <div id="sales">
        <notifications></notifications>
        <b-container>

            <b-row class="mt-4 mb-2">
                <b-col cols="10">
                  <h3>Sales</h3>
                </b-col>
                <b-col cols="2">
                  <b-button variant="primary" v-b-modal.sales-modal @click="active_sale_form = true">New +</b-button>
                </b-col>
            </b-row>

            <b-table striped hover :items="sales" :fields="fields">
                <template v-slot:cell(actions)="data">
                    <b-button size="sm" variant="success" @click="viewItem(data.item)" style="margin-left: 6px;">View</b-button>                   
                    <b-button size="sm" variant="danger" @click="deleteItem(data.item)" style="margin-left: 6px;">Delete</b-button>
                </template>
            </b-table>

            <b-modal id="sales-modal" title="New sale" size="xl"  hide-footer  @hidden="resetSale">
                <SaleForm :products="products" @record="record" v-if="active_sale_form"/>
            </b-modal>

            <b-modal id="sale-view-modal" title="New sale" size="xl" hide-footer>
                <b-row>
                    <b-col md="5">
                      <p>Name</p>
                    </b-col>
                    <b-col md="2">
                        <p>Price</p>
                    </b-col>
                    <b-col md="1">
                        <p>Quantity</p>
                    </b-col>
                    <b-col md="2">
                        <p>Total</p>
                    </b-col>
                    <b-col md="2">
                      <p>Total tax</p>
                    </b-col>
                </b-row>
                <b-row v-for="(p, i) in list_products_sale.products" :key="p.id" class="mb-3">
                    <b-col md="5">
                        <b-form-input v-model="p.name" disabled></b-form-input>
                    </b-col>
                    <b-col md="2">
                        <b-form-input type="text" v-model="p.price" disabled></b-form-input>
                    </b-col>
                    <b-col md="1">
                        <b-form-input type="number" min="1" v-model="p.quantity" disabled></b-form-input>
                    </b-col>
                    <b-col md="2">
                        <b-form-input type="text" v-model="p.value" disabled></b-form-input>
                    </b-col>
                    <b-col md="2">
                        <b-form-input type="text" v-model="p.tax_value" disabled></b-form-input>
                    </b-col>
                </b-row>
                <div class="d-flex justify-content-end mt-3">
                    <b-button variant="primary" class="me-lg-4">Total sale: {{ list_products_sale.total }}</b-button>
                    <b-button variant="primary" class="me-lg-4">Total tax: {{ list_products_sale.tax_value }}</b-button>
                    <b-button variant="secondary" @click="$bvModal.hide('sale-view-modal')" class="me-lg-4">Close</b-button>
                </div>
            </b-modal>

        </b-container>
    </div>
  </template>
  
  <script>
  import SaleForm from "../components/SaleForm.vue"

  export default {
    name: "Products",
    components: {
        SaleForm
    },
    data(){
        return{
            products: [],
            list_products_sale: [],
            sales: [],
            fields: [
                { key: "id", label: "id" },
                { key: "total", label: "Total value" },
                { key: "tax_value", label: "Total tax" },
                { key: "count_products", label: "Products" },
                { key: "actions", label: "Actions" }
            ],
            active_sale_form: false // reset component
        }
    },

    created(){
        this.getSales()
        this.getProductsActive()
    },

    methods:{

        sumTaxByProduct(price, quantity, tax_percent){

            const valueInReal = (price / 100).toFixed(2);
            const totalValue = valueInReal * quantity;
            const taxValue = (totalValue * (tax_percent / 100)).toFixed(2);

            return '$ ' + taxValue;
        },

        sumTotalByProduct(price, quantity){
            const valueInDolar = (price / 100).toFixed(2);
            const total = (valueInDolar * quantity).toFixed(2);
            return '$ ' + total;
        },

        viewItem(item){
            this.$bvModal.show('sale-view-modal')
            console.log('item', item)
            this.list_products_sale = item
        },

        deleteItem(item){

            this.$http.delete('http://localhost:8080/sale/'+item.id).then(resp=> {
                this.sales = this.$c(this.sales).filter((s)=>{
                    return s.id != item.id
                }).all()
                notify('Deleted sale!');
            }).catch(error => {
                notify({
                    text: error.response.data.message,
                    theme: 'red'
                })
            })

        },

        getSales(){
            this.$http.get('http://localhost:8080/sales').then(resp=> {
                this.sales = this.$c(resp.data.data).toArray()

                this.sales.sort(function(a, b) {
                  return b.id - a.id;
                })
            }).catch(error => {
                notify({
                    text: error.response.data.message,
                    theme: 'red'
                })
            })
        },

        getProductsActive(){
            this.$http.get('http://localhost:8080/products-active').then(resp=> {
                this.products = this.$c(resp.data.data).map((p)=>{
                    p.quantity = 1
                    p.selected = false
                    return p
                }).all()
            }).catch(error => {
                notify({
                    text: error.response.data.message,
                    theme: 'red'
                })
            })
        },

        resetSale(){
            this.active_sale_form = false
            this.products = this.$c(this.products).map((p)=>{
                p.quantity = 1
                p.selected = false
                return p
            }).all()
        },

        record(products){

            let payload = this.$c(products).map((p)=>{
                return {
                    product_id: p.id,
                    quantity: p.quantity,
                }
            }).all()

            this.$http.post('http://localhost:8080/sale', payload).then(resp=> {
                this.getSales()
                this.$bvModal.hide('sales-modal')
                
                notify('Registered sale!');
            }).catch(error => {
                notify({
                    text: error.response.data.message,
                    theme: 'red'
                })
            })
        }
    }

  }
  </script>
  
  <style>
  
  </style>
  