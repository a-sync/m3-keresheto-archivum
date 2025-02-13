<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (count($items) === 0)
{
	echo '<div class="mdc-typography--body1 list__no_items">Nincs találat...</div>';
}
else
{
	echo '<div class="mdc-typography--caption list__total">'.$total.' találat</div>';

	echo $links;

	$keys = array_keys($items[0]);
?>
<div class="mdc-data-table mdc-elevation--z2 list__table">
	<table class="mdc-data-table__table">
		<thead>
			<tr class="mdc-data-table__header-row">
			  <?php foreach($keys as $k): ?>
				<th class="mdc-data-table__header-cell" role="columnheader" scope="col"><?php echo html_escape($k); ?></th>
			  <?php endforeach; ?>
			</tr>
		</thead>
		<tbody class="mdc-data-table__content">
			<?php foreach ($items as $i):
				$duration = explode(':', $i['duration']);
				$h = intval($duration[0]) ? intval($duration[0]).'ó ' : '';
				$m = intval(isset($duration[1])) ? intval($duration[1]).'p' : '';
			?>
			  <tr class="mdc-data-table__row">
				  <?php foreach ($i as $ii => $val): ?>
					<td class="mdc-data-table__cell">
					  <?php 
						$val = html_escape($val);
						if ($ii === 'description' || $ii === 'short_description') {
						  $val = implode(",\n", explode(',', $val));
						  $val = implode(";\n", explode(';', $val));
						}
						echo nl2br($val); 
					  ?>
					</td>
				  <?php endforeach; ?>
			  </tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php
	echo $links;
}
