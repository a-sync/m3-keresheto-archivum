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
                <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">ID</th>
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
                <td class="mdc-data-table__cell mdc-data-table__cell--numeric mdc-typography--caption"><?php echo html_escape($i['program_id']); ?></td>
                <td class="mdc-data-table__cell cell__title">
                    <span class="mdc-typography--body1 cell__title--title"><?php echo html_escape($i['title']); ?></span>
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

// https://archivum.mtva.hu/images/m3/M3-87130999959999A59
// https://archivum.mtva.hu/m3/stream?no_lb=1&target=M3-87130999959999A59
// if ($i[hasSubtitle]) https://archivum.mtva.hu/subtitle/M3-87130999959999A59.srt

