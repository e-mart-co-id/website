<?php
$orders = $data[Tags::RESULT];
$pagination = $data['pagination'];
$this->load->model("user/user_model", "mUserModel");
?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
                <?php $this->load->view("backend/include/messages"); ?>
            </div>

        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header" style="width : 100%;">
                        <div class=" row ">
                            <div class="pull-left col-md-8 box-title">
                                <b><?= Translate::sprint("Orders Management") ?></b>
                                <?php
                                $CI =& get_instance();
                                $url = $CI->config->site_url($CI->uri->uri_string());
                                $query_uri = $_SERVER['QUERY_STRING'];
                                if ($query_uri != ""): ?>
                                    <a href="<?= current_url() ?>"><span
                                                class="badge bg-red"><i
                                                    class="mdi mdi-close"></i>&nbsp;&nbsp;<?= _lang("Clear filter") ?></span></a>
                                <?php endif; ?>
                            </div>
                            <div class="pull-right col-md-4">
                                <div class="row">
                                    <div class="pull-right col-sm-4">

                                        <a href="#" data-toggle="modal" data-toggle="tooltip"
                                           data-target="#modal-default-filter">
                                            <button type="button"
                                                    title="<?= Translate::sprint("Filter") ?>"
                                                    class="btn btn-primary btn-sm pull-right">
                                                <span class="glyphicon glyphicon-filter"></span>
                                            </button>
                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table id="" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th><?= Translate::sprint("Order ID") ?></th>
                                <th><?= Translate::sprint("Client") ?></th>
                                <th><?= Translate::sprint("Business Owner") ?></th>
                                <th><?= Translate::sprint("Status") ?></th>

                                <th><?= Translate::sprint("Payment") ?></th>
                                <th><?= Translate::sprint("Subtotal") ?></th>

                                <?php if (ConfigManager::getValue('ORDER_COMMISSION_ENABLED') == TRUE): ?>
                                    <th><?= Translate::sprint("Commission") ?></th>
                                <?php endif; ?>

                                <?php if (ModulesChecker::isEnabled('delivery')): ?>
                                    <th><?= Translate::sprint("Delivery fees") ?></th>
                                <?php endif; ?>


                                <th><?= Translate::sprint("Date") ?></th>
                                <th>

                                    <?php

                                    $export_plugin = $this->exim_tool->plugin_export(array(
                                        'module' => 'orders'
                                    ));

                                    echo $export_plugin['html'];
                                    TemplateManager::addScript($export_plugin['script']);

                                    ?>

                                </th>
                            </tr>
                            </thead>
                            <tbody id="list">

                            <?php

                            $total_commission = 0;
                            $total_amount = 0;

                            ?>
                            <?php if (!empty($orders)) : ?>

                                <?php foreach ($orders as $key => $order): ?>

                                    <?php /* echo "<pre>"; print_r($order);die(); */
                                    $token = $this->mUserBrowser->setToken(Text::encrypt($order['id'])); ?>

                                    <tr class="store_<?= $token ?>" role="row" class="odd">

                                        <td>
                                            <span style="font-size: 14px">  <b> <?= "#" . str_pad($order['id'], 6, 0, STR_PAD_LEFT) ?> </b> </span>
                                        </td>
                                        <td>
                                            <u><?= ucfirst($this->mUserModel->getFieldById("name", $order['user_id'])) ?></u>

                                            <?php if (GroupAccess::isGranted("user", MANAGE_USERS)): ?>
                                                &nbsp;&nbsp;<a target="_blank"
                                                               href="<?= admin_url("user/edit?id=" . $order['user_id']) ?>"><i
                                                            class="mdi mdi-open-in-new"></i></a>
                                            <?php endif; ?>

                                        </td>

                                        <td>
                                            <?php $store = $this->mOrder->getStoreFromCart($order['id']);?>
                                            <a target="_blank"
                                               href="<?= admin_url("user/edit?id=" . $store['user_id']) ?>"><?= ucfirst($this->mUserModel->getUserNameById($store['user_id'])) ?>
                                                <i class="mdi mdi-open-in-new"></i></a></a>
                                        </td>

                                        <td>

                                            <?php

                                            if (isset($order['status']) && $order['status'] != "") {
                                                //if (preg_match("#;#", $order['status'])) {
                                                $statusParser = explode(";", $order['status']);
                                                echo "<span class=badge style='background:" . $statusParser[1] . "'>" . $statusParser[0] . "</span>";
                                                // }
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php

                                            $pcode = $order['payment_status'];
                                            $payments = Order_payment::PAYMENT_STATUS;
                                            if (isset($payments[$pcode])) {
                                                echo "<span class='badge' style='background-color: " . $payments[$pcode]['color'] . "'>" . ucfirst(_lang($payments[$pcode]['label'])) . "</span>";
                                            }else if($pcode == "cod_paid"){
                                                echo "<span class='badge bg-green'>"._lang("Paid on delivery")."</span>";
                                            }

                                            ?>
                                        </td>


                                        <td>

                                            <?php

                                            $cart = json_decode($order['cart'], JSON_OBJECT_AS_ARRAY);
                                            $sub_total = 0;
                                            $currency = DEFAULT_CURRENCY;

                                            $commission = 0;

                                            foreach ($cart as $item) {

                                                $callback = BookmarkLinkedModule::find($item['module'], 'getData');

                                                if ($callback != NULL) {

                                                    $params = array(
                                                        'id' => $item['module_id']
                                                    );

                                                    $result = call_user_func($callback, $params);

                                                    if (!empty($result['currency']) && is_array($result['currency'])) {
                                                        $currency = $result['currency']['code'];
                                                    } else if (isset($result['currency'] ) && is_string($result['currency'])) {
                                                        $currency = $result['currency'];
                                                    }else{
                                                        $currency = DEFAULT_CURRENCY;
                                                    }

                                                }

                                                $sub_total = $sub_total + ($item['amount'] * $item['qty']);
                                                $commission = $commission + ($result['commission'] * $item['qty']);

                                                $total_commission = $total_commission + $commission;
                                                $total_amount = $total_amount + $sub_total;
                                            }

                                            if (defined('DEFAULT_TAX') and DEFAULT_TAX > 0) {

                                                $percent = 0;
                                                $tax = $this->mTaxModel->getTax(DEFAULT_TAX);
                                                if ($tax != NULL) {
                                                    $percent = $tax['value'];

                                                }

                                                // $sub_total = (($percent / 100) * $sub_total) + $sub_total;

                                            }

                                            echo "<b>" . Currency::parseCurrencyFormat($sub_total, $currency) . "</b>";


                                            ?>

                                        </td>


                                        <?php if (ConfigManager::getValue('ORDER_COMMISSION_ENABLED') == TRUE): ?>
                                            <td><?= Currency::parseCurrencyFormat($commission, $currency); ?></td>
                                        <?php endif; ?>


                                        <?php if (ModulesChecker::isEnabled('delivery') ): ?>
                                            <?php


                                            ?>
                                            <td>
                                                <?php

                                                if(!isset($delivery_fees)){
                                                    $delivery_fees = 0;
                                                }

                                                if( $order['delivery_status'] == 3){
                                                    $delivery_fees = $delivery_fees + $order['delivery_commission'];
                                                    echo Currency::parseCurrencyFormat($order['delivery_commission'], $currency);
                                                }else{
                                                    echo "--";
                                                }

                                                ?>
                                            </td>
                                        <?php endif; ?>

                                        <td>
                                            <span style="font-size: 14px">  <?= MyDateUtils::convert($order['updated_at'], "UTC", TimeZoneManager::getTimeZone(), "d M, Y h:i:s A") ?>  </span>
                                        </td>

                                        <td align="right">

                                            <?php if (ModulesChecker::isEnabled("order_payment") && $order['user_id'] == SessionManager::getData("id_user")): ?>

                                                <?php
                                                $invoice = $this->mOrderModel->getInvoiceID($order['id']);
                                                ?>

                                                <?php if ($invoice != NULL && ($order['payment_status'] == "" or $order['payment_status'] == "unpaid")): ?>
                                                    <a class="btn btn-sm btn-primary"
                                                       href="<?= site_url("payment/make_payment?id=" . $invoice['id']) ?>">
                                                        <?= _lang("Pay") ?>
                                                    </a>
                                                    &nbsp;
                                                <?php endif; ?>

                                            <?php endif; ?>

                                            <a class="btn btn-default" data-toggle="tooltip"
                                               href="<?= admin_url("nsorder/view?id=" . $order['id']) ?>"
                                               title="<?= Translate::sprint("Edit") ?>">
                                                <i class="fa fa-pencil"></i>
                                            </a>

                                            &nbsp;
                                            <a class="btn btn-default" target="_blank"
                                               href="<?= admin_url("nsorder/print_order?id=" . $order['id']) ?>"><i
                                                        class="mdi mdi-printer"></i></a>

                                        </td>

                                    </tr>


                                    <?php


                                    $store = $this->mOrder->getStoreFromCart($order['id']);


                                    $pcode = $order['payment_status'];
                                    $payments = Order_payment::PAYMENT_STATUS;

                                    $statusParser = explode(";", $order['status']);

                                    $array  = array(
                                        'order_id' => "#" . str_pad($order['id'], 6, 0, STR_PAD_LEFT),
                                        'client' => ucfirst($this->mUserModel->getFieldById("name", $order['user_id'])),
                                        'client_phone' => ucfirst($this->mUserModel->getFieldById("telephone", $order['user_id'])),
                                        'business_owner' => ucfirst($this->mUserModel->getUserNameById($store['user_id'])),
                                        'order_status' => $statusParser[0],
                                        'payment' => isset($payments[$pcode]) ? ucfirst(_lang($payments[$pcode]['label'])) : "",
                                        'amount' => $total_amount,
                                        'commission' => $total_commission,
                                        'date' => $order['updated_at'],
                                    );

                                    if (ModulesChecker::isEnabled('delivery')
                                        && $order['delivery_status'] == 3){
                                        $array['delivery_fees'] = $order['delivery_commission'];
                                    }

                                    echo Exim_toolManager::setupRows($array);
                                    ?>

                                <?php endforeach; ?>

                            <?php else: ?>
                                <tr>
                                    <td colspan="7" align="center">
                                        <div style="text-align: center"><?= Translate::sprint("No data found", "") ?></div>
                                    </td>
                                </tr>

                            <?php endif; ?>


                            </tbody>
                        </table>

                        <?php if (GroupAccess::isGranted('nsorder', MANAGE_ORDERS)): ?>
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <td width="50%"></td>
                                    <td width="25%" align="right"><b><?= _lang("Total") ?></b></td>
                                    <td width="25%"> <?= Currency::parseCurrencyFormat($total_amount, DEFAULT_CURRENCY) ?></td>
                                </tr>

                                <?php if ($total_commission > 0): ?>
                                    <tr>
                                        <td width="50%"></td>
                                        <td width="25%" align="right"><b><?= _lang("Commission") ?></b></td>
                                        <td width="25%"><?= Currency::parseCurrencyFormat($total_commission, DEFAULT_CURRENCY) ?></td>
                                    </tr>
                                    <tr>
                                        <td width="50%"></td>
                                        <td width="25%" align="right"><b><?= _lang("Total without commission") ?></b>
                                        </td>
                                        <td width="25%"> <?= Currency::parseCurrencyFormat($total_amount - $total_commission, DEFAULT_CURRENCY) ?></td>
                                    </tr>
                                <?php endif; ?>



                                <?php if (ModulesChecker::isEnabled('delivery') && isset($delivery_fees)): ?>
                                    <tr>
                                        <td width="50%"></td>
                                        <td width="25%" align="right"><b><?= _lang("Delivery fees") ?></b>
                                        </td>
                                        <td width="25%"> <?= Currency::parseCurrencyFormat($delivery_fees, DEFAULT_CURRENCY) ?></td>
                                    </tr>
                                <?php endif; ?>

                            </table>
                        <?php endif; ?>


                        <div class="row">
                            <div class="col-sm-12">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

                                    <?php
                                    echo $pagination->links(array(
                                        "status" => intval($this->input->get("status")),
                                        "search" => $this->input->get("search"),
                                        "owner_id" => intval($this->input->get("owner_id")),
                                    ), $pagination_url);

                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->


            <!-- /.box -->
        </div>
        <!-- /.col -->
        <!-- /.row -->
</div>


<!--  Model popup : begin-->
<div class="modal fade" id="modal-default-filter">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Translate::sprint("Filter order") ?> </h4>
            </div>
            <div class="modal-body">

                <div class="row">

                    <div class="col-md-6">

                        <?php if (GroupAccess::isGranted('nsorder', MANAGE_ORDER_CONFIG_ADMIN)): ?>

                            <div class="form-group">
                                <label><?= _lang("Business Owner") ?></label>
                                <select id="select_owner" name="select_owner" class="form-control select2">
                                    <option selected="" value="0">-- <?= Translate::sprint("Select owner") ?></option>
                                </select>
                            </div>

                        <?php endif; ?>



                        <?php $status = $this->mOrder->getList(); ?>


                        <div class="form-group">
                            <label><?= _lang("Select date") ?></label>
                            <input type="text" class="form-control" name="datefilter" placeholder="Range date"
                                   value=""/>
                        </div>


                        <div class="form-group">
                            <label><?= _lang("Order Status") ?></label>
                            <select id="select_order_status" class="form-control select2">
                                <option selected="" value="0">-- <?= Translate::sprint("Select") ?></option>
                                <?php foreach ($status as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= $s['label'] ?></option>
                                <?php endforeach; ?>

                            </select>
                        </div>


                        <div class="form-group">
                            <label><?= _lang("Limit") ?></label>
                            <input type="number" class="form-control" name="limit" id="limit"
                                   value="<?= NO_OF_ITEMS_PER_PAGE ?>"/>
                        </div>

                        <div class="form-group">
                            <label><?= _lang("Payment Status") ?></label>
                            <select id="select_payment_status" class="form-control select2">
                                <option value="0">-- <?= Translate::sprint("Select") ?></option>
                                <?php foreach (Order_payment::PAYMENT_STATUS as $k => $ps): ?>
                                    <option value="<?= $k ?>"><?= _lang($ps['label']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>


                    </div>

                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left"
                        data-dismiss="modal"><?= Translate::sprint("Cancel") ?></button>
                <button type="button" id="_filter"
                        data=""
                        class="btn btn-flat btn-primary"><?= Translate::sprint("Apply") ?></button>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!--  Model popup : end-->


<?php
$script = $this->load->view('nsorder/backend/scripts/orders-script', NULL, TRUE);
TemplateManager::addScript($script);
?>
