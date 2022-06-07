<?php
/**
 * Template results.tpl.php
 * 
 * Display the search results.
 * Available variables:
 *  $terms Search terms entered by the user.
 *  $results HTML Representation of search terms.
 */
?>

<?php print $widget; ?>

<div class="searchResults">
    <h2 class='summary'>
        Showing results for <i><?php print $terms; ?></i>
    </h2>

    <?php foreach($results as $result): ?>
        <?php print $result->toHtml(); ?>
    <?php endforeach; ?>
</div>


<script src="/modules/sphinx/assets/js/components/video.js" type="module">
</script>

