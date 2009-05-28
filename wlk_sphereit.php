<?php
$plugin['version'] = '0.3.5';
$plugin['author'] = 'Walker Hamilton';
$plugin['author_uri'] = 'http://www.walkerhamilton.com';
$plugin['description'] = 'Put the sphereIt! widget on your site.';

$plugin['type'] = 0;

@include_once(dirname(dirname(__FILE__)).'/zem_tpl.php');

if(0) {
?>
# --- BEGIN PLUGIN HELP ---
h1. wlk_sphereit

This plugin puts sphereIt on your site. Also....Uh.....this shit is GNU GPL etc etc, same as the original said ( http://www.sphere.com/tools )

Use this in place of the txp:body tags, in your forms. It'll place @<!-- sphereit start -->@ & @<!-- sphereit end -->@ around the body of your article.
If place the sphereIt start & end manually around the txp:body tag in your form, you can don't need to use this tag.

@<txp:wlk_spherebody />@


Put this in the head of the pages that'll have sphereIt embedded:

@<txp:wlk_spherehead />@

Takes one argument:

* leanings - Set to "political_dem", "political_rep", or "political_gen" for Left Leaning, Right Leaning, or general political info respectively. If you aren't a political blog or don't give a shit, then don't set this.


Then put this in the forms you're using to output those articles:

@<txp:wlk_sphereit />@

Takes a few arguments:

* auto - Set as either true or false. Tells wlk_sphereit to embed automatically (if threshold is true, must also meet threshold requirements....auto defaults to true),
* threshold - Set as either true or false. Tells wlk_sphereit to obey a character-limit _&_ word-limit threshold (defaults to true),
* min_words - Set as a number. Tells wlk_sphereit the minimum number of words that must be in an entry in order for embed to occur, if threshold it set to true or has defaulted to true (defaults to 30)
* min_chars - Set as a number. Tells wlk_sphereit the minimum number of characters that must be in an entry in order for embed to occur, if threshold it set to true or has defaulted to true (defaults to 500)
* linktext - Set the link text for the sphereit widget to something other than "Sphere: Related Content"
* control_fieldname - Set as the name of a custom_field. Tells wlk_sphereit which custom_field to look in to see if the control word is set.

The following must be read carefully. If people ask me about this on the forums, I'll be annoyed.

# If auto is set to "false" and the control word it set to "true", sphereIt will embed itself.
# If auto is set to "false" and the control word is set to "false" or left blank, sphereIt will not embed.
# If auto is set to "true" or you have let it default to "true" and the control word is set to "false", sphereIt will not embed.
# If auto is set to "true" or you have let it default to "true" and the control word is set to "true" or left blank, sphereIt will embed itself.
# If number 1 or number 4 is the outcome but threshold is set to true & not met by the current entry (too few words or too few characters), sphereIt will not embed itself.
# If number 1 or number 4 is the outcome and threshold is set to false, sphereIt will embed itself no matter the entry content or length.

Put this in your stylesheet if you want:

<pre><code>a.iconsphere
{
	background: url(http://www.sphere.com/images/sphereicon.gif) top left no-repeat;
	padding-left: 20px;
	padding-bottom: 10px;
	font-size: 10px;
	white-space: nowrap;
}</code></pre>

That's it. "let me know":http://walkerhamilton.com/contact if you find bugs.

# --- END PLUGIN HELP ---
<?php
}

# --- BEGIN PLUGIN CODE ---

function wlk_sphereit($atts) 
{
	global $prefs;
	global $permlink_mode;
	global $thisarticle;
	
	extract(lAtts(array(
		'auto'=>(!empty($prefs['wlk_sphereit_auto']))?$prefs['wlk_sphereit_auto']:'true',
		'threshold'=>(!empty($prefs['wlk_sphereit_threshold']))?$prefs['wlk_sphereit_threshold']:'true',
		'min_words'=>(!empty($prefs['wlk_sphereit_auto']))?$prefs['wlk_spherit_min_words']:'30',
		'min_chars'=>(!empty($prefs['wlk_sphereit_auto']))?$prefs['wlk_sphereit_min_chars']:'500',
		'linktext'=>(!empty($prefs['wlk_sphereit_linktext']))?$prefs['wlk_sphereit_linktext']:'Sphere: Related Content',
		'spherecontrol'=>(!empty($prefs['wlk_sphereit_control_fieldname']))?$prefs['wlk_sphereit_control_fieldname']:'sphereit'
	),$atts));
		
	if(isset($thisarticle[$spherecontrol])) {
		if($thisarticle[$spherecontrol]=='off' || $thisarticle[$spherecontrol]=='on') {
			$sphereswitch = $thisarticle[$spherecontrol];
		} else if($auto=='true') {
			$sphereswitch = 'on';
		} else {
			$sphereswitch = 'off';
		}
	} else if($auto=='true') {
		$sphereswitch = 'on';
	} else {
		$sphereswitch = 'off';
	}

	if($auto==true && $sphereswitch=='off') {
		return '';
	} else if($auto==false && $sphereswitch!='on') {
		return '';
	} else if($threshold=='true') {
		$num_words = count(explode(' ',$thisarticle['body']));
		$num_chars = strlen($thisarticle['body']);
		if($num_words<$min_words && $num_chars<$min_chars) { return ''; }
	}

	$silink = permlinkurl($thisarticle);
	$sil = '<span class="sphereitLink"><a class="iconsphere" title="'.$linktext.'" onclick="return Sphere.Widget.search(\''.$silink.'\')" href="http://www.sphere.com/search?q=sphereit:'.$silink.'">'.$linktext.'</a></span>';
	return $sil;
}

function wlk_spherebody() {
	global $thisarticle;
	
	$content_orig = $thisarticle['body'];
	$content = '<!-- sphereit start -->'."\r";
	$content.= $content_orig;
	$content.= '<!-- sphereit end -->'."\r";
	
	return $content;
}

function wlk_spherehead($atts) {
	global $prefs;
	global $permlink_mode;
	global $thisarticle;
	extract(lAtts(array('leanings'=>(!empty($prefs['wlk_spherehead_leanings']))?$prefs['wlk_spherehead_leanings']:'wordpressorg'),$atts));
	return '
		<script type="text/javascript" src="http://www.sphere.com/widgets/sphereit/js?t='.$leanings.'&amp;p=wordpressorg"></script>
	';
}

# --- END PLUGIN CODE ---
?>