/** @jsx vNode */


import { vNode, View } from '/node_modules/@ocdladefense/view/view.js';

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

    //https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails,statistics&id=4mxFb5VH12Y&key=AIzaSyB95m4ud1CBRSP-4evnK_ng8CkMBG6Hyu0
    let url = endpoint+"?part="+parts+"&id="+params+"&key="+apiKey;

    fetch(url)
    .then(response => response.json())
    .then(youtubeDataApiToImage);
}

function youtubeDataApiToImage(videoObjects) {

    let configphplink = store_url+"/Videos?id="; //"https://www.youtube.com/watch?v="
    

    videoObjects.items.forEach(function (video) {
        let vid = video.id;
        const el = document.getElementById(vid);
        
        

        //https://ocdla.force.com/Videos?id=
        let anchor = createImage(video, el.dataset.media, configphplink, "medium", 200);

        let createdAnchor = View.createElement(anchor);
        document.getElementById(vid).appendChild(createdAnchor);
    } );
}


function createImage(info, linkEnd, linkUrl = "https://www.youtube.com/watch?v=", res = "medium", size = 200) {

    let thumbnails = info.snippet.thumbnails;

    res = ["default", "medium", "high", "standard", "maxres"].includes(res) ? res : "medium";
    let src = thumbnails[res].url;

    return (

        <a href={linkUrl + linkEnd} target="_blank">

            <img src={src} width={size+"px"} height="auto" />
        </a>
    );

}


