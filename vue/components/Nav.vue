<template>
    <div id="nav">
        <div  class="bg-blue">
            <b-container>

                <ul>
                    <router-link to="/">
                        <li :class="active == 'sales' ? 'active' : ''"  @click="active = 'sales'">Sales</li>
                    </router-link>
                    <router-link to="/products">
                        <li :class="active == 'products' ? 'active' : ''" @click="active = 'products'">Products</li>
                    </router-link>
                    <router-link to="/products-types">
                        <li :class="active == 'products-types' ? 'active' : ''" @click="active = 'products-types'">Products types</li>
                    </router-link>
                </ul>
                
            </b-container>
        </div>
    </div>
  </template>
  
  <script>
  
  export default {
    name: "nav",
    components: {
  
    },
    data(){
        return{
            active: null
        }
    },
    mounted() {
        this.active = this.$route.name
    }
  };
  </script>
  
  <style scoped>

    .active{
        background: white;
        color: blue;
    }
    .bg-blue{
        background: blue;
        width: 100%;
        float: left;
        margin-bottom: 25px;
    }

    ul{
        width: 100%;
        float: left;
        padding: 20px;
        margin: 0;
    }

    ul li{
        float: left;
        list-style: none;
        color: white;
        margin-right: 35px;
        padding: 5px 12px;
        border-radius: 5px;
    }

    a{
        text-decoration: none;
        color: darkblue;
    }
  </style>
  