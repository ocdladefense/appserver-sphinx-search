/** @jsx vNode */
import { vNode, View } from '/node_modules/@ocdladefense/view/view.js';
startFetch();

function startFetch() {
  var videoNodes = document.querySelectorAll("div.search-result-video");
  var videoIds = [];
  videoNodes.forEach(function (element) {
    return videoIds.push(element.id);
  }); //let videoIds = ["4mxFb5VH12Y"]; //this is populated by for each loop with resource ids

  var endpoint = "https://www.googleapis.com/youtube/v3/videos";
  var parts = "snippet,contentDetails,statistics";
  var params = videoIds.toString();
  var apiKey = "AIzaSyB95m4ud1CBRSP-4evnK_ng8CkMBG6Hyu0"; //https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails,statistics&id=4mxFb5VH12Y&key=AIzaSyB95m4ud1CBRSP-4evnK_ng8CkMBG6Hyu0

  var url = endpoint + "?part=" + parts + "&id=" + params + "&key=" + apiKey;
  fetch(url).then(function (response) {
    return response.json();
  }).then(youtubeDataApiToImage);
}

function youtubeDataApiToImage(videoObjects) {
  var configphplink = store_url + "/Videos?id="; //"https://www.youtube.com/watch?v="

  videoObjects.items.forEach(function (video) {
    var vid = video.id;
    var el = document.getElementById(vid); //https://ocdla.force.com/Videos?id=

    var anchor = createImage(video, el.dataset.media, configphplink, "medium", 200);
    var createdAnchor = View.createElement(anchor);
    document.getElementById(vid).appendChild(createdAnchor);
  });
}

function createImage(info, linkEnd) {
  var linkUrl = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : "https://www.youtube.com/watch?v=";
  var res = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : "medium";
  var size = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : 200;
  var thumbnails = info.snippet.thumbnails;
  res = ["default", "medium", "high", "standard", "maxres"].includes(res) ? res : "medium";
  var src = thumbnails[res].url;
  return vNode("a", {
    href: linkUrl + linkEnd,
    target: "_blank"
  }, vNode("img", {
    src: src,
    width: size + "px",
    height: "auto"
  }));
}