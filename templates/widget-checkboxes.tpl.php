<?php
/**
 * @template widget-checkboxes.tpl.php
 * 
 * Template to display repositories as checkboxes.
 */
?>


<?php
    // Some testing code to figure out how to pre-check the 
    // appropriate checkboxes.
    // var_dump($repos); 
    // var_dump($selected);
?>


<link rel="stylesheet" href="<?php print module_path(); ?>/assets/css/widget-checkboxes.css" />
<div class="searchBar">
    <form id="search" action="/search" method="GET">
        
        <div class="form-group">
            <div class="form-item search-terms">
                <input type="text" id="terms" name="q" value="<?php print $q; ?>" placeholder="Enter search terms here"/>
            </div>

            <div class="form-item search-button">
                <input id="submit" type="submit" value="Submit" />
            </div>

            <div class="form-item hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>

        
        <div class="checkboxes noselect form-group">
            <?php foreach($repos as $repo): ?>

                <?php 
                    $key = $repo["key"];
                    $isSelected = isset($selected[$key]);

                ?>
                <div class="form-item">
                    <input type="checkbox" id="<?php print $repo["id"]; ?>" name="repos[]" value="<?php print $repo["name"]; ?>" class="search-filter repository-selected noselect " title="" <?php print (str_contains($checkedRepos, $repo['name']) ? "checked " : ""); ?> />
                    <label for="<?php print $repo["id"]; ?>" onclick="function(){}" class="checkboxLabel <?php print (str_contains($checkedRepos, $repo['name']) ? "checkToggled " : "checkUntoggled"); ?>"><?php print $repo["display"]; ?> </label>
                </div>
            <?php endforeach; ?>
        </div>


        
        
    </form>


</div>





<script>


    const hamburger = document.querySelector(".hamburger");
    const checkboxes = document.querySelector(".checkboxes");

    const checkboxLabels = document.querySelectorAll(".checkboxLabel");

    hamburger.addEventListener("click", () => {
        hamburger.classList.toggle("active");
        checkboxes.classList.toggle("noselect");
    });

    checkboxLabels.forEach(element => element.addEventListener("click", () => {
        element.classList.toggle("checkToggled");
        element.classList.toggle("checkUntoggled");
    }));



</script>