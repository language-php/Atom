<?php

/*==============================================================
| Atom Theme Framework / PHP-Fusion 
| Version 1.01 alpha
| Lead Designer & Coordinator: Fred.Chan (Hien)
| www.phpfusion.me - Coded for PHP-Fusion under AGPL2
| PHP-Fusion Version 8.01 
================================================================*/
define("TEMPLATES_DIR", THEMES."templates/");
define("THEME_INCLUDES_DIR", TEMPLATES_DIR."theme_includes/");
//require_once INCLUDES."theme_functions_include.php";
## Proof of PHP-Fusion dependencies
require_once THEME_INCLUDES_DIR."simpledom/simpledom.php";


function global_styles() {
add_to_head("<style>
label, select, body { 
color: rgb(55, 55, 55);
font-size: 13px !important;
font-family: 'Myriad Pro',Calibri,Helvetica,Arial,sans-serif !important;
}

ul.nav-tabs li { font-family: 'Myriad Pro',Calibri,Helvetica,Arial,sans-serif !important; font-size:14px !important; outline:0 !important; }
</style>
");
}

/*--------------------------------------------------
| Megamenu
| http://www.phpfusion.me
|---------------------------------------------------
| Description: 
| Hierarchy Menu Version 2 Using Bootstrap
|----------------------------------------------------
| Original Work: Fred.Chan (hien)  
|----------------------------------------------------*/

// Define Sitelinks
define ("DB_SITELINKS", DB_PREFIX."site_links");


function menu_hierarchy($master_cats) {
$refs = array();
$list = array();

$result = dbquery("SELECT * FROM ".DB_SITELINKS." WHERE link_master='$master_cats' ORDER BY link_order ASC"); 
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
function build_megamenu($arr,$indent,$cat) { 
global $settings, $userdata, $aidlink, $locale;


/*   // Searchbox
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
//	$searchbox .="</form></div>"; */
$html = "";

if (!isset($indent)) { $indent = 0; }
  
  if ($indent >= "1") {
  $html .= "<ul class='dropdown-menu dropdown-menu-".$cat."' role='menu' aria-labelledby='dropdownMenu'>\n";
  } 
  else  {
  $html .= "<ul class='nav nav-".$cat."'>\n";
  }
 
  
   $count_array = count($arr); $i =1;
   foreach ($arr as $v) {

	
	// Hides link_visibility 1 from Member because that is only for Guest to See. iAdmin is solved by first condition.
	// Lets say visible is 100, and userdata is 103
	if (($userdata['user_level'] >= $v['link_visibility'])) {
		// ... show
		
				// new Link class introduced ID Assignment for Jquery call
				//if ($v['link_class'] !=="") { $menu_id = $v['link_class']; } else { $menu_id = $v['link_id']; }
				
				if ($v['link_cat'] !=="0") { 
					$a_class = ""; 
					$d_toggle = "";
					$active_class = "";
                                        $caret = "";
                                        $li_class = "";
                                        
					## PREPEND MENU CLASS TO CHILDREN CONTENT
					if (array_key_exists('children', $v)) { 
					$li_class = "dropdown-submenu";  $a_class = ""; $d_toggle = ""; $caret = ""; }
					else { $li_class = ""; }
			  		
			  		
			  		## SEPERATOR
					if ($v['link_class'] == "--") { $divider_class = "divider"; } else { $divider_class= ""; }
			  	
			  	} else {
                                        
                                        if (isset($v['link_url']) && ($v['link_url'] !=="")) {
			  		if ($v['link_url'] == "index.php") { $uri = $settings['opening_page']; }
                                        elseif ($v['link_url'] == "profile.php") { 
			  		$uri = "profile.php?lookup=".$userdata['user_id']."";
			  		} else { $uri = $v['link_url']; } // Set new var 
                                        } else { $uri = "none"; }
					## SELF_URL CHECKS
			  		if (START_PAGE == "$uri") { $active_class = "active"; 	} else { $active_class = ""; } 
					
					## PREPEND MENU CLASS TO CHILDREN CONTENT
					if (array_key_exists('children', $v)){ 
			  			$a_class="class='dropdown-toggle'"; $d_toggle = "data-toggle='dropdown'"; $caret = "<b class='caret'></b>"; $li_class = "dropdown"; } 
					else { $a_class = ""; $d_toggle=""; $caret = ""; $li_class = ""; }
					
					## SEPERATOR
					if ($v['link_class'] == "--") { $divider_class = "divider-vertical"; } else { $divider_class = ""; }
					
			  	}
				
                                # Render Output Section link_class
			  	## PLEASE NOTE: ACTIVE STATE REQUIRES $V['LINK_URL']. IT CANNOT BE BLANK IF YOU WANT ACTIVE STATE.
				
                                if ($v['link_class'] == "--") { 
				$html .= "<li class='".$divider_class." li-divider'>"; // divider - sub or main
				}
				elseif (($v['link_class'] == "subtitle") && ($indent >0)) { 
				$html .= "<li class='nav-header li-nav-header'>".$v['link_name']."</a>"; // sub header (SQL: link_class=title)
				}
                                
                                elseif (($v['link_class'] == "avatar")  && ($indent=="0")) {
                                
                                $html .= "<li class='dropdown li-avatar'><a href='#'  class='dropdown-toggle' data-toggle='dropdown'><i class='pictogram-user pictogram-medium'></i></a>";    
                                
                                    ## Old User Control Panel Goes Here
                                    if (iMEMBER) {
                                    $html .="<ul class='dropdown-menu' role='menu' aria-labelledby='dropdownMenu'>";
                                    $html .="<li><a href='".BASEDIR."profile.php?lookup=".$userdata['user_id']."'><i class='icon-info-sign'></i> Profile</a></li>\n";
                                    $html .="<li><a href='".BASEDIR."members.php'><i class='pictogram-users'></i> Members Directory</a></li>\n";
                                    if (iADMIN) {
                                    $html .="<li class='divider'>\n</li>\n";
                                    $html .="<li class='nav-header'>Administration</li>\n";
                                    $html .="<li><a href='".BASEDIR."administration/index.php".$aidlink."'><i class='pictogram-keyboard'></i> Admin cPanel</a></li>\n";
                                    $html .="<li class='divider'>\n</li>\n";
                                    }
                                    $html .="<li><a href='".BASEDIR."'><i class='pictogram-rocket'></i> Upgrade to Developer</a></li>\n";
                                    $html .="<li><a href='".BASEDIR."edit_profile.php'><i class='pictogram-feather'></i> Account Settings</a></li>\n";
                                    $html .="<li class='divider'>\n</li>\n";
                                    $html .="<li><a href='".BASEDIR."index.php?logout=yes'><i class='pictogram-key'></i> Logout</a></li>\n";
                                    $html .="</ul>\n";
                                    }
                                
                                }
                                
                                elseif (($v['link_class'] == "pm") && ($indent== "0")) {
                                // Later to include text-doc for unread notification
                                $html .= "<li class='".$li_class." ".$active_class." li-pm'><a class='tip' data-toggle='tooltip' data-original-title='You have no unread notification' data-placement='bottom' href='".BASEDIR.$v['link_url']."'><i class='pictogram-doc pictogram-medium'></i></a>";        
                                }
                                elseif (($v['link_class'] == "login") && ($indent=="0")) {
                                    
                                    ## LOGIN MENU
                                    if (iMEMBER) {
                                    $html .= "<li class='".$li_class." ".$active_class."'><a href='".BASEDIR."index.php?logout=yes' ".$a_class." ".$d_toggle." ><i class='pictogram-logout pictogram-medium'></i> Sign Out</a>";
                                    } else { 
                                    $html .= "<li class='dropdown'><a href='".BASEDIR."login.php' class='dropdown-toggle' data-toggle='dropdown'><i class='pictogram-login pictogram-medium'></i> Sign In</a>";
                                   
									if (!preg_match('/login.php/i',FUSION_SELF)) {
                                            
                                        $action_url = FUSION_SELF.(FUSION_QUERY ? "?".FUSION_QUERY : "");
                                            
                                        if (isset($_GET['redirect']) && strstr($_GET['redirect'], "/")) {
                                            $action_url = cleanurl(urldecode($_GET['redirect']));
                                        }
                                        
                                        $html .="<ul class='dropdown-menu' role='menu' aria-labelledby='dropdownMenu'>";
                                        
                                        $html .="<li style='width:250px; height:200px; padding:15px; padding-bottom:0px;'>\n";
                                        
                                        $html .= "<h4>Sign in with your ID</h4>";
                                        
                                        $html .="<form name='loginform' method='post' action='".$action_url."'>\n";
        
										$html .="<div class='control-group'>\n";
										//$html .="<strong>Username ID:</strong>";			
										$html .="<div class='controls' style='margin-top:5px;'><div class='input-prepend'><span class='add-on'><i class='icon-user'></i></span><input type='text' name='user_name' placeholder='User Name'/></div>\n";
										$html .="</div>\n</div>\n";	
	
   										$html .="<div class='control-group'>\n";
										//$html .="<strong>Password:</strong>";			
										$html .="<div class='controls' style='margin-top:5px;'><div class='input-prepend'><span class='add-on'><i class='icon-lock'></i></span><input type='password' name='user_pass' placeholder='Password'/></div>\n";
										$html .="</div>\n</div>\n";	                                     
                                        
                                        $html .="<label><input type='checkbox' name='remember_me' value='y' title='Stay logged in' style='vertical-align:middle;' /><span style='font-size:12px; margin-left:5px;'>Stay logged in</span></label>\n";
                                        

                                        $html .="<button type='submit' class='btn btn-primary pull-right' name='login' />Sign In</button>\n";

                                        if ($settings['enable_registration']) {
                                        $html .="<a class='btn pull-right' href='".BASEDIR."register.php' style='margin-right:10px !important;'/>Register</a>\n";
                                        }
                                        
                                        $html .="</form>\n";
                                        $html .="</li>\n";
                                        $html .="<li class='divider'>\n</li>\n";
   
                                        $html .="<li>";
                                        $html .="<a href='".BASEDIR."lostpassword.php'>Forgot ID or Password?</a>\n";
                                        $html .="</li>\n";
                                        
                                        $html .="</ul>\n";   
                                    }
                                }
                                }
                                elseif (($v['link_class'] == "acsettings") && ($indent=="0")) {
                                $html .= "<li class='".$li_class." ".$active_class."'><a href='".BASEDIR.$v['link_url']."' ".$a_class." ".$d_toggle." ><i class='pictogram-tools pictogram-medium'></i></a>";     
                                }
                            
                                elseif (($v['link_class'] == "search") && ($indent=="0")) {
                                //if ($v['link_order'] !=="1") { $prenav = "</li></ul>\n"; } else { $prenav = ""; }
                               //html .=$prenav;
                                $html .="<li>\n";
                                $html .="<form class='navbar-search' action=''>\n";
                                $html .="<input class='search-query span2' type='text' style='width:200px; margin-bottom:0px;' placeholder='Search ".$settings['sitename']."'>\n";
                                $html .="</form></li>";
                                //$html .="<ul class='nav'>\n";
                                $html .="<li><a class='tip' data-toggle='tooltip' data-original-title='Advanced Search' data-placement='bottom' href='".BASEDIR."search.php'><i class='pictogram-globe pictogram-medium'></i></a>\n";       
                                }
				
                                else {
				$html .= "<li class='".$li_class." ".$active_class."'><a href='".BASEDIR.$v['link_url']."' ".$a_class." ".$d_toggle." >".$v['link_name']." ".$caret."</a>"; // list
                                }
			    
			    if (array_key_exists('children', $v)){
			    
					// sub <ul> Class assignment <! Imprtant: Do not Remove >
			    	if ($v['link_cat'] == "0") { $indent = 1; } else { $indent = 2; }
				   	 	 
			      $html .= build_megamenu($v['children'], $indent, $cat);
				    }  
			    $html .= "</li>\n";
		
	 } // End of Link Visibility

  } ## End Foreach

	$html .= "</ul>";
  	return $html;
}
function showmenu($cat,$inverse=false,$corner=false,$title=false) {
global $aidlink, $locale, $settings, $main_style;

if (isset($inverse)) { $inverse = stripinput($inverse);}

echo "<div id='navbar-".$cat."' class='navbar ".((isset($inverse) && ($inverse == "1")) ? "navbar-inverse":"")." navbar-".$cat."'>
  			<div id='navbar-inner-".$cat."' class='navbar-inner navbar-inner-".$cat."' style='".((isset($corner) && ($corner == "1")) ? "border-radius:0px;":"")."'>\n";
  		
                        echo "<div id='container-".$cat."' class='container container-".$cat."' style='vertical-align:middle;'>\n";
                                
				
                                echo "<a id='btn-navbar-".$cat."' class='btn btn-navbar btn-navbar-".$cat."' data-toggle='collapse' data-target='.menu-".$cat."'>
				<span class='icon-bar'></span>
				<span class='icon-bar'></span>
				<span class='icon-bar'></span>
				</a>\n";
				
				
				echo "".((isset($title) && ($title !=="")) ? "<a href='".BASEDIR."index.php' class='brand brand-".$cat."'>$title</a>":"")."  				
  				<div class='nav-collapse collapse menu-".$cat."'>\n";
  				echo build_megamenu(menu_hierarchy($cat),0,$cat);
                                echo "</div>\n";
            	
                                
                        
                        echo "</div>\n";
                
echo "</div></div>\n";

/* Login Fix. Uncomment if Menu Login does not work.
$script = "
$(function() {
  // Setup drop down menu
  $('.dropdown-toggle').dropdown();
 
  // Fix input element click problem
  $('.dropdown input, .dropdown label').click(function(e) {
    e.stopPropagation();
  });
});
";
add_to_footer($script);
*/

} 

## *.B Un-tested SECTION
## Socket Function from Atom Core - Version 4.2 - will revise again with drop down for defaults
## Rarely used. Try to avoid if possible.
// if current them has news.php or articles.php in its template folder ... if needed.
function infusions_template_socket($infusions_name) {
	//$infusions_name = "news";
	$cinfusions = stripinput($infusions_name);
	define("".$cinfusions."_FILE", INFUSIONS."template/".$cinfusions.".php");
	if (FILE_EXISTS($cinfusions."_FILE")) { 
	echo $cinfusions."_FILE"; 
	} else { 
	echo INFUSIONS.$cinfusions."template/template.php"; 
	} // to revise code again for path 
}


## A. Common PHP-API SECTION
/*--------------------------------------------------
| Common PHP API
| http://www.phpfusion.me
|---------------------------------------------------
| Description: 
| Fundamental PHP Shorthand Code
|----------------------------------------------------
| Original Work: Fred.Chan (hien)  
|----------------------------------------------------*/

function construct_array($string) {
	$value = explode(",", $string);
	return $value;
	}
function deconstruct_array($string, $desconstructor) {
	$value = implode("$deconstructor", $string);
	return $value;
	}	


## B. JQUERY
function open_jquery() { 
return "<script type='text/javascript'>\n
	  	$(function() {";
}
function close_jquery() { 
return "});
	</script>";
}

## B1. BOOTSTRAP DOM
/*--------------------------------------------------
| BOOTSTRAP DOM by Fred.Chan (Hien)
| http://www.phpfusion.me
|---------------------------------------------------
| Description: 
| Fundamental HTML elements styled and enhanced with 
| extensible classes.
|----------------------------------------------------
| Original Work: ivaynberg 
| Github: http://ivaynberg.github.io/select2/ 
|----------------------------------------------------*/

// Load Bootstrap
function load_bootstrap() { 

add_to_head("<link href='".THEME_INCLUDES_DIR."bootstrap/css/bootstrap.min.css' rel='stylesheet' />");
add_to_head("<link href='".THEME_INCLUDES_DIR."bootstrap/css/bootstrap-datetimepicker.min.css' rel='stylesheet' />");
add_to_head("<link href='".THEME_INCLUDES_DIR."bootstrap/css/bootstrap-responsive.min.css' rel='stylesheet' />");
add_to_head("<script src='".THEME_INCLUDES_DIR."bootstrap/js/bootstrap.min.js'></script>");
//add_to_head("<script src='".THEME_INCLUDES_DIR."bootstrap/js/bootstrap.js'></script>");
add_to_head("<script src='".THEME_INCLUDES_DIR."bootstrap/js/bootstrap-datetimepicker.min.js'></script>");
add_to_head("<link href='".THEME_INCLUDES_DIR."entypo/entypo.css' rel='stylesheet' />");

$script = open_jquery();
$script .="$('.tip').tooltip();";
$script .= close_jquery();
add_to_footer($script);

}

function load_localstorage() {
add_to_head("<script src='".THEME_INCLUDES_DIR."storage/jquery.storage.js'></script>");
}

## B1.1: PHP-Fusion 8 Responsive Layout


function load_layout() {
    
$top_class = 11;

$header_class = 12;
$showcase_class = 13;
$feature_class = 14;
$utility_class = 15;
$maintop_class = 16;
$drawer_class = 17;
$content_top_class = 18;

$sidebar1_class = 1;
$mainbody_class = 2;
$content_bottom_class = 3;
$sidebar2_class = 4;
$sidebar3_class = 19;
$sidebar4_class = 20;

$mainbottom_class = 24;
$extension_class = 25;
$bottom_class = 26;
$footer_class = 27;
$copyright_class = 28;

$p_result = dbquery("SELECT * FROM ".DB_PANELS." WHERE panel_status='1' AND panel_access<='".$userdata['user_level']."' ORDER BY panel_side, panel_order");
	while ($data = dbarray($p_result)) {
		
		$panel_id = stripinput($data['panel_id']);
		$panel_name = stripinput($data['panel_name']);
		$panel_filename = stripinput($data['panel_filename']);
		$panel_content = stripinput($data['panel_content']);
		$panel_side = stripinput($data['panel_side']);
		$panel_order= stripinput($data['panel_order']);
		$panel_type = stripinput($data['panel_type']);
		$panel_access = stripinput($data['panel_access']);
		$panel_status = stripinput($data['panel_status']);
		$panel_assigned = stripinput($data['panel_assigned']);
		$panel_excluded = stripinput($data['panel_excluded']);
		$panel_class_id = "0"; // $data['panel_class_id'];

		$current_array = array( 'panel_id'=>$panel_id, 'panel_name'=>$panel_name, 'panel_filename'=>$panel_filename, 'panel_content'=>$panel_content, 'panel_side'=>$panel_side, 'panel_order'=>$panel_order, 'panel_type'=>$panel_type, 'panel_assigned'=>$panel_assigned, 'panel_excluded'=>$panel_excluded, 'panel_class_id'=>$panel_class_id);
	
			if ($data['panel_side'] == $top_class) { $top[] = $current_array; } // push to top
			if ($data['panel_side'] == $header_class) { $header[] = $current_array;  } // push to top
			if ($data['panel_side'] == $showcase_class) { $showcase[] = $current_array; } // push to top
			if ($data['panel_side'] == $feature_class) { $feature[] = $current_array;  } // push to top
			if ($data['panel_side'] == $utility_class) { $utility[] = $current_array;  } // push to top
			if ($data['panel_side'] == $maintop_class) { $maintop[] = $current_array;  } // push to top
			if ($data['panel_side'] == $content_top_class) { $content_top[] = $current_array;  } // push to top
			if ($data['panel_side'] == $mainbody_class) { $mainbody[] = $current_array;  } // push to top
			if ($data['panel_side'] == $content_bottom_class) { $content_bottom[] = $current_array;  } // push to top
			if ($data['panel_side'] == $sidebar1_class) { $sidebar1[] = $current_array;  } // push to top
			if ($data['panel_side'] == $sidebar2_class) { $sidebar2[] = $current_array;  } // push to top
			if ($data['panel_side'] == $sidebar3_class) { $sidebar3[] = $current_array;  } // push to top
			if ($data['panel_side'] == $sidebar4_class) { $sidebar4[] = $current_array;  } // push to top
			if ($data['panel_side'] == $mainbottom_class) { $mainbottom[] = $current_array;  } // push to top
			if ($data['panel_side'] == $extension_class) { $extension[] = $current_array;  } // push to top
			if ($data['panel_side'] == $bottom_class) { $bottom[] = $current_array;  } // push to top
			if ($data['panel_side'] == $footer_class) { $footer[] = $current_array;  } // push to top
			if ($data['panel_side'] == $copyright_class) { $copyright[] = $current_array;  } // push to top
			if ($data['panel_side'] == $drawer_class) { $drawer[] = $current_array;  } // push to top
	}
	$array = array_push($value, "$top, $header, $showcase, $feature, $utility, $maintop, $content_top, $mainbody, $content_bottom, $sidebar1, $sidebar2, $sidebar3, $sidebar4, $mainbottom, 
	$extension, $bottom, $footer, $copyright, $drawer");
	return $array;
}

// Recompile to abbreivations for theme output
// ie. example $definition is $top_class, $header_class, etc
function load_containers($definition) { 
$html = "<div class='row-fluid'>\n";
	
	foreach($definition as $arr) { 
	$html .="<div class='".span_counter($definition)."' />\n";
		if (($arr['panel_assigned'] == START_PAGE) && ($arr['panel_exluded'] !== START_PAGE)) {	
			if ($arr['panel_type'] == "file") {
				if (file_exists(INFUSIONS.$arr['panel_filename']."/".$arr['panel_filename'].".php")) { $html .= include INFUSIONS.$arr['panel_filename']."/".$arr['panel_filename'].".php"; }
			} else {
				$html .= eval(stripslashes($arr['panel_content']));
			}	
		}
	$html .="</div />\n";
	}
	

$html .="</div>\n";
return $html;
}
function responsive_presets_output() {
	// layout_id, layout_title, layout_theme, layout_datestamp
	
	
}

// Complements Span calculation based on numebr of array for atom_layout only
function span_counter($definition) {
	$counter = count($definition);
	$span_class = floor(12/$counter);
	return "span-$span_class";
}


## B1.2: PHP-Fusion Tabs

// simple format
function opentab($array, $content) {

/*  Usage Method:
==========================================	
	$content1 = "<strong>This</strong>";
	$content2 = "<form><input type='text' name='$string_supported'></form>";
	$content3 = "Normal Text";	
	$content = construct_array("$content1,$content2,$content3");
	$title = construct_array("Table1,Table2,Table3");
	$id = "news_admin";
	echo opentab("news_admin", $title, $content);	*/


	$html = "<div>";
	$html .= "<ul class='nav nav-tabs'>\n";
	foreach ($array as $arr=>$v) {
	if ($arr==0) { $html .= "<li class='active'><a data-toggle='tab' href='#".$v."'>".$v."</a></li>\n";} else {
		$html .= "<li><a data-toggle='tab' href='#".$v."'>".$v."</a></li>\n"; 		
		}
	}
	$html .= "</ul>\n";
	
	$html .="<div class='tab-content'>\n";
	foreach ($array as $arr=>$v) {
		if ($arr==0) { $html .="<div class='tab-pane active' id='".$v."'>".$content[$arr]."</div>\n"; } else {
		$html .="<div class='tab-pane' id='".$v."'>".$content[$arr]."</div>\n";			
		}
	}
	$html .="</div>\n";
	$html .="</div>\n";
	
	## No need to use this because we're using data-toggle.
	//	$html .="
	//	<script type='text/javascript'>
	//	  jQuery(document).ready(function ($) {
	//	$('#".$id."').tab();	
	//	})
	//	</script>
	//	";
	return $html;
	}
// Advanced Format opentab function
function opentab_title($titles, $link_active) { 
	$html = "<div>\n";
	$html .= "<ul class='nav nav-tabs'>\n";
	foreach ($titles as $arr=>$v) {
	if ($arr==$link_active) {  
	//$html .= "<li><a data-toggle='tab' href='#".$v."'>".$v."</a></li>\n";
	$html .= "<li class='active'><a data-toggle='tab' href='#".$v."'>".$v."</a></li>\n";
	
	} else {
		$html .= "<li><a data-toggle='tab' href='#".$v."'>".$v."</a></li>\n"; 		
		}
	}
	$html .= "</ul>\n";
	$html .="<div class='tab-content'>\n";
	return $html;
}
function opentab_body($titles, $key, $is_active=false) { 
        if (isset($is_active) && ($is_active == "1")) { $status = "active"; } else { $status = ""; }
        $id = $titles[$key];
	return "<div class='tab-pane ".$status."' id='".$id."'>\n";
}	
function opentab_closebody() { return "</div>\n"; }
function opentab_close() { return "</div>\n</div>\n"; }

function persist_tab() { 
add_to_footer ("
<script type='text/javascript'>
$(function() { 
  $('a[data-toggle='tab']').on('shown', function (e) {
    //save the latest tab; use cookies if you like 'em better:
    localStorage.setItem('lastTab', $(e.target).attr('id'));
  });

  //go to the latest tab, if it exists:
  var lastTab = localStorage.getItem('lastTab');
  if (lastTab) {
      $('#'+lastTab).tab('show');
  }
});
</script>
");
}


## B2. OTHER JQUERY DOM

/*--------------------------------------------------
| SELECT 2 DOM by Fred.Chan (Hien)
| http://www.phpfusion.me
|---------------------------------------------------
| Description: 
| Very nice drop down and tagging solution
|----------------------------------------------------
| Original Work: ivaynberg 
| Github: http://ivaynberg.github.io/select2/ 
|----------------------------------------------------*/

// Load the Select into PHP-Fusion
 function load_select2() { 
add_to_head("<script src='".THEME_INCLUDES_DIR."select2/select2.js'></script>");
add_to_head("<link href='".THEME_INCLUDES_DIR."select2/select2.css' rel='stylesheet' />");

}

/* Select2 Tag
Usage: 	select2_tags_DOM("input_field_id", construct_array("tag1,tag2,tag3"), construct_array($_POST['news_tag']));
Description:
$id = the unique ID of the input DOM element
$default_array = pre-populate a tag <option> drop down with a list of array
$init_array = pre-populate the field on load. Usually used for edit, preview, etc. */

function select2_tags_DOM($id, $default_array, $init_array) {
	
	if ($default_array['0'] !=="") { $start=1; } 
	if (isset($start)) { 
	if (is_array($default_array)) { 
	$default_tags = "tags:[";
	if (($default_array !=="") && (is_array($default_array))) { 
	foreach($default_array as $arr=>$v) { 
	if ($arr !== 0) { $default_tags .=", '$v'"; } else {  $default_tags .="'$v'"; }
	}
	} else { $default_tags .="'tag'"; } 
	$default_tags .= "],";
	}
	} else { $default_tags = ""; } 
	
	if ($init_array['0'] !=="") { $init=1; } 
	if (isset($init)) { 
	if (is_array($init_array)) { 
	$val = "var preload_tag = [";   
		foreach($init_array as $arr=>$v) { 
			$val .= "{ id: '$v', text: '$v' },";
			}
	$val .= "];
		$('#".$id."').select2('data', preload_tag );
	";
	} 
	} else { $val = ""; }
$html = "$('#".$id."').select2({".$default_tags." placeholder: 'Type in tags', allowClear:true });";
$html .= "$val";
return $html;
}

/* Select2 Dropdown */
function select2_dropdown_DOM($id) {
return "$('#".$id."').select2(); ";
}

/*--------------------------------------------------
| HTML5 Editor DOM by Fred.Chan (Hien)
| http://www.phpfusion.me
|---------------------------------------------------
| Description: 
| HTML5 Editor for Bootstrap (Textarea)
|----------------------------------------------------
| Original Work: J Holling Worth  <- a shit editor
| Github: https://github.com/jhollingworth/bootstrap-wysihtml5/
|----------------------------------------------------*/

// Load the Editor into PHP-Fusion
function load_editor() { 
add_to_head("<link href='".THEME_INCLUDES_DIR."bootstrap/css/bootstrap-wysihtml5-0.0.2.css' rel='stylesheet' />");
add_to_head("<script src='".THEME_INCLUDES_DIR."bootstrap/js/wysihtml5-0.3.0.min.js'></script>");
add_to_head("<script src='".THEME_INCLUDES_DIR."bootstrap/js/bootstrap-wysihtml5-0.0.2.min.js'></script>");
}
/* HTML 5 Editor for Textarea 
Usage: html5_editor_DOM("Unique ID of your textarea" [text], textarea value [$string]);
Example : html5_editor("newsCats", $data['news_name']); 
Note: to get html value use .val instead of .setvalue
*/
function html5_editor_DOM($id, $val) { 
## NOTE: DO NOT sanitize $val. Pre-sanitize $val before going into the function	because this is Jquery
//	if (strlen($val) > 0) {
	$html ="$('#".$id."').wysihtml5({'html':true})";
	
//	.editor.setValue('".$val."');";
	
//	.val($(this).val()
	
//	} else { 
//	$html ="$('#".$id."').wysihtml5({'html':true});";
//	}

return $html;

}


/*--------------------------------------------------
| CK Editor DOM by Fred.Chan (Hien) - NEXT PROJECT AS INFUSION
| http://www.phpfusion.me
|---------------------------------------------------
| Description: 
| CK Editor - Will be made into infusion later. 
|----------------------------------------------------
| Original Work: CK Editor
| http://www.ckeditor.com
|----------------------------------------------------*/

function load_ckeditor() { 
add_to_head("<script src='".THEME_INCLUDES_DIR."ckeditor/ckeditor.js'></script>");
}

function ckeditor_basic_DOM($id) { 
	return "<script>CKEDITOR.replace( '".$id."', {
	toolbar :[
	{ name: 'format', items: [ 'Bold', 'Italic', 'Underline' , 'Strike' , 'Subscript' , 'Superscript' , 'RemoveFormat' ] },
	{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', 'Outdent', 'Indent', 'Blockquote', 'CreateDiv', 'JustifyLeft', 'JustifyRight', 'JustifyBlock', 'BidiLtr', 'BidiRtl' ] }
	{ name: 'link', items: [ 'Link', 'Unlink' ] }
	{ name: 'social', items: [ 'Smiley' ] }
	]
	});</script>";
}


function ckeditor_standard_DOM($id) { 
	return "<script>CKEDITOR.replace( '".$id."', {
	toolbar: [
		{ name: 'document', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },	
		[ ' SCYAT ' ], 	{ name: 'link', items: [ 'Link', 'Unlink' , 'Anchor' ] }, 
		{ name: 'media', items: [ 'Image', 'Table' , 'HorizontalRule', 'InsertPre' ] }, [ 'Source' ],
		'/',																					
		{ name: 'format', items: [ 'Bold', 'Italic', 'Underline' , 'Strike' , 'Subscript' , 'Superscript' , 'RemoveFormat' ] },
		{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', 'Outdent', 'Indent', 'Blockquote', 'CreateDiv', 'JustifyLeft', 'JustifyRight', 'JustifyBlock', 'BidiLtr', 'BidiRtl' ] },
		[ 'Styles' ], [ 'Format' ]
	]
	});</script>";
}

function ckeditor_full_DOM($id) { 
	return "<script>CKEDITOR.replace( '".$id."' );</script>";
}



/*--------------------------------------------------
| Datepicker DOM by Fred.Chan (Hien)
| http://www.phpfusion.me
|---------------------------------------------------
| Description: 
| Datepicker for Bootstrap 
|----------------------------------------------------
| Original Work: Stefan Petre
| Github: http://www.eyecon.ro/bootstrap-datepicker/
|----------------------------------------------------*/	

function datepicker_DOM($id) { 
 $html = "$('#".$id."').datetimepicker({ language: 'en', pick12HourFormat: true});";

 return $html;
}

/*--------------------------------------------------
| Dynamic Table Row Adder / Remover (Billing)
| http://www.phpfusion.me
| Original Work: Firehub.com (Commercial Project)
----------------------------------------------------*/
/* Usage:

Callback:
$rv = array();
foreach($_POST['col1'] as $k => $v) {
$rv[] = array($v, $_POST['col2'][$k], $_POST['col3'][$k]);
}

<table id='invoice'>
<tr><td><input type='checkbox' name='chk'></td><td><input type='text' name='input_field[]' value='$input_field'></td></tr>
</table>
<input type="button" value="Add Row" onclick="addRow(\'invoice\')"/><input type="button"" value="Delete Row" onclick="deleteRow(\'invoice\')"/>

*/
function load_dynamic_table() {
$html = '
		<script language="javascript">
		function addRow(tableID) {

			var table = document.getElementById(tableID);
			var rowCount = table.rows.length;
			var row = table.insertRow(rowCount);
			var colCount = table.rows[0].cells.length;
			for(var i=0; i<colCount; i++) {
				var newcell	= row.insertCell(i);
				newcell.innerHTML = table.rows[0].cells[i].innerHTML;
				//alert(newcell.childNodes);
				switch(newcell.childNodes[0].type) {
					case "text":
							newcell.childNodes[0].value = "";
							break;
					case "checkbox":
							newcell.childNodes[0].checked = false;
							break;
					case "select-one":
							newcell.childNodes[0].selectedIndex = 0;
							break;
				}
			}
		}

		function deleteRow(tableID) {
			try {
			var table = document.getElementById(tableID);
			var rowCount = table.rows.length;
			for(var i=0; i<rowCount; i++) {
				var row = table.rows[i];
				var chkbox = row.cells[0].childNodes[0];
				if(null != chkbox && true == chkbox.checked) {
					if(rowCount <= 1) {
						alert("Cannot delete all the rows.");
						break;
					}
					table.deleteRow(i);
					rowCount--;
					i--;
				}
			}
			}catch(e) {
				alert(e);
			}
		}
	</SCRIPT>';
return $html;
}

/*--------------------------------------------------
| Bootstrap Toggle Button as Form Inputs
| http://www.phpfusion.me
| Original Work: Hien 
| Dev Notes: not sure why input type will not parse into browser even after refresh. cloudflare?
----------------------------------------------------*/

function btn_toggle($label, $field_name, $option_array, $post) {
	$html ="<label>".$label."</label>\n";
	$html .="<div class='btn-group' id='".$field_name."_master' data-toggle='buttons-radio' />\n";
		$i ="0"; // default
		foreach($option_array as $arr=>$v) {
		if (($post == "$arr")) { $btn_active = "active"; } else {$btn_active = ""; }
		$html .= "<button type='button' class='btn ".$btn_active."' data-toggle='button' value='$arr'/>$v</button>\n";
		
		}
	$html .="<input type='text' style='display:none;' id='$field_name' class='field' name='$field_name' value='' />";
	$html .="</div>\n";


	$html .= open_jquery();
	$html .="$('#".$field_name."_master .btn').click(function() {";
	$html .="$('#".$field_name."_master .field').val($(this).val());";
	$html .="});";
	$html .= close_jquery();
	return $html;
}
	


?>