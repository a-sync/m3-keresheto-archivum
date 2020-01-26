<?php
defined('BASEPATH') OR exit('No direct script access allowed');

echo form_open(site_url(''), array(
    'id' => 'search-form',
    'method' => 'get'
));
?>

<div id="search-field-wrapper" class="mdc-text-field mdc-text-field--outlined mdc-text-field--with-leading-icon">
    <i class="material-icons mdc-text-field__icon" tabindex="0" role="button">search</i>

    <?php 
    echo form_input(array(
        'id'=> 'search-field', 
        'name'=> 'kereses', 
        'value' => $search,
        'class' => 'mdc-text-field__input'
    ));
    ?>

    <div class="mdc-notched-outline mdc-notched-outline--upgraded">
        <div class="mdc-notched-outline__leading"></div>
        <div class="mdc-notched-outline__notch"></div>
        <div class="mdc-notched-outline__trailing"></div>
    </div>
</div>

<button class="mdc-button mdc-button--raised"><span class="mdc-button__ripple"></span>Keresés</button>

<?php 

echo form_close();
