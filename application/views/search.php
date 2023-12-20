<?php
defined('BASEPATH') OR exit('No direct script access allowed');

echo form_open(site_url(''), array(
	'id' => 'search-form',
	'method' => 'get'
));
?>

<div class="mdc-text-field mdc-text-field--outlined mdc-text-field--with-leading-icon">
	<i class="mdi mdi-magnify mdc-text-field__icon"></i>

	<?php 
	echo form_input(array(
		'id'=> 'search-field', 
		'name'=> 'kereses', 
		'value' => $search,
		'class' => 'mdc-text-field__input',
		'tabindex' => 0
	));
	?>

	<div class="mdc-notched-outline mdc-notched-outline--upgraded">
		<div class="mdc-notched-outline__leading"></div>
		<div class="mdc-notched-outline__notch"></div>
		<div class="mdc-notched-outline__trailing"></div>
	</div>
</div>

<button id="search-button" type="submit" class="mdc-button mdc-button--raised"><span class="mdc-button__ripple"></span>Keresés</button>

<?php 
echo form_close();
?>

<script type="text/javascript">
window.addEventListener('load', () => {
	const searchField = document.getElementById('search-field');
	searchField.focus();
	const v = searchField.value;
	searchField.value = '';
	searchField.value = v;

	const form = document.getElementById('search-form');
	const searchButton = document.getElementById('search-button');
	form.addEventListener('submit', async ev => {
		ev.preventDefault();
		searchButton.disabled = true;
		searchField.readonly = true;
		form.classList.add('searching');

		const icon = form.querySelector('.mdc-text-field__icon');
		icon.classList.add('mdi-dots-circle');
		icon.classList.remove('mdi-magnify');

		const currVal = searchField.value.trim();
		if (currVal.length > 3 && currVal.toUpperCase().startsWith('M3-')) {
			try {
				const res = await fetch('<?php echo site_url('cron/add?id='); ?>' + encodeURIComponent(currVal));
				const resText = await res.text();
				console.log('cron/add', resText);
			} catch (err) {
				console.error(err);
			}
		}
		form.submit();
	});
});
</script>
