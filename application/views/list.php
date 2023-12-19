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
?>
<div class="mdc-data-table mdc-elevation--z2 list__table">
	<table class="mdc-data-table__table">
		<thead>
			<tr class="mdc-data-table__header-row">
				<th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">🎞ID</th>
				<th class="mdc-data-table__header-cell" role="columnheader" scope="col">Cím / Rövid leírás</th>
				<th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">Hossz</th>
			</tr>
		</thead>
		<tbody class="mdc-data-table__content">
			<?php foreach ($items as $i):
				$duration = explode(':', $i['duration']);
				$h = intval($duration[0]) ? intval($duration[0]).'ó ' : '';
				$m = intval($duration[1]) ? intval($duration[1]).'p' : '';
			?>
			<tr class="mdc-data-table__row">
				<td class="mdc-data-table__cell mdc-data-table__cell--numeric">
					<div class="m3id"><?php echo html_escape($i['program_id']); ?></div>
					<div class="m3player">
						<video
							id="vid-<?php echo html_escape(strtolower($i['program_id'])); ?>"
							data-programid="<?php echo html_escape($i['program_id']); ?>"
							data-hassubtitle="<?php echo html_escape($i['hasSubtitle']); ?>"
							class="video-js"
							preload="none"
							poster="https://nemzetiarchivum.hu/images/m3/<?php echo html_escape($i['program_id']); ?>"
						></video>
					</div>
				</td>
				<td class="mdc-data-table__cell cell__title">
					<span class="mdc-typography--headline6 cell__title--title"><?php echo html_escape($i['title']); ?></span>
					<?php if ($i['subtitle']): ?>
						&#8212;
						<span class="mdc-typography--subtitle1 cell__title--subtitle"><?php echo html_escape($i['subtitle']) ?: ''; ?></span>
					<?php endif; ?>
					<?php if ($i['isSeries']): ?>
						<span class="mdc-typography--subtitle2 cell__title--ep">(<?php echo html_escape($i['episode']); ?>. / <?php echo html_escape($i['episodes']); ?>)</span>
					<?php endif; ?>
					<br>
					<span class="mdc-typography--caption"><?php echo html_escape($i['short_description']); ?></span>
				</td>
				<td class="mdc-data-table__cell mdc-data-table__cell--numeric"><?php echo html_escape($h.$m); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php
	echo $links;
}

/* TODO: use layout grid & show all available data for each item (image, extended_info, year, genre, contributors, pg rating, etc...)
  <div class="adopt-a-pup-puppers adopt-a-pup-body mdc-layout-grid">
	<div class="mdc-layout-grid__inner">
	  <div class="mdc-layout-grid__cell">
		<div class="mdc-card mdc-card--outlined adopt-a-pup-card">
		  <div class="adopt-a-pup-image mdc-card__media mdc-card__media--square" style="background-image: url('./media/1.jpg')"></div>
		  <div class="adopt-a-pup-card__text-label">Monty</div>
		  <div class="adopt-a-pup-card__secondary mdc-typography--body2">Monty enjoys chicken treats and cuddling while watching Seinfeld.</div>
		  <div class="mdc-card__actions">
			<div class="mdc-card__action-buttons">
			  <button class="mdc-button mdc-card__action mdc-card__action--button adopt-form__button">
				<i class="material-icons mdc-button__icon adopt-form__button-icon">pets</i> <span class="adopt-form__button-text">Adopt</span>
			  </button>
			</div>
		  </div>
		</div>
	  </div>
	  <div class="mdc-layout-grid__cell">
		<div class="mdc-card mdc-card--outlined adopt-a-pup-card">
		  <div class="adopt-a-pup-image mdc-card__media mdc-card__media--square" style="background-image: url('./media/2.jpg')"></div>
		  <div class="adopt-a-pup-card__text-label">Jubilee</div>
		  <div class="adopt-a-pup-card__secondary mdc-typography--body2">Jubilee enjoys thoughtful discussions by the campfire.</div>
		  <div class="mdc-card__actions">
			<div class="mdc-card__action-buttons">
			  <button class="mdc-button mdc-card__action mdc-card__action--button adopt-form__button">
				<i class="material-icons mdc-button__icon adopt-form__button-icon">pets</i> <span class="adopt-form__button-text">Adopt</span>
			  </button>
			</div>
		  </div>
		</div>
	  </div>
	  <div class="mdc-layout-grid__cell">
		<div class="mdc-card mdc-card--outlined adopt-a-pup-card">
		  <div class="adopt-a-pup-image mdc-card__media mdc-card__media--square" style="background-image: url('./media/3.jpg')"></div>
		  <div class="adopt-a-pup-card__text-label">Beezy</div>
		  <div class="adopt-a-pup-card__secondary mdc-typography--body2">Beezy's favorite past-time is helping you choose your brand color.</div>
		  <div class="mdc-card__actions">
			<div class="mdc-card__action-buttons">
			  <button class="mdc-button mdc-card__action mdc-card__action--button adopt-form__button">
				<i class="material-icons mdc-button__icon adopt-form__button-icon">pets</i> <span class="adopt-form__button-text">Adopt</span>
			  </button>
			</div>
		  </div>
		</div>
	  </div>

	  <div class="mdc-layout-grid__cell">
		<div class="mdc-card mdc-card--outlined adopt-a-pup-card">
		  <div class="adopt-a-pup-image mdc-card__media mdc-card__media--square" style="background-image: url('./media/4.jpg')"></div>
		  <div class="adopt-a-pup-card__text-label">Mochi</div>
		  <div class="adopt-a-pup-card__secondary mdc-typography--body2">Mochi is the perfect "rubbery ducky" debugging pup, always listening.</div>
		  <div class="mdc-card__actions">
			<div class="mdc-card__action-buttons">
			  <button class="mdc-button mdc-card__action mdc-card__action--button adopt-form__button">
				<i class="material-icons mdc-button__icon adopt-form__button-icon">pets</i> <span class="adopt-form__button-text">Adopt</span>
			  </button>
			</div>
		  </div>
		</div>
	  </div>
	  <div class="mdc-layout-grid__cell">
		<div class="mdc-card mdc-card--outlined adopt-a-pup-card">
		  <div class="adopt-a-pup-image mdc-card__media mdc-card__media--square" style="background-image: url('./media/5.jpg')"></div>
		  <div class="adopt-a-pup-card__text-label">Brewery</div>
		  <div class="adopt-a-pup-card__secondary mdc-typography--body2">Brewery loves fetching you your favorite homebrew.</div>
		  <div class="mdc-card__actions">
			<div class="mdc-card__action-buttons">
			  <button class="mdc-button mdc-card__action mdc-card__action--button adopt-form__button">
				<i class="material-icons mdc-button__icon adopt-form__button-icon">pets</i> <span class="adopt-form__button-text">Adopt</span>
			  </button>
			</div>
		  </div>
		</div>
	  </div>
	  <div class="mdc-layout-grid__cell">
		<div class="mdc-card mdc-card--outlined adopt-a-pup-card">
		  <div class="adopt-a-pup-image mdc-card__media mdc-card__media--square" style="background-image: url('./media/6.jpg')"></div>
		  <div class="adopt-a-pup-card__text-label">Lucy</div>
		  <div class="adopt-a-pup-card__secondary mdc-typography--body2">Picture yourself in a boat on a river, Lucy is a pup with kaleidoscope eyes.</div>
		  <div class="mdc-card__actions">
			<div class="mdc-card__action-buttons">
			  <button class="mdc-button mdc-card__action mdc-card__action--button adopt-form__button">
				<i class="material-icons mdc-button__icon adopt-form__button-icon">pets</i> <span class="adopt-form__button-text">Adopt</span>
			  </button>
			</div>
		  </div>
		</div>
	  </div>
	  <div class="mdc-layout-grid__cell">
		<div class="mdc-card mdc-card--outlined adopt-a-pup-card">
		  <div class="adopt-a-pup-image mdc-card__media mdc-card__media--square" style="background-image: url('./media/7.jpg')"></div>
		  <div class="adopt-a-pup-card__text-label">Astro</div>
		  <div class="adopt-a-pup-card__secondary mdc-typography--body2">Is it a bird? A plane? No, it's Astro blasting off into your heart!</div>
		  <div class="mdc-card__actions">
			<div class="mdc-card__action-buttons">
			  <button class="mdc-button mdc-card__action mdc-card__action--button adopt-form__button">
				<i class="material-icons mdc-button__icon adopt-form__button-icon">pets</i> <span class="adopt-form__button-text">Adopt</span>
			  </button>
			</div>
		  </div>
		</div>
	  </div>
	  <div class="mdc-layout-grid__cell">
		<div class="mdc-card mdc-card--outlined adopt-a-pup-card">
		  <div class="adopt-a-pup-image mdc-card__media mdc-card__media--square" style="background-image: url('./media/8.jpg')"></div>
		  <div class="adopt-a-pup-card__text-label">Boo</div>
		  <div class="adopt-a-pup-card__secondary mdc-typography--body2">Boo is just a teddy bear in disguise. What he lacks in grace, he makes up in charm.</div>
		  <div class="mdc-card__actions">
			<div class="mdc-card__action-buttons">
			  <button class="mdc-button mdc-card__action mdc-card__action--button adopt-form__button">
				<i class="material-icons mdc-button__icon adopt-form__button-icon">pets</i> <span class="adopt-form__button-text">Adopt</span>
			  </button>
			</div>
		  </div>
		</div>
	  </div>
	  <div class="mdc-layout-grid__cell">
		<div class="mdc-card mdc-card--outlined adopt-a-pup-card">
		  <div class="adopt-a-pup-image mdc-card__media mdc-card__media--square" style="background-image: url('./media/9.jpg')"></div>
		  <div class="adopt-a-pup-card__text-label">Pippa</div>
		  <div class="adopt-a-pup-card__secondary mdc-typography--body2">Pippa likes to look out the window and write pup-poetry</div>
		  <div class="mdc-card__actions">
			<div class="mdc-card__action-buttons">
			  <button class="mdc-button mdc-card__action mdc-card__action--button adopt-form__button">
				<i class="material-icons mdc-button__icon adopt-form__button-icon">pets</i> <span class="adopt-form__button-text">Adopt</span>
			  </button>
			</div>
		  </div>
		</div>
	  </div>

	  <div class="mdc-layout-grid__cell">
		<div class="mdc-card mdc-card--outlined adopt-a-pup-card">
		  <div class="adopt-a-pup-image mdc-card__media mdc-card__media--square" style="background-image: url('./media/10.jpg')"></div>
		  <div class="adopt-a-pup-card__text-label">Coco</div>
		  <div class="adopt-a-pup-card__secondary mdc-typography--body2">Coco enjoys getting pampered at the local puppy spa.</div>
		  <div class="mdc-card__actions">
			<div class="mdc-card__action-buttons">
			  <button class="mdc-button mdc-card__action mdc-card__action--button adopt-form__button">
				<i class="material-icons mdc-button__icon adopt-form__button-icon">pets</i> <span class="adopt-form__button-text">Adopt</span>
			  </button>
			</div>
		  </div>
		</div>
	  </div>
	  <div class="mdc-layout-grid__cell">
		<div class="mdc-card mdc-card--outlined adopt-a-pup-card">
		  <div class="adopt-a-pup-image mdc-card__media mdc-card__media--square" style="background-image: url('./media/11.jpg')"></div>
		  <div class="adopt-a-pup-card__text-label">Brody</div>
		  <div class="adopt-a-pup-card__secondary mdc-typography--body2">Brody is a good boy, waiting for your next command.</div>
		  <div class="mdc-card__actions">
			<div class="mdc-card__action-buttons">
			  <button class="mdc-button mdc-card__action mdc-card__action--button adopt-form__button">
				<i class="material-icons mdc-button__icon adopt-form__button-icon">pets</i> <span class="adopt-form__button-text">Adopt</span>
			  </button>
			</div>
		  </div>
		</div>
	  </div>
	  <div class="mdc-layout-grid__cell">
		<div class="mdc-card mdc-card--outlined adopt-a-pup-card">
		  <div class="adopt-a-pup-image mdc-card__media mdc-card__media--square" style="background-image: url('./media/12.jpg')"></div>
		  <div class="adopt-a-pup-card__text-label">Stella</div>
		  <div class="adopt-a-pup-card__secondary mdc-typography--body2">Stella! Calm and always up for a challenge, she's the perfect companion.</div>
		  <div class="mdc-card__actions">
			<div class="mdc-card__action-buttons">
			  <button class="mdc-button mdc-card__action mdc-card__action--button adopt-form__button">
				<i class="material-icons mdc-button__icon adopt-form__button-icon">pets</i> <span class="adopt-form__button-text">Adopt</span>
			  </button>
			</div>
		  </div>
		</div>
	  </div>
	</div>
  </div>
*/
