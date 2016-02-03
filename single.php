<?php
//
// //var_dump($post);
// //$contacto = get_post( $GLOBALS['special_pages_ids']['contacto']);
var_dump(new Cltvo_Page_Contacto);
//
// echo "<hr>";
$Posts = get_posts(["post_type" => "any"]);

foreach ($Posts as $post) {
    var_dump((new Cltvo_Lookbook));
}
