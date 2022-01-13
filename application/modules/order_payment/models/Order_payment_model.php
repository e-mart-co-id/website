<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order_payment_model extends CI_Model {



    public function updateOrderPaymentStatus($inv_object,$status){

        $order_id = $inv_object->module_id;

        $this->db->where("id",$order_id);
        $this->db->update("order_list",array(
            'payment_status' => $status,
            'amount' => $inv_object->amount,
        ));

    }

    public function getInvoice($module_id){

        $this->db->where('module',"order_payment");
        $this->db->where('module_id',$module_id);

        $invoice = $this->db->get('invoice',1);
        $invoice = $invoice->result();

        if(isset($invoice[0]))
            return $invoice[0];

        return NULL;
    }

    public function convert_order_to_invoice($user_id,$order_id){

        $this->db->where('user_id',$user_id);
        $this->db->where('module',"order_payment");
        $this->db->where('module_id',$order_id);
        $invoice = $this->db->get('invoice',1);
        $invoice = $invoice->result();

        if(!isset($invoice[0])){

            $this->db->where('user_id',$user_id);
            $this->db->where('id',$order_id);
            $order = $this->db->get('order_list',1);
            $order = $order->result_array();

            if(isset($order[0])){

                $order = $order[0];

                $extras = array();
                $items = array();
                $amount = 0;

                $cart = json_decode($order['cart'],JSON_OBJECT_AS_ARRAY);


                foreach ($cart as $item){

                    $callback = BookmarkLinkedModule::find($item['module'],'getData');

                    if($callback != NULL){

                        $params = array(
                            'id' => $item['module_id']
                        );

                        $result = call_user_func($callback,$params);

                        $items[] = array(
                            'item_id'=> $item['module_id'],
                            'item_name'=> $result['label'],
                            'price'=> $item['amount'],
                            'qty' => $item['qty'],
                            'unit' => 'item',
                            'price_per_unit' => $item['amount'],
                        );

                        $amount = $amount+($item['amount']*$item['qty']);

                    }

                }


                if($amount==0)
                    return array(Tags::SUCCESS=>1,Tags::RESULT=>-1);

                $this->db->where('user_id',$user_id);
                $no = $this->db->count_all_results('invoice');
                $no++;

                $data = array(
                    "method"    => "",
                    "amount"    => $amount,
                    "no"        => $no,
                    "module"    => "order_payment",
                    "module_id"    => $order['id'],
                    "tax_id"       => 0,
                    "items"       => json_encode($items,JSON_FORCE_OBJECT),
                    "currency"  => PAYMENT_CURRENCY,
                    "status"    => 0,
                    "user_id"   => $user_id,
                    "transaction_id"   => "",
                    "created_at"        => date("Y-m-d H:i:s",time())
                );



                $this->db->insert('invoice',$data);
                $id = $this->db->insert_id();

                $this->db->where('id',$id);
                $invoice = $this->db->get('invoice',1);
                $invoice = $invoice->result();
                $invoice = $invoice[0];

                ActionsManager::add_action('order_payment','payment_invoice_generated',$invoice);

                return array(Tags::SUCCESS=>1,Tags::RESULT=>$id);
            }


        }else{
            return array(Tags::SUCCESS=>1,Tags::RESULT=>$invoice[0]->id);
        }


        return array(Tags::SUCCESS=>0);

    }




}

