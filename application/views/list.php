<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (count($items) === 0)
{
    echo 'Nincs találat...';
}
else
{
    echo $links.'<br>';
    echo '<table border="1" cellspacing="0">';

    $headers = array_keys($items[0]);
    echo '<th>'.implode('</th><th>', $headers).'</th>';

    foreach ($items as $i) 
    {
        echo '<tr valign="top">';
        foreach ($i as $f) 
        {
            echo '<td>'.html_escape($f).'</td>';
        }
        echo '</tr>';
    }

    echo '</table>';
    echo '<br>'.$links;
}

