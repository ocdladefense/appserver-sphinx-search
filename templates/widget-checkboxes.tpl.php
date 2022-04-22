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

    <script>
        //needs to be in a start up function


        function toggleButtonClicked(buttonId) {
            let button = document.getElementById(buttonId.id);
            button.classList.toggle("repository-selected");
            let localRepos = document.getElementById("repos");

            toggledButtons = document.querySelectorAll(".repository-selected");

            localRepos.value = "";
            toggledButtons.forEach(button => {
                let newRepoValue = localRepos.value + ' ' + button.id;
                localRepos.value = newRepoValue;});
            //console.log(buttonId.id);
            
        }

        function formSubmit() {
            let myString = document.getElementById("terms").value;

            if ((typeof myString === 'string' || myString instanceof String) && myString != "") {
                document.getElementById("formbase").action = "/search/" + myString;
            }
            else {
                document.getElementById("formbase").action = "/search/page";
            }
            
        }

        //more script under html
    </script>

    <div>
    <aside>
        <form id="formbase" action="/test/1" method="post">
            
            <label for="terms">Search Term: </label>
            <input type="text" id="terms" name="terms" />
            <input type="hidden" id="repos" name="repos" value="People, Places, Library, Blog, Case, Publications, Products, Seminars, Motions, Videos, wiki_main" />
            <br />

            

            <?php
            
            foreach ($repos as $innerArray) {
                //  Check type
                if (is_array($innerArray)){
                    //  Scan through inner loop
                    $rep = $innerArray["IdName"];
                    if ($innerArray["Render"] == true) {
                        echo "<div> <input type='checkbox' id='{$innerArray["IdName"]}' name='repos[]' value='{$innerArray["RealName"]}' class='search-filter repository-selected noselect' title='' /> <label> {$innerArray["DisplayName"]} </label> </div>";
                    }

                }
            }

            ?>
            

            <input id="submitButton" type="submit" value="Submit" style="align:center;" />
            
        </form>

        <div class="buttons" id="checkboxHolder">
        
        

        </div>

        
    </aside>


</div>

<script>
    const form = document.getElementById('formbase');
    form.addEventListener('submit', formSubmit);

    //[FriendlyName, MachineName, IsInabled, ActiveByDefault (currently does nothing), title] 
    //const checkboxArray = [["People", "NA", false, false, "Search OCDLA members, expert witnesses, and judges."], ["Places", "NA", false, false, "Search cities and counties."], ["Library of Defence", "NA", false, false, "Search Library of Defense subject articles."], ["Blog", "NA", false, false, "Search Library of Defense blog posts."], ["Case Reviews", "Carstuff", false, false, "Search Criminal Appellate Review summaries."], ["Publications", "NA", false, false, "Search OCDLA publications."], ["Products", "ocdla_products", true, false, "Search OCDLA products."], ["Videos", "NA", false, false, "Search video transcripts from OCDLA seminars and events."], ["Seminars & Events", "NA", false, false, "Search OCDLA Events."], ["Motions", "NA", false, false, "Search the legacy motion bank."], ["ocdla.org", "NA", false, false, "Search the ocdla.org website."]];

    //checkboxArray.forEach(repo => {
    //    if (repo[2] == true) {
    //        let element = document.getElementById("checkboxHolder");
    //        let tag = `<input type="checkbox" id=${repo[0]} value=${repo[1]} class="search-filter repository-selected noselect" onclick="toggleButtonClicked(${repo[0]})" title="">${repo[0]}</input>
    //        <div> `;
    //        element.insertAdjacentHTML("beforeend", tag);
            
    //    }});
</script>