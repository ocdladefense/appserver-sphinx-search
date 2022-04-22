<?php


interface ISnippet {


    public function getDocumentIds();


    public function loadDocuments($productIds);


    public function newResult($docId);

    //public function getSnippets();


}