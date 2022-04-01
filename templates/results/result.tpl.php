<?php
/**
 * Template file for rendering a Product search result.
 * 
 * Available variables:
 *  $title The name of the Product.
 *  $snippet The summary of the search result.
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


<?php
 /*
$snippet = str_replace('&nbsp;', ' ', $snippet);
$snippet = '<div style="line-height:15px;">'.$snippet."</div>";
$name = "<h2 style='font-size:12pt;'><a href='{$shoplink}' target='_blank'>{$product['Name']}</a></h2>";

self::enqueue('<div class="search-result" style="margin-bottom:14px;">'.$name.$snippet.'</div>');
*/