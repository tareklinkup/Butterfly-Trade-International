<style>
	.v-select{
		margin-bottom: 5px;
	}
	.v-select .dropdown-toggle{
		padding: 0px;
	}
	.v-select input[type=search], .v-select input[type=search]:focus{
		margin: 0px;
	}
	.v-select .vs__selected-options{
		overflow: hidden;
		flex-wrap:nowrap;
	}
	.v-select .selected-tag{
		margin: 2px 0px;
		white-space: nowrap;
		position:absolute;
		left: 0px;
	}
	.v-select .vs__actions{
		margin-top:-5px;
	}
	.v-select .dropdown-menu{
		width: auto;
		overflow-y:auto;
	}
	#branchDropdown .vs__actions button{
		display:none;
	}
	#branchDropdown .vs__actions .open-indicator{
		height:15px;
		margin-top:7px;
	}
</style>

<div id="sales" class="row">
	<div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;margin-bottom:5px;">
		<div class="row">
			<div class="form-group">
				<label class="col-sm-1 control-label no-padding-right"> Invoice no </label>
				<div class="col-sm-2">
					<input type="text" id="invoiceNo" class="form-control" v-model="sales.invoiceNo" readonly />
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-1 control-label no-padding-right"> Sales By </label>
				<div class="col-sm-2">
					<v-select v-bind:options="employees" v-model="selectedEmployee" label="Employee_Name" placeholder="Select Employee"></v-select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-1 control-label no-padding-right"> Sales From </label>
				<div class="col-sm-2">
					<v-select id="branchDropdown" v-bind:options="branches" label="Brunch_name" v-model="selectedBranch" disabled></v-select>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-3">
					<input class="form-control" id="salesDate" type="date" v-model="sales.salesDate" v-bind:disabled="userType == 'u' ? true : false"/>
				</div>
			</div>
		</div>
	</div>


	<div class="col-xs-9 col-md-9 col-lg-9">
		<div class="widget-box">
			<div class="widget-header">
				<h4 class="widget-title">Sales Information</h4>
				<div class="widget-toolbar">
					<a href="#" data-action="collapse">
						<i class="ace-icon fa fa-chevron-up"></i>
					</a>

					<a href="#" data-action="close">
						<i class="ace-icon fa fa-times"></i>
					</a>
				</div>
			</div>

			<div class="widget-body">
				<div class="widget-main">

					<div class="row">
						<div class="col-sm-5">
							<div class="form-group">
								<label class="col-sm-4 control-label no-padding-right"> Customer </label>
								<div class="col-sm-7">
									<v-select v-bind:options="customers" label="display_name" v-model="selectedCustomer" v-on:input="customerOnChange"></v-select>
								</div>
								<div class="col-sm-1" style="padding: 0;">
									<a href="<?= base_url('customer')?>" class="btn btn-xs btn-danger" style="height: 25px; border: 0; width: 27px; margin-left: -10px;" target="_blank" title="Add New Customer"><i class="fa fa-plus" aria-hidden="true" style="margin-top: 5px;"></i></a>
								</div>
							</div>

							<div class="form-group" style="display:none;" v-bind:style="{display: selectedCustomer.Customer_Type == 'G' ? '' : 'none'}">
								<label class="col-sm-4 control-label no-padding-right"> Name </label>
								<div class="col-sm-8">
									<input type="text" id="customerName" placeholder="Customer Name" class="form-control" v-model="selectedCustomer.Customer_Name" v-bind:disabled="selectedCustomer.Customer_Type == 'G' ? false : true" />
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4 control-label no-padding-right"> Mobile No </label>
								<div class="col-sm-8">
									<input type="text" id="mobileNo" placeholder="Mobile No" class="form-control" v-model="selectedCustomer.Customer_Mobile" v-bind:disabled="selectedCustomer.Customer_Type == 'G' ? false : true" />
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4 control-label no-padding-right"> Address </label>
								<div class="col-sm-8">
									<textarea id="address" placeholder="Address" class="form-control" v-model="selectedCustomer.Customer_Address" v-bind:disabled="selectedCustomer.Customer_Type == 'G' ? false : true"></textarea>
								</div>
							</div>
						</div>

						<div class="col-sm-5">
							<form v-on:submit.prevent="addToCart">
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right"> Category </label>
									<div class="col-sm-8">
										<v-select v-bind:options="categories" v-model="selectedCategory" label="ProductCategory_Name" v-on:input="categoryOnChange" placeholder="Select Category"></v-select>
									</div>
									<div class="col-sm-1" style="padding: 0;">
										<a href="<?= base_url('category')?>" class="btn btn-xs btn-danger" style="height: 25px; border: 0; width: 27px; margin-left: -10px;" target="_blank" title="Add New Category"><i class="fa fa-plus" aria-hidden="true" style="margin-top: 5px;"></i></a>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right"> Material </label>
									<div class="col-sm-8">
										<v-select v-bind:options="materials" v-model="selectedMaterial" label="display_text" v-on:input="materialOnChange"></v-select>
									</div>
									<div class="col-sm-1" style="padding: 0;">
										<a href="<?= base_url('material')?>" class="btn btn-xs btn-danger" style="height: 25px; border: 0; width: 27px; margin-left: -10px;" target="_blank" title="Add New Material"><i class="fa fa-plus" aria-hidden="true" style="margin-top: 5px;"></i></a>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right"> Sale Rate </label>
									<div class="col-sm-9">
										<input type="number" id="salesRate" placeholder="Rate" step="0.01" class="form-control" v-model="selectedMaterial.sale_rate" v-on:input="materialTotal"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right"> Quantity </label>
									<div class="col-sm-9">
										<input type="number" step="0.01" id="quantity" placeholder="Qty" class="form-control" ref="quantity" v-model="selectedMaterial.quantity" v-on:input="materialTotal" autocomplete="off" required/>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right"> Amount </label>
									<div class="col-sm-9">
										<input type="text" id="materialTotal" placeholder="Amount" class="form-control" v-model="selectedMaterial.total" readonly />
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right"> </label>
									<div class="col-sm-9">
										<button type="submit" class="btn btn-default pull-right">Add to Cart</button>
									</div>
								</div>
							</form>

						</div>
						<div class="col-sm-2">
							<div style="display:none;" v-bind:style="{display:sales.isService == 'true' ? 'none' : ''}">
								<div class="text-center" style="display:none;" v-bind:style="{color: materialStock > 0 ? 'green' : 'red', display: selectedMaterial.material_id == '' ? 'none' : ''}">{{ materialStockText }}</div class="text-center">

								<input type="text" id="materialStock" v-model="materialStock" readonly style="border:none;font-size:20px;width:100%;text-align:center;color:green"><br>
								<input type="text" id="stockUnit" v-model="selectedMaterial.Unit_Name" readonly style="border:none;font-size:12px;width:100%;text-align: center;"><br><br>
							</div>
							<input type="password" ref="materialPurchaseRate" v-model="selectedMaterial.purchase_rate" v-on:mousedown="toggleMaterialPurchaseRate" v-on:mouseup="toggleMaterialPurchaseRate"  readonly title="Purchase rate (click & hold)" style="font-size:12px;width:100%;text-align: center;">

						</div>
					</div>
				</div>
			</div>
		</div>


		<div class="col-xs-12 col-md-12 col-lg-12" style="padding-left: 0px;padding-right: 0px;">
			<div class="table-responsive">
				<table class="table table-bordered" style="color:#000;margin-bottom: 5px;">
					<thead>
						<tr class="">
							<th style="width:10%;color:#000;">Sl</th>
							<th style="width:25%;color:#000;">Category</th>
							<th style="width:20%;color:#000;">Material Name</th>
							<th style="width:7%;color:#000;">Qty</th>
							<th style="width:8%;color:#000;">Rate</th>
							<th style="width:15%;color:#000;">Total Amount</th>
							<th style="width:15%;color:#000;">Action</th>
						</tr>
					</thead>
					<tbody style="display:none;" v-bind:style="{display: cart.length > 0 ? '' : 'none'}">
						<tr v-for="(material, sl) in cart">
							<td>{{ sl + 1 }}</td>
							<td>{{ material.categoryName }}</td>
							<td>{{ material.name }}</td>
							<td>{{ material.quantity }}</td>
							<td>{{ material.salesRate }}</td>
							<td>{{ material.total }}</td>
							<td><a href="" v-on:click.prevent="removeFromCart(sl)"><i class="fa fa-trash"></i></a></td>
						</tr>

						<tr>
							<td colspan="7"></td>
						</tr>

						<tr style="font-weight: bold;">
							<td colspan="4">Note</td>
							<td colspan="3">Total</td>
						</tr>

						<tr>
							<td colspan="4"><textarea style="width: 100%;font-size:13px;" placeholder="Note" v-model="sales.note"></textarea></td>
							<td colspan="3" style="padding-top: 15px;font-size:18px;">{{ sales.total }}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>


	<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
		<div class="widget-box">
			<div class="widget-header">
				<h4 class="widget-title">Amount Details</h4>
				<div class="widget-toolbar">
					<a href="#" data-action="collapse">
						<i class="ace-icon fa fa-chevron-up"></i>
					</a>

					<a href="#" data-action="close">
						<i class="ace-icon fa fa-times"></i>
					</a>
				</div>
			</div>

			<div class="widget-body">
				<div class="widget-main">
					<div class="row">
						<div class="col-sm-12">
							<div class="table-responsive">
								<table style="color:#000;margin-bottom: 0px;border-collapse: collapse;">
									<tr>
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label no-padding-right">Sub Total</label>
												<div class="col-sm-12">
													<input type="number" id="subTotal" class="form-control" v-model="sales.subTotal" readonly />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label no-padding-right"> Vat </label>
												<div class="col-sm-4">
													<input type="number" id="vatPercent" class="form-control" v-model="vatPercent" v-on:input="calculateTotal"/>
												</div>
												<label class="col-sm-1 control-label no-padding-right">%</label>
												<div class="col-sm-7">
													<input type="number" id="vat" readonly="" class="form-control" v-model="sales.vat"/>
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label no-padding-right">Discount Persent</label>

												<div class="col-sm-4">
													<input type="number" id="discountPercent" class="form-control" v-model="discountPercent" v-on:input="calculateTotal"/>
												</div>

												<label class="col-sm-1 control-label no-padding-right">%</label>

												<div class="col-sm-7">
													<input type="number" id="discount" class="form-control" v-model="sales.discount" v-on:input="calculateTotal"/>
												</div>

											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label no-padding-right">Transport Cost</label>
												<div class="col-sm-12">
													<input type="number" class="form-control" v-model="sales.transportCost" v-on:input="calculateTotal"/>
												</div>
											</div>
										</td>
									</tr>

									<tr style="display:none;">
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label no-padding-right">Round Of</label>
												<div class="col-sm-12">
													<input type="number" id="roundOf" class="form-control" />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label no-padding-right">Total</label>
												<div class="col-sm-12">
													<input type="number" id="total" class="form-control" v-model="sales.total" readonly />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label no-padding-right">Paid</label>
												<div class="col-sm-12">
													<input type="number" id="paid" class="form-control" v-model="sales.paid" v-on:input="calculateTotal" v-bind:disabled="selectedCustomer.Customer_Type == 'G' ? true : false"/>
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label">Due</label>
												<div class="col-sm-6">
													<input type="number" id="due" class="form-control" v-model="sales.due" readonly/>
												</div>
												<div class="col-sm-6">
													<input type="number" id="previousDue" class="form-control" v-model="sales.previousDue" readonly style="color:red;"  />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<div class="col-sm-6">
													<input type="button" class="btn btn-default btn-sm" value="Sale" v-on:click="saveSales" v-bind:disabled="saleOnProgress ? true : false" style="color: black!important;margin-top: 0px;width:100%;padding:5px;font-weight:bold;">
												</div>
												<div class="col-sm-6">
													<a class="btn btn-info btn-sm" v-bind:href="`/sales/${sales.isService == 'true' ? 'service' : 'material'}`" style="color: black!important;margin-top: 0px;width:100%;padding:5px;font-weight:bold;">New Sale</a>
												</div>
											</div>
										</td>
									</tr>

								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url();?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#sales',
		data(){
			return {
				sales:{
					salesId: parseInt('<?php echo $salesId;?>'),
					invoiceNo: '<?php echo $invoice;?>',
					salesBy: '<?php echo $this->session->userdata("FullName"); ?>',
					salesFrom: '',
					salesDate: '',
					customerId: '',
					employeeId: null,
					subTotal: 0.00,
					discount: 0.00,
					vat: 0.00,
					transportCost: 0.00,
					total: 0.00,
					paid: 0.00,
					previousDue: 0.00,
					due: 0.00,
					note: ''
				},
				vatPercent: 0,
				discountPercent: 0,
				cart: [],
				employees: [],
                selectedEmployee: null,
				branches: [],
				selectedBranch: {
					brunch_id: "<?php echo $this->session->userdata('BRANCHid'); ?>",
					Brunch_name: "<?php echo $this->session->userdata('Brunch_name'); ?>"
				},
				customers: [],
				selectedCustomer:{
					Customer_SlNo: '',
					Customer_Code: '',
					Customer_Name: '',
					display_name: 'Select Customer',
					Customer_Mobile: '',
					Customer_Address: '',
					Customer_Type: ''
				},
				oldCustomerId: null,
				oldPreviousDue: 0,
				categories: [],
				selectedCategory: null,
				materials: [],
				selectedMaterial: {
					material_id: '',
					display_text: 'Select Material',
					name: '',
					Unit_Name: '',
					quantity: 0,
					purchase_rate: '',
					sale_rate: 0.00,
					total: 0.00
				},
				materialPurchaseRate: '',
				materialStockText: '',
				materialStock: '',
				saleOnProgress: false,
				userType: '<?php echo $this->session->userdata("accountType");?>'
			}
		},
		created(){
			this.sales.salesDate = moment().format('YYYY-MM-DD');
			this.getEmployees();
			this.getBranches();
			this.getCustomers();
			this.getCategories();

			if(this.sales.salesId != 0){
				this.getSales();
			}
		},
		methods:{
			getEmployees(){
				axios.get('/get_employees').then(res => {
						this.employees = res.data;
				})
			},
			getBranches(){
				axios.get('/get_branches').then(res=>{
					this.branches = res.data;
				})
			},
			getCustomers(){
				axios.post('/get_customers', {customerType: this.sales.salesType}).then(res=>{
					this.customers = res.data;
					this.customers.unshift({
						Customer_SlNo: 'C01',
						Customer_Code: '',
						Customer_Name: '',
						display_name: 'General Customer',
						Customer_Mobile: '',
						Customer_Address: '',
						Customer_Type: 'G'
					})
				})
			},
			getCategories(){
				axios.post('/get_categories').then(res => {
					this.categories = res.data;
				})
			},
			getMaterials(){
				axios.post('/get_materials', {categoryId: this.selectedCategory.MaterialCategory_SlNo}).then(res=>{
                    this.materials = res.data;
				})
			},
			materialTotal(){
				this.selectedMaterial.total = (parseFloat(this.selectedMaterial.quantity) * parseFloat(this.selectedMaterial.sale_rate)).toFixed(2);
			},
			onSalesTypeChange(){
				this.selectedCustomer = {
					Customer_SlNo: '',
					Customer_Code: '',
					Customer_Name: '',
					display_name: 'Select Customer',
					Customer_Mobile: '',
					Customer_Address: '',
					Customer_Type: ''
				}
				this.getCustomers();

				this.clearMaterial();
				this.getMaterials();
			},
			customerOnChange(){
				if(this.selectedCustomer.Customer_SlNo == ''){
					return;
				}
				if(event.type == 'readystatechange'){
					return;
				}

				if(this.sales.salesId != 0 && this.oldCustomerId != parseInt(this.selectedCustomer.Customer_SlNo)){
					let changeConfirm = confirm('Changing customer will set previous due to current due amount. Do you really want to change customer?');
					if(changeConfirm == false){
						return;
					}
				} else if(this.sales.salesId != 0 && this.oldCustomerId == parseInt(this.selectedCustomer.Customer_SlNo)){
					this.sales.previousDue = this.oldPreviousDue;
					return;
				}
				axios.post('/get_customer_due',{customerId: this.selectedCustomer.Customer_SlNo}).then(res=>{
					if(res.data.length > 0){
						this.sales.previousDue = res.data[0].dueAmount;
					} else {
						this.sales.previousDue = 0;
					}
				})
			},
			categoryOnChange(){
				this.getMaterials();
			},
			async materialOnChange(){
				if(this.selectedMaterial.material_id != '' || this.selectedMaterial.material_id != 0){
					this.materialStock = await axios.post('/get_material_stock', {material_id: this.selectedMaterial.material_id}).then(res => {
						return res.data[0].stock_quantity;
					})

					this.materialStockText = this.materialStock > 0 ? "Available Stock" : "Stock Unavailable";
				}

				this.$refs.quantity.focus();
			},
			toggleMaterialPurchaseRate(){
				//this.materialPurchaseRate = this.materialPurchaseRate == '' ? this.selectedMaterial.purchase_rate : '';
				this.$refs.materialPurchaseRate.type = this.$refs.materialPurchaseRate.type == 'text' ? 'password' : 'text';
			},
			addToCart(){
				let material = {
					materialId : this.selectedMaterial.material_id,
					categoryName: this.selectedMaterial.ProductCategory_Name,
					name: this.selectedMaterial.name,
					salesRate: this.selectedMaterial.sale_rate,
					quantity: this.selectedMaterial.quantity,
					total: this.selectedMaterial.total,
					purchaseRate: this.selectedMaterial.purchase_rate
				}

				if(material.materialId == ''){
					alert('Select Material');
					return;
				}

				if(material.quantity == 0 || material.quantity == ''){
					alert('Enter quantity');
					return;
				}

				if(material.salesRate == 0 || material.salesRate == ''){
					alert('Enter sales rate');
					return;
				}

				if(material.quantity > this.materialStock){
					alert('Stock unavailable');
					return;
				}

				let cartInd = this.cart.findIndex(p => p.materialId == material.materialId);
				if(cartInd > -1){
					this.cart.splice(cartInd, 1);
				}

				this.cart.unshift(material);
				this.clearMaterial();
				this.calculateTotal();
			},
			removeFromCart(ind){
				this.cart.splice(ind, 1);
				this.calculateTotal();
			},
			clearMaterial(){
				this.selectedMaterial = {
					material_id: '',
					display_text: 'Select Material',
					name: '',
					Unit_Name: '',
					quantity: 0,
					purchase_rate: '',
					sale_rate: 0.00,
					total: 0.00
				}
				this.materialStock = '';
				this.materialStockText = '';
			},
			calculateTotal(){
				this.sales.subTotal = this.cart.reduce((prev, curr) => { return prev + parseFloat(curr.total)}, 0).toFixed(2);
				this.sales.vat = ((parseFloat(this.sales.subTotal) * parseFloat(this.vatPercent)) / 100).toFixed(2);
				if(event.target.id == 'discountPercent'){
					this.sales.discount = ((parseFloat(this.sales.subTotal) * parseFloat(this.discountPercent)) / 100).toFixed(2);
				} else {
					this.discountPercent = (parseFloat(this.sales.discount) / parseFloat(this.sales.subTotal) * 100).toFixed(2);
				}
				this.sales.total = ((parseFloat(this.sales.subTotal) + parseFloat(this.sales.vat) + parseFloat(this.sales.transportCost)) - parseFloat(this.sales.discount)).toFixed(2);
				if(this.selectedCustomer.Customer_Type == 'G'){
					this.sales.paid = this.sales.total;
				} else {
					this.sales.due = (parseFloat(this.sales.total) - parseFloat(this.sales.paid)).toFixed(2);
				}
			},
			saveSales(){
				if(this.selectedCustomer.Customer_SlNo == ''){
					alert('Select Customer');
					return;
				}
				if(this.cart.length == 0){
					alert('Cart is empty');
					return;
				}

				if(parseFloat(this.selectedCustomer.Customer_Credit_Limit) < (parseFloat(this.sales.due) + parseFloat(this.sales.previousDue))){
					alert(`Customer credit limit (${this.selectedCustomer.Customer_Credit_Limit}) exceeded`);
					return;
				}

				if(this.selectedEmployee != null && this.selectedEmployee.Employee_SlNo != null){
					this.sales.employeeId = this.selectedEmployee.Employee_SlNo;
				} else {
					this.sales.employeeId = null;
				}

				let url = "/add_material_sale";
				if(this.sales.salesId != 0){
					url = "/update_sales";
				}

				this.sales.customerId = this.selectedCustomer.Customer_SlNo;
				this.sales.salesFrom = this.selectedBranch.brunch_id;

				this.saleOnProgress = true;

				let data = {
					sales: this.sales,
					cart: this.cart
				}

				if(this.selectedCustomer.Customer_Type == 'G'){
					data.customer = this.selectedCustomer;
				}
				axios.post(url, data).then(async res=> {
					let r = res.data;
					if(r.success){
						let conf = confirm('Sale success, Do you want to view invoice?');
						if(conf){
							window.open('/material_sale_invoice/'+r.salesId, '_blank');
							await new Promise(r => setTimeout(r, 1000));
                        }

                        window.location = '/material_sale';
					} else {
						alert(r.message);
						this.saleOnProgress = false;
					}
				})
			},
			getSales(){
				axios.post('/get_material_sales', {salesId: this.sales.salesId}).then(res=>{
					let r = res.data;
					let sales = r.sales[0];
					this.sales.salesBy = sales.AddBy;
					this.sales.salesFrom = sales.SaleMaster_branchid;
					this.sales.salesDate = sales.SaleMaster_SaleDate;
					this.sales.salesType = sales.SaleMaster_SaleType;
					this.sales.customerId = sales.SalseCustomer_IDNo;
					this.sales.employeeId = sales.Employee_SlNo;
					this.sales.subTotal = sales.SaleMaster_SubTotalAmount;
					this.sales.discount = sales.SaleMaster_TotalDiscountAmount;
					this.sales.vat = sales.SaleMaster_TaxAmount;
					this.sales.transportCost = sales.SaleMaster_Freight;
					this.sales.total = sales.SaleMaster_TotalSaleAmount;
					this.sales.paid = sales.SaleMaster_PaidAmount;
					this.sales.previousDue = sales.SaleMaster_Previous_Due;
					this.sales.due = sales.SaleMaster_DueAmount;
					this.sales.note = sales.SaleMaster_Description;

					this.oldCustomerId = sales.SalseCustomer_IDNo;
					this.oldPreviousDue = sales.SaleMaster_Previous_Due;

					this.vatPercent = parseFloat(this.sales.vat) * 100 / parseFloat(this.sales.subTotal);
					this.discountPercent = parseFloat(this.sales.discount) * 100 / parseFloat(this.sales.subTotal);

					this.selectedEmployee = {
						Employee_SlNo: sales.employee_id,
						Employee_Name: sales.Employee_Name
					}

					this.selectedCustomer = {
						Customer_SlNo: sales.SalseCustomer_IDNo,
						Customer_Code: sales.Customer_Code,
						Customer_Name: sales.Customer_Name,
						display_name: sales.Customer_Type == 'G' ? 'General Customer' : `${sales.Customer_Code} - ${sales.Customer_Name}`,
						Customer_Mobile: sales.Customer_Mobile,
						Customer_Address: sales.Customer_Address,
						Customer_Type: sales.Customer_Type
					}

					r.saleDetails.forEach(material => {
						let cartMaterial = {
							materialId: material.material_id,
							categoryName: material.ProductCategory_Name,
							name: material.name,
							salesRate: material.SaleDetails_Rate,
							quantity: material.SaleDetails_TotalQuantity,
							total: material.SaleDetails_TotalAmount,
							purchaseRate: material.Purchase_Rate,
						}

						this.cart.push(cartMaterial);
					})

					this.getCustomers();
					this.getCategories();
				})

				
			}
		}
	})
</script>