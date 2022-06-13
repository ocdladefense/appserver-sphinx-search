<?php
/**
 * @template widget-checkboxes.tpl.php
 * 
 * Template to display repositories as checkboxes.
 */
?>




    <div>

    <?php
    // Some testing code to figure out how to pre-check the 
    // appropriate checkboxes.
    // var_dump($repos); 
    // var_dump($selected);
    ?>
    <link rel="stylesheet" href="<?php print module_path(); ?>/assets/css/widget-checkboxes.css">
    <div class="searchBar">
        <form id="search" action="/search" method="GET">
            
            <div class="form-item searchHamburger">
                <div class="searchTerms">
                    <label for="terms">Search Terms: </label>
                    <input type="text" id="terms" name="q" value="<?php print $q; ?>" />
                </div>

                <div class="hamburger">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>
            </div>

            <div class="form-item">
                <input id="submit" type="submit" value="Submit" style="align:center;" />
            </div>

            <div class="checkboxes noselect">
                <?php foreach($repos as $repo): ?>

                    <?php 
                        $key = $repo["key"];
                        $isSelected = isset($selected[$key]);
                    ?>
                    <div class="form-item">
                        <input type="checkbox" id="<?php print $repo["id"]; ?>" name="repos[]" value="<?php print $repo["name"]; ?>" class="search-filter repository-selected noselect" title="" <?php print ($isSelected ? "checked" : ""); ?> checked/>
                        <label for="<?php print $repo["id"]; ?>" class="checkboxLabel"><?php print $repo["display"]; ?></label>
                    </div>
                <?php endforeach; ?>
            </div>


            
            
        </form>


    </div>

    </div>



<script>

    const form = document.getElementById('search');

    const hamburger = document.querySelector(".hamburger");
    const checkboxes = document.querySelector(".checkboxes");

    hamburger.addEventListener("click", () => {
        hamburger.classList.toggle("active");
        checkboxes.classList.toggle("noselect");
    });



</script>