<?php
defined('BASEPATH') OR exit('No direct script access allowed');

echo form_open(site_url(''), array(
    'id' => 'search-form',
    'method' => 'get'
));

echo form_input(array(
    'id'=> 'search-field', 
    'name'=> 'kereses', 
    'value' => $search
));

echo form_close();
