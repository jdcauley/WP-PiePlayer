<?php
/* Plugin Name: WP PiePlayer
 * Plugin URI: 
 * Description: Using the power of Simple Pie to parse RSS feeds and MediaElements to support the embed. ex. [parse_feed feed="http://example.com/feed" num="8"(optional) name="Example Title"(optional) url="http://example.com/"(optional) ]
 * Author: Jordan Cauley
 * Version: 1.0
 * Author URI: http://jordancauley.com/
 */

include(dirname(__FILE__).'/simplepie.inc');

function get_feed($atts, $content = null){
	
	extract(shortcode_atts(array(
		"feed" => 'http://',
		"num" => '1',
		"name" => '',
		"url" => 'http://',
		//"showDesc" => 'true',
	), $atts));
	
	// Parse it
	$feed = new SimplePie();
	
	$feed->set_feed_url($atts['feed']);
	$feed->enable_cache(true);
	$feed->set_cache_location(dirname(__FILE__) . '/cache');
	$feed->set_cache_duration(3600);
	$feed->init();
	
	$feed->handle_content_type();
	
        $html = '<div id="'.str_replace(' ', '', strtolower( $feed->get_title() ) ).'" class="sp_results">';
        if ($feed->data):
            $items = $feed->get_items(0,$atts['num']);
            $html .= '<div class="source-image" id="'.str_replace(' ', '', strtolower( $feed->get_title() ) ) . '-logo"></div>';
            $html .= '<h2><a href="'.(($atts['url'])?$atts['url']:$feed->get_permalink()).'">'.$feed->get_title().'</a></h2>';
            foreach($items as $item):
                $html .= '<div class="chunk" style="padding:0 5px;margin-bottom: 8px;">';
                    $html .= '<h4>'.$item->get_title().'</a></h4>';
                    $html .= '<h6>'.$item->get_date('M j Y').'</h6>';
                    //if($showDesc == 'false'){
                    	$html .= $item->get_content();
                	//}
                	if ($enclosure = $item->get_enclosure(0)){
-						$html .= '<audio controls="controls" preload="none" src="' . $enclosure->get_link() . '" type="audio/mp3"><!-- Flash fallback for non-HTML5 browsers without JavaScript -->
							    <object type="application/x-shockwave-flash" data="flashmediaelement.swf">
							        <param name="audio" value="flashmediaelement.swf" />
							        <param name="flashvars" value="controls=true&file=' . $enclosure->get_link() . '" />
							        <!-- Image as a last resort -->
							      
							    </object>
							</audio>';
					}
                $html .= '</div><hr>';
            endforeach;  
        endif;
        $html .= '</div>';
        
        return $html;
} 

add_shortcode('parse_feed', 'get_feed');

?>