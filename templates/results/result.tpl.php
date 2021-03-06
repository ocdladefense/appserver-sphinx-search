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
        <a href='<?php print $url; ?>' target='_blank'>
            <?php print $title; ?>
        </a>
    </h2>
    <div class="snippet">
        <?php print $snippet; ?>
    </div>
</div>

