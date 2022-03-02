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
    <li class="search-filter" title="Search OCDLA members, expert witnesses, and judges.">People</li>
    <li class="search-filter" title="Search cities and counties.">Places</li>
    <li class="search-filter" title="Search Library of Defense subject articles.">Library of Defense</li>
    <li class="search-filter" title="Search Library of Defense blog posts.">Blog</li>
    <li class="search-filter" title="Search Criminal Appellate Review summaries.">Case Reviews</li>
    <li class="search-filter" title="Search OCDLA publications.">Publications</li>
    <li class="search-filter filter-active" title="Search OCDLA products.">Products</li>
    <li class="search-filter" title="Search OCDLA Events.">Seminars & Events</li>
    <li class="search-filter" title="Search the legacy motion bank.">Motions (motion bank)</li>
    <li class="search-filter" title="Search video transcripts from OCDLA seminars and events.">Videos</li>
    <li class="search-filter" title="Search the ocdla.org website.">ocdla.org</li>
</ul>