<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Created for Phpfusion 8
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
+--------------------------------------------------------+
| Package: New Menu System 
| Bundled: Atom
| PHP-Fusion version: 8.00.1
| Atom version:1
+--------------------------------------------------------*/
// Define Sitelinks
define ("DB_SITELINKS", DB_PREFIX."site_links");


function menu_hierarchy() {
$refs = array();
$list = array();

$result = dbquery("SELECT * FROM ".DB_SITELINKS." ORDER BY link_order ASC"); 
while ($data = dbarray($result)) {
	// definition;
	$thisref = &$refs[$data['link_id']];
	$thisref['link_cat'] = $data['link_cat'];
	$thisref['link_name'] = $data['link_name'];
	$thisref['link_id']	=	$data['link_id'];
	$thisref['link_url'] = $data['link_url'];
	$thisref['link_class'] = $data['link_class'];
	$thisref['link_visibility'] = $data['link_visibility'];
	$thisref['link_img'] = $data['link_img'];
	
	if ($data['link_cat'] == 0) {
	$list[$data['link_id']] = &$thisref;
	} else { 
	$refs[ $data['link_cat']]['children'][$data['link_id']] = &$thisref;
	}
	
}
return $list;
} 

// ## Hierarchy Compiler for Megamenu System
function build_menu($arr, $ident){
global $settings, $userdata, $aidlink, $locale;

if (!isset($ident)) { $ident = 0; }
  
  if ($ident == "1") {
  $html = "<ul class='sub'>\n";
  } else if ($ident >"1") { 
  $html = "<ul>";  // check current ident add: <li>".$ident."</li>
  } else  {
  $html = "<ul id='mega'><li class='spacer' style='width:20px;'></li><li class='top' style='width:160px;'><a id='logo' class='top_link' href='".BASEDIR."index.php'><span style='margin-top:0px; margin-left:-46px;'><img src='".THEME."images/phpfusion-small.png' style='margin-top:-5px;'></span></a></li><li class='vd'></li>"; 
  
  
  // Searchbox
 // 	include THEME."includes/header_search_includes.php";
	
//	$searchbox .="<li class='top_link'>aaa</li>
	
//	<div style='display:block; float:left; position:relative; width:100px; border:1px solid #000'>";
//	$searchbox .="<form id='top-search' class='search-wrap' name='top-search' action='".BASEDIR."search.php' method='get'><div class='search-input-wrap textbox flleft'>";

//	if ($stype != "") {
//	$searchbox .="<span id='search_area' class='search button mini flleft'>".$text." &nbsp;X</span>";
//	$searchbox .="<input type='hidden' value='".$stype."' id='search_type' name='stype' />";
//	}
		
//	$searchbox .="<span id='placeholder' class='flleft'>Search</span><input id='sinput' type='text' style='' class='textbox-search flleft' value='' name='stext' /></div>";
//	$searchbox .="<button type='submit' class='button search flleft'><img src='".THEME."images/search.png' alt='Search' /></button>";
//	$searchbox .="</form></div>";
   
  }
 
  
   $count_array = count($arr); $i =1;
   foreach ($arr as $v) {
	// Lets say visible is 100, and userdata is 103
	if (iMEMBER) { 
	// Hides link_visibility 1 from Member because that is only for Guest to See. iAdmin is solved by first condition.
	if (($userdata['user_level'] >= $v['link_visibility']) && ($v['link_visibility'] !=="1")) {
		// ... show
		
				// new Link class introduced ID Assignment for Jquery call
				if ($v['link_class'] !=="") { $menu_id = $v['link_class']; } else { $menu_id = $v['link_id']; }
				
				// Atom Theming
			  	if ($v['link_cat'] !=="0") {
			  	$liclass = ""; $aclass=""; $active=""; $spanclass = ""; 	
				if ($count_array == $i) { $seperator = ""; } else { $seperator = "<li class='hd'></li>";  $i++; 	}
			  	} else {
			  	if ($v['link_url'] == "index.php") { $uri = $settings['opening_page']; } else { $uri = $v['link_url']; } // Set new var 
			  	if (START_PAGE == $uri) { $active = "mega-active"; $spanclass = "active-span"; } else { $active=""; $spanclass = "down"; } // top class 
			  	// Define Elements
			  	$liclass = "top"; $aclass = "top_link"; $seperator = "<li class='vd'></li>"; // top class definition including seperators
			  	}
				
				if ($v['link_class'] == "user") {
				$html .= "<li class='$liclass'><a id='$menu_id' class='$aclass $active' href='".BASEDIR."profile.php?lookup=".$userdata['user_id']."'><span class='$spanclass'>".$userdata['user_name']."</span></a>"; // list
				} elseif ($v['link_class'] == "user2") {
				$html .= "<li class='$liclass'><a id='$menu_id' class='$aclass $active' href='".BASEDIR."profile.php?lookup=".$userdata['user_id']."'><span class='$spanclass'>".$v['link_name']."</a>"; // list
				} elseif ($v['link_class'] == "atomcp") { 
				$html .= "<li class='$liclass'><a id='$menu_id' class='".$v['link_class']."'><span class='$spanclass'>".$v['link_name']."</a>"; // list
				
				} elseif ($v['link_class'] == "admin") {
				$html .= "<li class='$liclass'><a id='$menu_id' class='$aclass $active' href='".ADMIN."index.php".$aidlink."'><span class='$spanclass'>Admin cPanel</span></a>"; // list
				} else {
				$html .= "<li class='$liclass'><a id='$menu_id' class='$aclass $active' href='".BASEDIR.$v['link_url']."'><span class='$spanclass'>".$v['link_name']."</span></a>"; // list
			    }
			    
			    if (array_key_exists('children', $v)){
			    
					// sub <ul> Class assignment <! Imprtant: Do not Remove >
			    	if ($v['link_cat'] == "0") { $ident = 1; } else { $ident = 2; }
				   	 	 
			      $html .= build_menu($v['children'], $ident);
				    }  
			    $html .= "</li> ".$seperator."";
		
	 } // End of Link Visibility

	} else {

	// Show also Link Visibility 1 item.
	if (($userdata['user_level'] >= $v['link_visibility']) || ($v['link_visibility'] <= "1")) {
		// ... show

		
				// new Link class introduced ID Assignment for Jquery call
				if ($v['link_class'] !=="") { $menu_id = $v['link_class']; } else { $menu_id = $v['link_id']; }
				
				// Atom Theming
			  	if ($v['link_cat'] !=="0") {
			  	$liclass = ""; $aclass=""; $active=""; $spanclass = ""; 	
				if ($count_array == $i) { $seperator = ""; } else { $seperator = "<li class='hd'></li>";  $i++; 	}
			  	} else {
			  	if ($v['link_url'] == "index.php") { $uri = $settings['opening_page']; } else { $uri = $v['link_url']; } // Set new var 
			  	if (START_PAGE == $uri) { $active = "mega-active"; $spanclass = "active-span"; } else { $active=""; $spanclass = "down"; } // top class 
			  	// Define Elements
			  	$liclass = "top"; $aclass = "top_link"; $seperator = "<li class='vd'></li>"; // top class definition including seperators
			  	}
				if ($v['link_class'] == "login2") {
					// Sourced from User Info Panel.. I still don't understand why code like this when you can use POST.
					if (!preg_match('/login.php/i',FUSION_SELF)) {
					$action_url = FUSION_SELF.(FUSION_QUERY ? "?".FUSION_QUERY : "");
					if (isset($_GET['redirect']) && strstr($_GET['redirect'], "/")) {
					$action_url = cleanurl(urldecode($_GET['redirect']));
					}
				$html .="<li class='$liclass' style='width:250px; height:188px; padding-left:10px;'>\n";
				$html .="<span style='text-transform:capitalize;'>\n";
				$html .="<form name='loginform' method='post' action='".$action_url."'>\n";
				$html .="Username <br />\n<input type='text' name='user_name' class='textbox' style='width:90%' /><br />\n";
				$html .="Password <br />\n<input type='password' name='user_pass' class='textbox' style='width:90%' /><br />\n";
				$html .="<label><input type='checkbox' name='remember_me' value='y' title='Stay logged in' style='vertical-align:middle;' /><span style='font-size:12px; margin-left:5px;'>Stay logged in</span></label>\n";
				$html .="<input type='submit' name='login' value='Login' class='button' /><br />\n";
				$html .="</form>\n<br />\n";
				$html .="</li>\n";
				$html .="<li class='hd' style='width:250px;'></li>";
				if ($settings['enable_registration']) {
				$html .="<li class='$liclass' style='width:250px; padding-left:10px; text-transform:none'>\n";
				$html .="<a class='$aclass $active' href='".BASEDIR."register.php'>\n";
				$html .="Join us today for FREE";
				$html .="</a>\n";
				$html .="</li>\n";
				$html .="<li class='hd' style='width:250px;'></li>";
				}
						
				$html .="<li class='$liclass' style='width:250px; padding-left:10px; text-transform:none'>\n";
				$html .="<a class='$aclass $active' href='".BASEDIR."lostpassword.php'>\n";
				$html .="Forgot your password?";
				$html .="</li>\n";
				$html .="</li>\n";
				
				}
				
				
				} else { 
				$html .= "<li class='$liclass'><a id='$menu_id' class='$aclass $active' href='".BASEDIR.$v['link_url']."'><span class='$spanclass'>".$v['link_name']."</span></a>"; // list
			    }
			    
			    if (array_key_exists('children', $v)){
			    
					// sub <ul> Class assignment <! Imprtant: Do not Remove >
			    	if ($v['link_cat'] == "0") { $ident = 1; } else { $ident = 2; }
				   	 	 
			      $html .= build_menu($v['children'], $ident);
				    }  
			    $html .= "</li> ".$seperator."";
		
	 	} // End of Link Visibility
		}	// End iMember
	

  }
	
	//Search based on the website area you are on
	$html .= '</ul>';
  	return $html;

} // Recursive Hierarchy for Megamenu System ## Searchbox Error Unsolved yet


function megamenu() {
global $aidlink, $locale, $settings, $main_style;
	
add_to_head("<link rel='stylesheet' type='text/css' href='".THEME."menu/menu.css' />");
add_to_footer("<script type='javascript'>	
	stuHover = function() {
	var cssRule;
	var newSelector;
	for (var i = 0; i < document.styleSheets.length; i++)
		for (var x = 0; x < document.styleSheets[i].rules.length ; x++)
			{
			cssRule = document.styleSheets[i].rules[x];
			if (cssRule.selectorText.indexOf('LI:hover') != -1)
			{
				 newSelector = cssRule.selectorText.replace(/LI:hover/gi, 'LI.iehover');
				document.styleSheets[i].addRule(newSelector , cssRule.style.cssText);
			}
		}
	var getElm = document.getElementById('nav').getElementsByTagName('LI');
	for (var i=0; i<getElm.length; i++) {
		getElm[i].onmouseover=function() {
			this.className+=' iehover';
		}
		getElm[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(' iehover\\b'), '');
		}
	}
}
if (window.attachEvent) window.attachEvent('onload', stuHover);
</script>");
	
	
echo build_menu(menu_hierarchy(), 0);
	
} // Output Template for Megamenu System





?>