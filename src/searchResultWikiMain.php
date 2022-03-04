<?php

class SearchResultWikiMain{
    private $conn;

    private static $ids = array();

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public static function addResult($r)
    {
        self::$ids[] = $r;
    }

    public function getDocs()
    {
        //SELECT page_id, page_title, page_namespace FROM page WHERE page_id = $docId
        /*
            Refer to Database::setDefault()
            $api = $this->loadForceApi()
            SELECT page_id, 'wiki_main' AS indexname, page_title, page_namespace, page_is_redirect, old_id, old_text FROM page, revision, text WHERE rev_id=page_latest AND old_id=rev_text_id AND page_id IN ($docIds)
            https://libraryofdefense.ocdla.org/page_namespace

        */

        foreach( $docIds as $page_id ) {
			$res = $this->db->select(
				'page',
				array( 'page_id', 'page_title', 'page_namespace' ),
				array( 'page_id' => $page_id ),
				__METHOD__,
				array()
			);
			if ( $this->db->numRows( $res ) > 0 ) {
				$mResultSet[] = $this->db->fetchObject( $res );
			}
		}

    }
    
}