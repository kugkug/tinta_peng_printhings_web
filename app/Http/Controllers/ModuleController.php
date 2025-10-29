<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ModuleController extends Controller
{
    private $data = [];

    public function __construct() {
        $this->data = [
            'theme' => 'light',
            'root_url' => URL::current(),
        ];
    }

    public function index() {
        $this->data['title'] = 'Home';
        $this->data['description'] = 'Home';
        $this->data['right_panel'] = '';
        return view('home', $this->data);
    }

    public function inventoryList() {
        $this->data['title'] = 'Inventory';
        $this->data['description'] = 'Inventory';
        $this->data['right_panel'] = "<a href='".route('inventory.add')."' class='btn btn-success btn-md btn-flat btn-block ' ><i class='fa fa-plus'></i> Add New</a>";
        return view('inventory.list', $this->data);
    }

    public function inventoryAdd() {
        $this->data['title'] = 'Add Item';
        $this->data['description'] = 'Add Item';
        $this->data['right_panel'] = '';
        $this->data['item_id'] = null;
        return view('inventory.add', $this->data);
    }

    public function inventoryEdit($id) {
        $this->data['title'] = 'Edit Item';
        $this->data['description'] = 'Edit Item';
        $this->data['right_panel'] = '';
        $this->data['item_id'] = $id;
        return view('inventory.add', $this->data);
    }

    public function settingsIndex() {
        $this->data['title'] = 'Settings';
        $this->data['description'] = 'Settings';
        $this->data['right_panel'] = '';
        return view('settings.index', $this->data);
    }

    public function productsList() {
        $this->data['title'] = 'Products';
        $this->data['description'] = 'Products Management';
        $this->data['right_panel'] = "<a href='".route('products.add')."' class='btn btn-success btn-md btn-flat btn-block ' ><i class='fa fa-plus'></i> Add New Product</a>";
        return view('products.list', $this->data);
    }

    public function productsAdd() {
        $this->data['title'] = 'Add Product';
        $this->data['description'] = 'Add Product';
        $this->data['right_panel'] = '';
        return view('products.add', $this->data);
    }

    public function productsEdit($id) {
        $this->data['title'] = 'Edit Product';
        $this->data['description'] = 'Edit Product';
        $this->data['right_panel'] = '';
        $this->data['id'] = $id;
        return view('products.edit', $this->data);
    }
}