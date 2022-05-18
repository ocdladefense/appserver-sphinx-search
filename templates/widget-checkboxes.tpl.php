<?php
/**
 * @template widget-checkboxes.tpl.php
 * 
 * Template to display repositories as checkboxes.
 */
?>

<style type="text/css">
 .buttons li {
     display: inline-block;
     padding:6px;
     border-radius: 6px;
     border: 1px solid #eee;
     cursor: pointer;
 }

 .repository-selected {
     color: #B8860B;
     background-color: #ddd;
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

 input[type=text] {
    padding:5px; 
    border:2px solid #ccc; 
    -webkit-border-radius: 5px;
    border-radius: 5px;
}

input[type=text]:focus {
    border-color:#333;
}

input[type=submit] {
    padding:5px 15px; 
    background:#ccc; 
    border:0 none;
    cursor:pointer;
    -webkit-border-radius: 5px;
    border-radius: 5px; 
}

aside {
  width: 20%;
  padding-top: 15px;
  margin-top: 0px;
  padding-left: 15px;
  margin-left: 15px;
  padding-right: 15px;
  margin-right: 15px;
  padding-bottom: 25px;
  margin-bottom: 15px;
  float: left;
  font-style: italic;
  background-color: lightgray;
  border-bottom-right-radius: 30px;
}

#submitButton {
    background-color: rgb(163, 161, 39);
}

.search-filter {
    color: rgb(68, 80, 146);
}

/* https://stackoverflow.com/questions/826782/how-to-disable-text-selection-highlighting */
.noselect {

  -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
     -khtml-user-select: none; /* Konqueror HTML */
       -moz-user-select: none; /* Old versions of Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version, currently
                                  supported by Chrome, Edge, Opera and Firefox */
}

</style>




    <div>

    <?php
    // Some testing code to figure out how to pre-check the 
    // appropriate checkboxes.
    // var_dump($repos); 
    // var_dump($selected);
    ?>

    <aside>
        <form id="search" action="/search" method="GET">
            
            <div class="form-item">
                <label for="terms">Search Terms: </label>
                <input type="text" id="terms" name="q" value="<?php print $q; ?>" />
            </div>



            <?php foreach($repos as $repo): ?>

                <?php 
                    $key = $repo["key"];
                    $isSelected = isset($selected[$key]);
                ?>
                <div class="form-item">
                    <input type="checkbox" id="<?php print $repo["id"]; ?>" name="repos[]" value="<?php print $repo["name"]; ?>" class="search-filter repository-selected noselect" title="" <?php print ($isSelected ? "checked" : ""); ?> />
                    <label><?php print $repo["display"]; ?></label>
                </div>

            <?php endforeach; ?>


            <div class="form-item">
                <input id="submit" type="submit" value="Submit" style="align:center;" />
            </div>
            
        </form>


    </aside>

    </div>



<script>

    const form = document.getElementById('search');

</script>