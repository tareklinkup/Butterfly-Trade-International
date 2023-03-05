<div id="salesInvoice">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<material-sales-invoice v-bind:sales_id="salesId"></material-sales-invoice>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/components/material-sale-invoice.js"></script>
<script src="<?php echo base_url();?>assets/js/moment.min.js"></script>
<script>
	new Vue({
		el: '#salesInvoice',
		data(){
			return {
				salesId: parseInt('<?php echo $salesId;?>')
			}
		}
	})
</script>


