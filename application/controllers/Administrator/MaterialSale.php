<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MaterialSale extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->branch = $this->session->userdata('BRANCHid');
        $userId = $this->session->userdata('userId');
         if($userId == '' ){
            redirect("Login");
        }
        $this->load->model('Model_table', "mt", TRUE);
        $this->load->model('SMS_model', 'sms', true);
    }
    
    public function index() {
        $access = $this->mt->userAccess();
        if(!$access){
            redirect(base_url());
        }
        $data['title'] = "Material Sales";
        
        $invoice = $this->mt->generateMaterialSaleInvoice();

        $data['salesId'] = 0;
        $data['invoice'] = $invoice;
        $data['content'] = $this->load->view('Administrator/MaterialSale/material_sale', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function addSales() {
        $res = ['success'=>false, 'message'=>''];
        try{
            $data = json_decode($this->input->raw_input_stream);

            $invoice = $data->sales->invoiceNo;
            $invoiceCount = $this->db->query("select * from tbl_material_sale where SaleMaster_InvoiceNo = ?", $invoice)->num_rows();
            if($invoiceCount != 0){
                $invoice = $this->mt->generateMaterialSaleInvoice();
            }

            $customerId = $data->sales->customerId;
            if(isset($data->customer)){
                $customer = (array)$data->customer;
                unset($customer['Customer_SlNo']);
                unset($customer['display_name']);
                $customer['Customer_Code'] = $this->mt->generateCustomerCode();
                $customer['status'] = 'a';
                $customer['AddBy'] = $this->session->userdata("FullName");
                $customer['AddTime'] = date("Y-m-d H:i:s");
                $customer['Customer_brunchid'] = $this->session->userdata("BRANCHid");

                $this->db->insert('tbl_customer', $customer);
                $customerId = $this->db->insert_id();
            }

            $sales = array(
                'SaleMaster_InvoiceNo' => $invoice,
                'SalseCustomer_IDNo' => $customerId,
                'employee_id' => $data->sales->employeeId,
                'SaleMaster_SaleDate' => $data->sales->salesDate,
                'SaleMaster_TotalSaleAmount' => $data->sales->total,
                'SaleMaster_TotalDiscountAmount' => $data->sales->discount,
                'SaleMaster_TaxAmount' => $data->sales->vat,
                'SaleMaster_Freight' => $data->sales->transportCost,
                'SaleMaster_SubTotalAmount' => $data->sales->subTotal,
                'SaleMaster_PaidAmount' => $data->sales->paid,
                'SaleMaster_DueAmount' => $data->sales->due,
                'SaleMaster_Previous_Due' => $data->sales->previousDue,
                'SaleMaster_Description' => $data->sales->note,
                'Status' => 'a',
                "AddBy" => $this->session->userdata("FullName"),
                'AddTime' => date("Y-m-d H:i:s"),
                'SaleMaster_branchid' => $this->session->userdata("BRANCHid")
            );
    
            $this->db->insert('tbl_material_sale', $sales);
            
            $salesId = $this->db->insert_id();
    
            foreach($data->cart as $cartProduct){
                $saleDetails = array(
                    'SaleMaster_IDNo' => $salesId,
                    'material_id' => $cartProduct->materialId,
                    'SaleDetails_TotalQuantity' => $cartProduct->quantity,
                    'Purchase_Rate' => $cartProduct->purchaseRate,
                    'SaleDetails_Rate' => $cartProduct->salesRate,
                    'SaleDetails_TotalAmount' => $cartProduct->total,
                    'Status' => 'a',
                    'AddBy' => $this->session->userdata("FullName"),
                    'AddTime' => date('Y-m-d H:i:s'),
                    'SaleDetails_BranchId' => $this->session->userdata('BRANCHid')
                );
    
                $this->db->insert('tbl_material_sale_details', $saleDetails);
            }
            $currentDue = $data->sales->previousDue + ($data->sales->total - $data->sales->paid);
            //Send sms
            $customerInfo = $this->db->query("select * from tbl_customer where Customer_SlNo = ?", $customerId)->row();
            $sendToName = $customerInfo->owner_name != '' ? $customerInfo->owner_name : $customerInfo->Customer_Name;

            $message = "{$sendToName},\nToday's your total purchase amount {$data->sales->total} . Deposited {$data->sales->paid} tk. On {$data->sales->salesDate} . Total remaining Balance is tk {$currentDue}. invoice no. {$invoice}";
            $recipient = $customerInfo->Customer_Mobile;
            $this->sms->sendSms($recipient, $message);
    
            $res = ['success'=>true, 'message'=>'Sales Success', 'salesId'=>$salesId];

        } catch (Exception $ex){
            $res = ['success'=>false, 'message'=>$ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateSales() {
        $res = ['success'=>false, 'message'=>''];
        try{
            $data = json_decode($this->input->raw_input_stream);
            $salesId = $data->sales->salesId;

            if(isset($data->customer)){
                $customer = (array)$data->customer;
                unset($customer['Customer_SlNo']);
                unset($customer['display_name']);
                $customer['UpdateBy'] = $this->session->userdata("FullName");
                $customer['UpdateTime'] = date("Y-m-d H:i:s");

                $this->db->where('Customer_SlNo', $data->sales->customerId)->update('tbl_customer', $customer);
            }

            $sales = array(
                'SalseCustomer_IDNo' => $data->sales->customerId,
                'employee_id' => $data->sales->employeeId,
                'SaleMaster_SaleDate' => $data->sales->salesDate,
                'SaleMaster_TotalSaleAmount' => $data->sales->total,
                'SaleMaster_TotalDiscountAmount' => $data->sales->discount,
                'SaleMaster_TaxAmount' => $data->sales->vat,
                'SaleMaster_Freight' => $data->sales->transportCost,
                'SaleMaster_SubTotalAmount' => $data->sales->subTotal,
                'SaleMaster_PaidAmount' => $data->sales->paid,
                'SaleMaster_DueAmount' => $data->sales->due,
                'SaleMaster_Previous_Due' => $data->sales->previousDue,
                'SaleMaster_Description' => $data->sales->note,
                "UpdateBy" => $this->session->userdata("FullName"),
                'UpdateTime' => date("Y-m-d H:i:s"),
                "SaleMaster_branchid" => $this->session->userdata("BRANCHid")
            );
    
            $this->db->where('SaleMaster_SlNo', $salesId);
            $this->db->update('tbl_material_sale', $sales);
            
            $this->db->query("delete from tbl_material_sale_details where SaleMaster_IDNo = ?", $salesId);

            foreach($data->cart as $cartProduct){
                $saleDetails = array(
                    'SaleMaster_IDNo' => $salesId,
                    'material_id' => $cartProduct->materialId,
                    'SaleDetails_TotalQuantity' => $cartProduct->quantity,
                    'Purchase_Rate' => $cartProduct->purchaseRate,
                    'SaleDetails_Rate' => $cartProduct->salesRate,
                    'SaleDetails_TotalAmount' => $cartProduct->total,
                    'Status' => 'a',
                    'AddBy' => $this->session->userdata("FullName"),
                    'AddTime' => date('Y-m-d H:i:s'),
                    'SaleDetails_BranchId' => $this->session->userdata("BRANCHid")
                );
    
                $this->db->insert('tbl_material_sale_details', $saleDetails);
            }
    
            $res = ['success'=>true, 'message'=>'Sales Updated', 'salesId'=>$salesId];

        } catch (Exception $ex){
            $res = ['success'=>false, 'message'=>$ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function editSale($salesId) {
        $data['title'] = "Sales update";
        $sales = $this->db->query("select * from tbl_material_sale where SaleMaster_SlNo = ?", $salesId)->row();
        $data['salesId'] = $salesId;
        $data['invoice'] = $sales->SaleMaster_InvoiceNo;
        $data['content'] = $this->load->view('Administrator/MaterialSale/material_sale', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getSales() {
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata("BRANCHid");

        $clauses = "";
        if(isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != ''){
            $clauses .= " and sm.SaleMaster_SaleDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if(isset($data->userFullName) && $data->userFullName != ''){
            $clauses .= " and sm.AddBy = '$data->userFullName'";
        }

        if(isset($data->customerId) && $data->customerId != ''){
            $clauses .= " and sm.SalseCustomer_IDNo = '$data->customerId'";
        }

        if(isset($data->employeeId) && $data->employeeId != ''){
            $clauses .= " and sm.employee_id = '$data->employeeId'";
        }

        if(isset($data->salesId) && $data->salesId != 0 && $data->salesId != ''){
            $clauses .= " and SaleMaster_SlNo = '$data->salesId'";
            $saleDetails = $this->db->query("
                select 
                    sd.*,
                    m.name,
                    pc.ProductCategory_Name,
                    u.Unit_Name
                from tbl_material_sale_details sd
                join tbl_materials m on m.material_id = sd.material_id
                join tbl_productcategory pc on pc.ProductCategory_SlNo = m.category_id
                join tbl_unit u on u.Unit_SlNo = m.unit_id
                where sd.SaleMaster_IDNo = ?
            ", $data->salesId)->result();
    
            $res['saleDetails'] = $saleDetails;
        }
        $sales = $this->db->query("
            select 
                sm.*,
                c.Customer_Code,
                c.Customer_Name,
                c.Customer_Mobile,
                c.Customer_Address,
                c.Customer_Type,
                e.Employee_Name,
                br.Brunch_name
            from tbl_material_sale sm
            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            left join tbl_employee e on e.Employee_SlNo = sm.employee_id
            left join tbl_brunch br on br.brunch_id = sm.SaleMaster_branchid
            where sm.SaleMaster_branchid = '$branchId'
            and sm.Status = 'a'
            $clauses
            order by sm.SaleMaster_SlNo desc
        ")->result();
        
        $res['sales'] = $sales;

        echo json_encode($res);
    }

    public function invoice($saleId) {
        $data['title'] = "Sales Invoice";
        $data['salesId'] = $saleId;
        $data['content'] = $this->load->view('Administrator/MaterialSale/invoice', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function salesRecord() {
        $access = $this->mt->userAccess();
        if(!$access){
            redirect(base_url());
        }
        $data['title'] = "Material Sales Record";  
        $data['content'] = $this->load->view('Administrator/MaterialSale/material_sales_record', $data, TRUE);
        $this->load->view('Administrator/index', $data); 
    }

    public function getSalesRecord(){
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata("BRANCHid");
        $clauses = "";
        if(isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != ''){
            $clauses .= " and sm.SaleMaster_SaleDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if(isset($data->userFullName) && $data->userFullName != ''){
            $clauses .= " and sm.AddBy = '$data->userFullName'";
        }

        if(isset($data->customerId) && $data->customerId != ''){
            $clauses .= " and sm.SalseCustomer_IDNo = '$data->customerId'";
        }

        if(isset($data->employeeId) && $data->employeeId != ''){
            $clauses .= " and sm.employee_id = '$data->employeeId'";
        }

        $sales = $this->db->query("
            select 
                sm.*,
                c.Customer_Code,
                c.Customer_Name,
                c.Customer_Mobile,
                c.Customer_Address,
                e.Employee_Name,
                br.Brunch_name,
                (
                    select ifnull(count(*), 0) from tbl_saledetails sd 
                    where sd.SaleMaster_IDNo = 1
                    and sd.Status != 'd'
                ) as total_products
            from tbl_material_sale sm
            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            left join tbl_employee e on e.Employee_SlNo = sm.employee_id
            left join tbl_brunch br on br.brunch_id = sm.SaleMaster_branchid
            where sm.SaleMaster_branchid = '$branchId'
            and sm.Status = 'a'
            $clauses
            order by sm.SaleMaster_SlNo desc
        ")->result();

        foreach($sales as $sale){
            $sale->saleDetails = $this->db->query("
                select 
                    sd.*,
                    m.name,
                    pc.ProductCategory_Name
                from tbl_material_sale_details sd
                join tbl_materials m on m.material_id = sd.material_id
                join tbl_productcategory pc on pc.ProductCategory_SlNo = m.category_id
                where sd.SaleMaster_IDNo = ?
                and sd.Status != 'd'
            ", $sale->SaleMaster_SlNo)->result();
        }

        echo json_encode($sales);
    }

    public function getSaleDetails(){
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if(isset($data->customerId) && $data->customerId != ''){
            $clauses .= " and c.Customer_SlNo = '$data->customerId'";
        }

        if(isset($data->materialId) && $data->materialId != ''){
            $clauses .= " and m.material_id = '$data->materialId'";
        }

        if(isset($data->categoryId) && $data->categoryId != ''){
            $clauses .= " and pc.ProductCategory_SlNo = '$data->categoryId'";
        }

        if(isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != ''){
            $clauses .= " and sm.SaleMaster_SaleDate between '$data->dateFrom' and '$data->dateTo'";
        }

        $saleDetails = $this->db->query("
            select 
                sd.*,
                m.name,
                pc.ProductCategory_Name,
                sm.SaleMaster_InvoiceNo,
                sm.SaleMaster_SaleDate,
                c.Customer_Code,
                c.Customer_Name
            from tbl_material_sale_details sd
            join tbl_materials m on m.material_id = sd.material_id
            join tbl_productcategory pc on pc.ProductCategory_SlNo = m.category_id
            join tbl_salesmaster sm on sm.SaleMaster_SlNo = sd.SaleMaster_IDNo
            join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            where sd.Status != 'd'
            and sm.SaleMaster_branchid = ?
            $clauses
        ", $this->branch)->result();

        echo json_encode($saleDetails);
    }

    public function  deleteSale(){
        $res = ['success'=>false, 'message'=>''];
        try{
            $data = json_decode($this->input->raw_input_stream);
            $saleId = $data->saleId;

            $sale = $this->db->select('*')->where('SaleMaster_SlNo', $saleId)->get('tbl_material_sale')->row();
            if($sale->Status != 'a'){
                $res = ['success'=>false, 'message'=>'Sale not found'];
                echo json_encode($res);
                exit;
            }

            /*Delete Sale Details*/
            $this->db->set('Status', 'd')->where('SaleMaster_IDNo', $saleId)->update('tbl_material_sale_details');

            /*Delete Sale Master Data*/
            $this->db->set('Status', 'd')->where('SaleMaster_SlNo', $saleId)->update('tbl_material_sale');
            $res = ['success'=>true, 'message'=>'Sale deleted'];
        } catch (Exception $ex){
            $res = ['success'=>false, 'message'=>$ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function salesInvoices()  {
        $access = $this->mt->userAccess();
        if(!$access){
            redirect(base_url());
        }
        $data['title'] = "Material Sales Invoice"; 
		$data['content'] = $this->load->view('Administrator/MaterialSale/material_sales_invoices', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
}
