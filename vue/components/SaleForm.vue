<template>
    <div id="saleForm">

        <template v-if="step == 1">
            
            <h4>Select sale products</h4>
            
            <div class="list-group">
                <label v-for="item in products" :key="item.id" class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ item.name }}</span>
                        <div class="form-check">
                        <input class="form-check-input" type="checkbox" v-model="item.selected">
                        </div>
                    </div>
                </label>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <b-button variant="primary" @click="filterSelectedProducts">Next</b-button>
            </div>

        </template>

        <template v-if="step == 2">
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
            <b-row v-for="(p, i) in list_products_sale" :key="p.id" class="mb-3">
                <b-col md="5">
                    <b-form-input v-model="p.name" disabled></b-form-input>
                </b-col>
                <b-col md="2">
                    <b-form-input type="text" v-model="p.price_formatted" disabled></b-form-input>
                </b-col>
                <b-col md="1">
                    <b-form-input type="number" min="1" v-model="p.quantity"></b-form-input>
                </b-col>
                <b-col md="2">
                    <b-form-input type="text" :value="sumTotalByProduct(p.price, p.quantity)" disabled></b-form-input>
                </b-col>
                <b-col md="2">
                    <b-form-input type="text" :value="sumTaxByProduct(p.price, p.quantity, p.tax_percentage)" disabled></b-form-input>
                </b-col>
            </b-row>
            <div class="d-flex justify-content-end mt-3">
                <b-button variant="primary" class="me-lg-4">Total sale: {{total}}</b-button>
                <b-button variant="primary" class="me-lg-4">Total tax: {{total_tax}}</b-button>
                    
                <b-button variant="secondary" @click="step = 1" class="me-lg-4">Back</b-button>
                <b-button variant="primary" @click="record">Save</b-button>
            </div>
        </template>
        
    </div>
</template>
  
<script>
  
  export default {
    name: "saleForm",
    components: {
    },
    props:{
        products:{
            type: Array,
            default: []
        }
    },
    data(){
        return{
            step: 1,
            list_products_sale: [],
            total: 0,
            total_tax: 0,
        }
    },
    watch: {
        list_products_sale: {
            handler(newValue, oldValue) {
                let products = this.$c(newValue).map((p)=>{
                    let total = p.price * p.quantity
                    return {
                        total: total,
                        total_tax: total * (p.tax_percentage / 100)
                    }
                })

                this.total = (products.sum('total') / 100).toFixed(2)
                this.total_tax = (products.sum('total_tax') / 100).toFixed(2)

            },
            deep: true 
        }
    },
    methods:{

        filterSelectedProducts(){
            let products = this.$c(this.products).where('selected', true)
            this.list_products_sale = products.all()
            this.step = 2
        },

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

        record(){
            this.$emit('record', this.list_products_sale)
        }

    }
  }

</script>
  
<style scoped>
.list-group-item {
    cursor: pointer;
}
.list-group-item:hover {
    background: #efeeee;
}

.list-group{
    height: 320px;
    overflow: auto;
}

</style>
  