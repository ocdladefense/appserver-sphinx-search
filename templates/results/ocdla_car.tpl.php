<?php
/**
 * Template file for rendering a generic search result.
 * 
 * Available variables:
 *  $title The name of the document.
 *  $snippet The summary of the document.
 *  $url A link to the document source.
 */
?>

<div class="search-result">
    <h2 style='font-size:12pt;'>
        <a href='https://ocdla.app/car/list/<?php print $url; ?>' target='_blank'>
            OCDLA CAR - <?php print $title; ?>
        </a>
    </h2>
    <div class="snippet">
        <?php print $snippet; ?>
    </div>
</div>

