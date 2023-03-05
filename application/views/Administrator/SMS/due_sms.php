<div id="sms">
    <div class="row">
        <div class="col-md-5">
            <form v-on:submit.prevent="sendSms">
                <div class="form-group" style="margin-top: 10px;">
                    <button type="submit" class="btn btn-primary btn-xs pull-left" v-bind:style="{display: onProgress ? 'none' : ''}"> <i class="fa fa-send"></i> Send </button>
                    <button type="button" class="btn btn-primary btn-xs pull-left" disabled style="display:none" v-bind:style="{display: onProgress ? '' : 'none'}"> Please Wait .. </button>
                </div>
            </form>
        </div>
    </div>
    <div class="row" style="margin-top: 25px;">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Select All &nbsp; <input type="checkbox" v-on:click="selectAll"></th>
                            <th>Customer Code</th>
                            <th>Customer Name</th>
                            <th>Mobile</th>
                            <th>Address</th>
                            <th>Due Amount</th>
                        </tr>
                    </thead>
                    <tbody style="display:none" v-bind:style="{display: customers.length > 0 ? '' : 'none'}">
                        <tr v-for="customer in customers">
                            <td><input type="checkbox" v-bind:value="customer" v-model="selectedCustomers" v-if="customer.Customer_Mobile.match(regexMobile)"></td>
                            <td>{{ customer.Customer_Code }}</td>
                            <td>{{ customer.Customer_Name }}</td>
                            <td><span class="label label-md arrowed" v-bind:class="[customer.Customer_Mobile.match(regexMobile) ? 'label-info' : 'label-danger']">{{ customer.Customer_Mobile }}</span></td>
                            <td>{{ customer.Customer_Address }}</td>
                            <td>{{ parseFloat(customer.dueAmount).toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>

<script>
    new Vue({
        el:'#sms',
        data(){
            return {
                customers:[],
                selectedCustomers: [],
                onProgress: false,
                regexMobile: /^01[13-9][\d]{8}$/
            }
        },
        created(){
            this.getDue();
        },
        methods:{
           
            getDue() {
                axios.get('/get_customer_due').then(res => {
					this.customers = res.data.filter(d => parseFloat(d.dueAmount) != 0);
				})
            },
            selectAll(){
                let checked = event.target.checked;
                if(checked){
                    this.selectedCustomers = this.customers.filter(c => c.Customer_Mobile.match(this.regexMobile));
                    console.log(this.selectedCustomers);
                } else {
                    this.selectedCustomers = [];
                }
            },
           
            sendSms(){
                if(this.selectedCustomers.length == 0){
                    alert('Select customer');
                    return;
                }

                let data = {
                    numbers: this.selectedCustomers 
                }

                this.onProgress = true;
                axios.post('/send_due_sms', data).then(res => {
                    let r = res.data;
                    alert(r.message);
                    this.onProgress = false;
                })
            }
        }
    })
</script>