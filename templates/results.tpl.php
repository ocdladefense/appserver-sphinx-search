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

<h2 class='summary'>
    Showing results for <i><?php print $terms; ?></i>
</h2>

<?php foreach($results as $result): ?>
    <?php print $result->toHtml(); ?>
<?php endforeach; ?>