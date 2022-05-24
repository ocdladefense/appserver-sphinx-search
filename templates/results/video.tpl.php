<?php
/**
 * Template file for rendering a Wiki search result.
 * 
 * Available variables:
 *  $title The name of the Product.
 *  $snippet The summary of the search result.
 *  $url A link to the document source.
 */
?>
<div class="search-result search-result-video" style="margin-top:15px; padding-top:10px;border-top:1px solid #ccc;">
    <h2 style='font-size:12pt;'> 
        I am a video
        <a href='<?php print $url; ?>' target='_blank'>
            <?php print $title; ?>
        </a>
    </h2>
    <div class="snippet">
        <?php print $snippet; ?>
    </div>
</div>