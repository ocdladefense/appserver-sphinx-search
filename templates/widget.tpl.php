<?php

// Your template code goes here.



?>

<style type="text/css">
 .buttons li {
     display: inline-block;
     padding:6px;
     border-radius: 6px;
     background-color:#efefef;
     border: 1px solid #eee;
     cursor: pointer;
 }

 li.filter-active {
     background-color:#ddd;
 }

 .buttons li:hover {
     background-color:#ddd;
 }

 ul.buttons {
     margin-bottom: 25px;
 }

 h2.summary {
     margin-bottom:35px;
 }
    </style>

    <ul class="buttons">
        <li type="checkbox" value="People" class="search-filter" title="Search OCDLA members, expert witnesses, and judges.">People</li>
        <li type="checkbox" value="Places" class="search-filter" title="Search cities and counties.">Places</li>
        <li type="checkbox" value="Library of Defence" class="search-filter" title="Search Library of Defense subject articles.">Library of Defense</li>
        <li type="checkbox" value="Blog" class="search-filter" title="Search Library of Defense blog posts.">Blog</li>
        <li type="checkbox" value="Case Reviews" class="search-filter" title="Search Criminal Appellate Review summaries.">Case Reviews</li>
        <li type="checkbox" value="Publications" class="search-filter" title="Search OCDLA publications.">Publications</li>
        <li type="checkbox" value="Products" class="search-filter filter-active" title="Search OCDLA products.">Products</li>
        <li type="checkbox" value="Seminars & Events" class="search-filter" title="Search OCDLA Events.">Seminars & Events</li>
        <li type="checkbox" value="Motions" class="search-filter" title="Search the legacy motion bank.">Motions (motion bank)</li>
        <li type="checkbox" value="Videos" class="search-filter" title="Search video transcripts from OCDLA seminars and events.">Videos</li>
        <li type="checkbox" value="ocdla.org" class="search-filter" title="Search the ocdla.org website.">ocdla.org</li>
    </ul>

<form action="/search" method="post">
    
    <label for="term">Search Term</label>
    <input type="text" id="term" name="term" />
    <input type="hidden" id="repos" name="repos" value="ocdla_products, wiki_main" />

    <input type="submit" value="Submit" />
    
</form>