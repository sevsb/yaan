<?php
include_once(dirname(__FILE__) . "/../app/config.php");

class customers_controller {
    public function preaction($action) {
        login::assert();
    }
    public function index_action() {
        $tpl = new tpl("admin/header", "admin/footer");
        $customers = customers::get_all_customers();
        $tpl->set('customers', $customers);
        $tpl->display("customers/index");
    }
    
    public function detail_show_action() {
        $tpl = new tpl("admin/header", "admin/footer");
        $id = get_request('id');
        $customer = customers::get_customer_detail($id);
        $tpl->set('id', $id);
        $tpl->set('customer', $customer);
        $tpl->display("customers/detail");
    }

    public function modify_action() {
        $tpl = new tpl("admin/header", "admin/footer");
        $id = get_request('id');
        $customer = customers::get_customer_detail($id);
        $tpl->set('id', $id);
        $tpl->set('customer', $customer);
        $tpl->display("customers/modify");
    }
    
    public function new_action() {
        $tpl = new tpl("admin/header", "admin/footer");
        $tpl->display("customers/new");
    }

    public function add_ajax() {
        $name = get_request('name');
        $tel = get_request('tel');
        $ret = customers::add($name, $tel);
        return $ret ? 'success' : 'fail';
    }
    
    public function modify_ajax() {
        $id = get_request('id');
        $name = get_request('name');
        $tel = get_request('tel');
        $ret = customers::modify($id, $name, $tel);
        return $ret ? 'success' : 'fail';
    }
    
    public function delete_ajax() {
        logging::d("customers_controller", "delete_ajax()");
        $id = get_request('id');
        $ret = customers::del($id);
        return $ret ? 'success' : 'fail';
    }

}













