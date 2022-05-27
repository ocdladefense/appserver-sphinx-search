startFetch();


function startFetch() {

    let videoNodes = document.querySelectorAll("div.search-result-video");
    let videoIds = [];
    videoNodes.forEach(element => videoIds.push(element.id));

    //let videoIds = ["4mxFb5VH12Y"]; //this is populated by for each loop with resource ids

    let endpoint = "https://www.googleapis.com/youtube/v3/videos";
    let parts = "snippet,contentDetails,statistics";
    let params = videoIds.toString();
    let apiKey = "AIzaSyB95m4ud1CBRSP-4evnK_ng8CkMBG6Hyu0";

    let url = endpoint+"?part="+parts+"&id="+params+"&key="+apiKey;

    fetch(url)
    .then(response => response.json())
    .then(youtubeDataApiToImage);
}

function youtubeDataApiToImage(videoObjects) {

    for(let i = 0; i < videoObjects.items.length; i++) {
        let info = videoObjects.items[i].snippet;
        let itemId = videoObjects.items[i].id;
        
        let title = info.title;
        let desc = info.description;
        let thumbnails = info.thumbnails;

        let src = thumbnails.medium.url;
        let ytImage = document.createElement("img");
        ytImage.setAttribute("src", src);
        //let ytTitle = document.createElement("p");
        //ytTitle.setAttribute("text", title);
        //let ytDesc = document.createElement("p");
        //ytDesc.setAttribute("text", desc);
        
        document.getElementById(itemId).appendChild(ytImage);
        //document.querySelector("#"+itemId).appendChild(ytImage);
    }
}



