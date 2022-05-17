<?php
/**
 * Template search.tpl.php
 * 
 * Display the search form.
 * Available variables:
 *  
 */
?>

<form action="/search" method="get">
    <div class="form-item">
        <img src="/content/images/logo.png" />
    </div>
    <div class="form-item">
        <input id="search" type="text" name="q" size="60" placeholder="Search directory, case reviews and library of defense..." />
    </div>
</form>

<style type="text/css">
@media screen and (min-width: 1024px) {
    #stage-content {
        width: 80%;
        margin: 0 auto;
        text-align: center;
    }

    #search {
        padding:10px;
        font-size:1.5em;
    }
}
</style>