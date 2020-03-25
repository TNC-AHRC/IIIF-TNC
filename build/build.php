<?php

$site = getRemoteJsonDetails("site.json", false, true);
if (!is_array($site) or count($site) < 1)
	{exit("\nERROR: Sorry your site.json file has not been opened correctly please check you json formatting and try vaildating it using a web-site similar to https://jsonlint.com/\n\n");}
$pages = getRemoteJsonDetails("pages.json", false, true);
if (!is_array($pages) or count($pages) < 1)
	{exit("\nERROR: Sorry your pages.json file has not been opened correctly please check you json formatting and try vaildating it using a web-site similar to https://jsonlint.com/\n\n");}
$raw_subpages = getRemoteJsonDetails("sub-pages.json", false, true);
if (!is_array($raw_subpages) or count($raw_subpages) < 1)
	{exit("\nERROR: Sorry your sub-pages.json file has not been opened correctly please check you json formatting and try vaildating it using a web-site similar to https://jsonlint.com/\n\n");}

$menuList = array();
$subpages = array();
$bcs = array();

$defaults = array(
	"metaDescription" => "The National Gallery, London, ".
		"Scientific Department, is involved with research within a wide ".
		"range of fields, this page presents an example of some of the ".
		"work carried out.",
	"metaKeywords" => "The National Gallery, London, ".
		"National Gallery London, Scientific, Research, Heritage, Culture",
	"metaAuthor" => "Joseph Padfield| joseph.padfield@ng-london.org.uk |".
			"National Gallery | London UK | website@ng-london.org.uk |".
			" www.nationalgallery.org.uk",
	"metaTitle" => "NG Test Page",
	"metaFavIcon" => "https://www.nationalgallery.org.uk/custom/ng/img/icons/favicon.ico",
	"extra_js_scripts" => array(), 
	"extra_css_scripts" => array(),
	"extra_css" => "",
	"extra_js" => "",
	"logo_link" => "",
	"logo_path" => "graphics/ng-logo-white-100x40.png",
	"logo_style" => "",
	"extra_onload" => "",
	"topNavbar" => "",
	"body" => "",
	"fluid" => false,
	"offcanvas" => false,
	"footer" => "&copy; The National Gallery 2020</p>",
	"footer2" => false,
	"licence" => false,
	"extra_logos" => array(),
	"breadcrumbs" => false
	);
				
$gdp = array_merge ($defaults, $site);   

$html_path = "../docs/";
		
buildExamplePages ();	
	
function prg($exit=false, $alt=false, $noecho=false)
	{
	if ($alt === false) {$out = $GLOBALS;}
	
	else {$out = $alt;}
	
	ob_start();
	//echo "<pre class=\"wrap\">";
	if (is_object($out))
		{var_dump($out);}
	else
		{print_r ($out);}
	echo "\n";//</pre>";
	$out = ob_get_contents();
	ob_end_clean(); // Don't send output to client
  
	if (!$noecho) {echo $out;}
		
	if ($exit) {exit;}
	else {return ($out);}
	}
	

function getRemoteJsonDetails ($uri, $format=false, $decode=false)
	{if ($format) {$uri = $uri.".".$format;}
	 $fc = file_get_contents($uri);
	 if ($decode)
		{$output = json_decode($fc, true);}
	 else
		{$output = $fc;}
	 return ($output);}

$fcount = 1;

function countFootNotes($matches) {
  global $fcount;
  $out = '<sup><a id="ref'.$fcount.'" href="#section'.$fcount.'">['.$fcount.']</a></sup>';
  $fcount++;
  return($out);
}

function addLinks($matches) {
  $out = "<a href='$matches[0]'>$matches[0]</a>";
  return($out);
}

function parseFootNotes ($text, $footnotes, $sno=1)
	{
	global $fcount;
	$fcount = $sno;
	
	$text = preg_replace_callback('/\[[@][@]\]/', 'countFootNotes', $text);
	$text = $text . "<div class=\"foonote\"><ul>";
	foreach ($footnotes as $j => $str)
		{$k = $j + 1;
		 $str = preg_replace_callback('/http[^\s]+/', 'addLinks', $str);
		 $text = $text."<li id=\"section${k}\"><a href=\"#ref${k}\">[${k}]</a> $str</li>";}
	
	$text = $text . "</ul></div>";
	
	return ($text);	
	}		


function buildSimpleBSGrid ($bdDetails = array())
		{
		ob_start();
		
		if (isset($bdDetails["topjumbotron"]))
			{echo "<div class=\"jumbotron\">".$bdDetails["topjumbotron"].
				"</div>";}
		
		if (isset($bdDetails["rows"])) 
			{
			foreach ($bdDetails["rows"] as $k => $row)
				{
				echo "<div class=\"row\">";	
				
				foreach ($row as $j => $col)
					{if (!isset($col["class"])) {$col["class"] ="col-6 col-lg-4";}
					 if (!isset($col["content"])) {$col["content"] ="Default Text";}
					 echo "<div class=\"$col[class]\">".$col["content"]."</div><!--/span-->";}
				
				echo "</div><!--/row-->    ";
				}
			}
		
		if (isset($bdDetails["bottomjumbotron"]) and $bdDetails["bottomjumbotron"])
			{echo "<div class=\"jumbotron\">".$bdDetails["bottomjumbotron"].
				"</div>";}
		else
			{echo "<br/>";}
		
		$html = ob_get_contents();
		ob_end_clean(); // Don't send output to client		
		
		return($html);
		}

function loopMenus ($str, $key, $arr, $no)
	{
	global $pages, $raw_subpages;

	$str .=
		'<!-- Dropdown Loop '.$no.' -->';
            
	$str .= '<li class="dropdown-submenu">
   <a id="dropdownMenu'.$no.'" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle" title="Click to open the '.ucfirst($key).' menu">'.ucfirst($key).'</a>
		<ul aria-labelledby="dropdownMenu'.$no.'" class="dropdown-menu border-0 shadow">'.
		'<li><a href="'.$key.'.html" class="dropdown-item  top-item" title="Click to open the '.ucfirst($key).' page">'.ucfirst($key).'</a></li>'.
		'<li class="dropdown-divider"></li>';

	foreach ($arr as $k => $a)
		{
		if (!$a)
			{$str .= '<li><a href="'.$k.'.html" class="dropdown-item">'.
				ucfirst($k).'</a></li>';}
		else
			{$str = loopMenus ($str, $k, $a, false, $no+1);}
		}

	$str .= '</ul></li><!-- End Loop '.$no.' -->'; 

	return ($str);
	}
	
function buildTopNav ($name, $bcs=false)
	{
	global $pages, $menuList;
	
	$pnames = array_keys($pages);
	$active = array("active", '<span class="sr-only">(current)</span>');
	$html = "<div class=\"collapse navbar-collapse\" id=\"navbarsExampleDefault\"><ul class=\"navbar-nav\">
";

	$no = 1;	
	
	foreach ($pnames as $pname)
		{if ($pname == "home") {$puse= "index";}
		 else {$puse = $pname;}
			 
		 if ($pname == $name) {$a = $active;}
		 else {$a = array("", "");}
		 
		 if (isset($menuList[$pname]))
			{
			$html .= '<!-- Dropdown Loop '.$no.' --><li class="nav-item dropdown '.$a[0].'">'.
				'<a id="dropdownMenu'.$no.'" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle" title="Click to open the '.ucfirst($pname).' menu" >'.ucfirst($pname).$a[1].'</a>';
			$html .= '<ul aria-labelledby="dropdownMenu'.$no.
				'" class="dropdown-menu border-0 shadow">'.'<li><a href="'.
				$puse.'.html" class="dropdown-item top-item" title="Click to open the '.ucfirst($pname).' page">'.ucfirst($pname).'</a></li>'.
				'<li class="dropdown-divider"></li>';
			foreach ($menuList[$pname] as $k => $a)
				{
				if (!$a)
					{$html .= '<li><a href="'.$k.'.html" class="dropdown-item">'.ucfirst($k).'</a></li>';}
				else
					{$html = loopMenus ($html, $k, $a, $no+1);}
				}

			$html .= '</ul></li><!-- End Loop '.$no.' -->'; 	
			}
		 else
			{$html .= '<li class="nav-item '.$a[0].'"><a class="nav-link" href="'.
			$puse.'.html">'.ucfirst($pname).$a[1].'</a></li>';}}
	
	$html .= "</ul></div>";
	
	return($html);
	}
	
function loopBreadcrumbs ($name, $arr=array())
	{
	global $pages, $raw_subpages;
	
	$arr[] = $name;
	
	if ($name and !isset($pages[$name]) and isset($raw_subpages[$name]))
		{$arr = loopBreadcrumbs ($raw_subpages[$name]["parent"], $arr);}	
	
	return ($arr);
	}

function buildMenuList ($a)
	{
	global $menuList;
	foreach ($raw_subpages as $k => $a)
		{}
	
	}
	
function buildExamplePages ()
	{
	global $gdp, $pages, $site, $raw_subpages, $subpages, $bcs,
		$html_path, $menuList;
	
	$files = glob($html_path."*.html");
	
	foreach ($files as $file)
		{unlink ($file);}

	// add a timestamp page to mark most recent update and to force github
	// to commit at least one new file as thus not return an error.
	writeTSPage ();

	//foreach ($pages as $name => $d)
	//	{$menuList[$name] = array();}

	foreach ($raw_subpages as $k => $a)
		{$a["name"] = $k;		 
		 $a["bcs"] = array_reverse(loopBreadcrumbs ($k));
		 $tml = implode ("']['", $a["bcs"]);
		 $tml = "\$menuList['".$tml."'] = array();";
		 eval($tml);	 
		 $raw_subpages[$k] = $a;
		 $subpages[$a["parent"]][]= $a;}
	
	foreach ($raw_subpages as $k => $a)
		{writePage ($k, $a, false);}
		 
	foreach ($pages as $name => $d)
		{writePage ($name, $d, true);}
	}

function buildBreadcrumbs ($arr)
	{
	
	$html = false;
	
	// we do not need a link to the page we are on
	$ignore_last = array_pop($arr);
	
	if ($arr) {
		
		$list = "";
		foreach ($arr as $k => $v)
			{
			$V = ucfirst($v);
			$list .= "<li class=\"breadcrumb-item\"><a href=\"${v}.html\">$V</a></li>";
			}
	ob_start();			
	echo <<<END
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				$list
			</ol>
		</nav>
END;
		$html = ob_get_contents();
		ob_end_clean(); // Don't send output to client
		}
	
	return($html);
	}

function writeTSPage ()
	{
	global $html_path;
	
	$ds = date("Y-m-d H:i:s");
	$myfile = fopen($html_path."${ds}.html", "w");
	$html = "<h2>Last updated on: $ds</h2>";
	fwrite($myfile, $html);
	fclose($myfile);
	}
	
function writePage ($name, $d, $tnav=true)
	{
	global $subpages, $gdp, $menuList;
	
	$pd = $gdp;
		
	if ($name == "home") {$use= "index";}
	else {$use = $name;}
		
	if ($tnav)
		{$pd["topNavbar"] = buildTopNav ($name);
		 $pd["breadcrumbs"] = "";}
	else
		{$pd["topNavbar"] = buildTopNav ($d["bcs"][0]);
		 $pd["breadcrumbs"] = buildBreadcrumbs ($d["bcs"]);}
	
	$home = parseFootNotes ($d["content"], $d["footnotes"], 1);
				
	$pd["grid"] = array(
		"topjumbotron" => "<h2>$d[title]</h2>",
		"bottomjumbotron" => "",
		"rows" => array(
			array(
				array (
					"class" => "col-12 col-lg-12",
					"content" => $pd["breadcrumbs"])
				),
			array(
				array (
					"class" => "col-12 col-lg-12",
					"content" => $home)
				)));
							
	if ($d["content right"])
		{$pd["grid"]["rows"][1][0]["class"] = "col-6 col-lg-6";
		 $pd["grid"]["rows"][1][1] = 
				array (
					"class" => "col-6 col-lg-6",
					"content" => $d["content right"]);}

	// Button links replaced with nested dropdown in nav bar				
	/*if (isset($subpages[$name]))
		{
		$crows = "";
			
		foreach ($subpages[$name] as $g => $a)
			{
			ob_start();			
			echo <<<END
			<tr>
				<td>
					<a class="btn btn-outline-dark btn-block" href="$a[name].html" role="button">$a[title]</a>
				</td>
			</tr>
END;
			$crows .= ob_get_contents();
			ob_end_clean(); // Don't send output to client			
			}
			
		$pd["grid"]["rows"][] = array(array (
			"class" => "col-12 col-lg-12",	
			"content" => '<table width="100%">'.$crows.'</table></br>'));						
		}*/
					
	$pd["body"] = buildSimpleBSGrid ($pd["grid"]);
	$html = buildBootStrapNGPage ($pd);
	$myfile = fopen("../docs/${use}.html", "w");
	fwrite($myfile, $html);
	fclose($myfile);
	}

function formatSubPages ($arr)
	{
	$items = "";
	foreach ($arr as $g => $a)
		{$items .= "<a class=\"dropdown-item\" href=\"$a[name].html\">".
			"$a[title]</a>";}
		
	ob_start();			
	echo <<<END
<li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Sub Pages
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">          
          $items
        </div>
      </li>
END;
	$subpages = ob_get_contents();
	ob_end_clean(); // Don't send output to client

	return ($subpages);
	}
	
function buildBootStrapNGPage ($pageDetails=array())
	{	
	$default_scripts = array(
	"js-scripts" => array (
		"jquery" => "js/jquery-3.4.1.min.js",
		"tether" => "js/tether.min.js",
		"bootstrap" => "js/bootstrap.min.js"),
	"css-scripts" => array(
		"fontawesome" => "https://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css",
		"main" => "css/main.css",
		"bootstrap" => "css/bootstrap.min.css"));

	/* Added before
	 $defaults = array(
		"metaDescription" => "The National Gallery, London, ".
			"Scientific Department, is involved with research within a wide ".
			"range of fields, this page presents an example of some of the ".
			"work carried out.",
		"metaKeywords" => "The National Gallery, London, ".
			"National Gallery London, Scientific, Research, Heritage, Culture",
		"metaAuthor" => "Joseph Padfield| joseph.padfield@ng-london.org.uk |".
			"National Gallery | London UK | website@ng-london.org.uk |".
			" www.nationalgallery.org.uk",
		"metaTitle" => "NG Test Page",
		"metaFavIcon" => "https://www.nationalgallery.org.uk/custom/ng/img/icons/favicon.ico",
		"extra_js_scripts" => array(), 
		"extra_css_scripts" => array(),
		"extra_css" => "",
		"extra_js" => "",
		"logo_link" => "",
		"logo_path" => "graphics/ng-logo-white-100x40.png",
		"logo_style" => "",
		"extra_onload" => "",
		"topNavbar" => "",
		"body" => "",
		"fluid" => false,
		"offcanvas" => false,
		"footer" => "&copy; The National Gallery 2020</p>",
		"footer2" => false,
		"licence" => false,
		"extra_logos" => array(),
		"breadcrumbs" => false
		);
	 
	$pageDetails = array_merge($defaults, $pageDetails);//*/

	ob_start();			
	echo <<<END
$(function() {
  // ------------------------------------------------------- //
  // Multi Level dropdowns
  // ------------------------------------------------------ //
  $("ul.dropdown-menu [data-toggle='dropdown']").on("click", function(event) {
    event.preventDefault();
    event.stopPropagation();

    $(this).siblings().toggleClass("show");


    if (!$(this).next().hasClass('show')) {
      $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
    }
    $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
      $('.dropdown-submenu .show').removeClass("show");
    });

  });
});
END;
	$pageDetails["extra_onload"] .= ob_get_contents();
	ob_end_clean(); // Don't send output to client

	$pageDetails["css_scripts"] = array_merge(
		$default_scripts["css-scripts"], $pageDetails["extra_css_scripts"]);
		
	$cssScripts = "";
	foreach ($pageDetails["css_scripts"] as $k => $path)
		{$cssScripts .="
	<link href=\"$path\" rel=\"stylesheet\" type=\"text/css\">";}
	
		
	$pageDetails["js_scripts"] = array_merge(
		$default_scripts["js-scripts"], $pageDetails["extra_js_scripts"]);
		
	$jsScripts = "";
	foreach ($pageDetails["js_scripts"] as $k => $path)
		{$jsScripts .="
	<script src=\"$path\"></script>";}

	if ($pageDetails["licence"])
			{$tofu = '<div class="licence">'.$pageDetails["licence"].'</div>';}
	else
			{$tofu = '<div>This site was developed and is maintained by: 
				<a href="mailto:joseph.padfield@ng-london.org.uk" 
					title="Joseph Padfield, The National Gallery Scientific Department">Joseph Padfield</a>.
					<a href="http://www.nationalgallery.org.uk/terms-of-use">Terms of Use</a></div>';}
	
	$extra_logos = "";
	$exlno = 1;
	foreach ($pageDetails["extra_logos"] as $k => $lds)
		{
		ob_start();			
		echo <<<END
			<a href="$lds[link]/">
				<img id="ex-logo${exlno}" class="logo" title="$k" src="$lds[logo]" 
				style="$pageDetails[logo_style]" alt="$lds[alt]"/>
		  </a>
END;
		$extra_logos .= ob_get_contents();
		ob_end_clean(); // Don't send output to client
		
		$exlno++;
		}
		
	if ($pageDetails["topNavbar"])
		{
		ob_start();			
		echo <<<END
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <a class="navbar-brand"  href="$pageDetails[logo_link]">
  		<img id="page-logo" class="logo" title="Logo" src="$pageDetails[logo_path]" 
				style="$pageDetails[logo_style]" alt="The National Gallery"/>
		  </a>
			$pageDetails[topNavbar]
			
    <span class="navbar-text">
      $extra_logos
    </span>
    </nav>
END;
		$pageDetails["topNavbar"] = ob_get_contents();
		ob_end_clean(); // Don't send output to client
		}
			
	if($pageDetails["offcanvas"])
		{
		$oc = $pageDetails["offcanvas"];
		$offcanvasClass = "row-offcanvas row-offcanvas-right";
		$offcanvasToggle = "<p class=\"float-right hidden-md-up\"> ".
			"<button type=\"button\" class=\"btn btn-primary btn-sm\" ".
			"data-toggle=\"offcanvas\">{$pageDetails["offcanvas"][0]}</button>".
			"</p>";
		$sidepanel = "<div class=\"{$pageDetails["offcanvas"][2]} sidebar-offcanvas\" ".
			"id=\"{$pageDetails["offcanvas"][1]}\"><div class=\"list-group\">";
		
		$active = "active";	
		foreach ($pageDetails["offcanvas"][3] as $k => $a)
			{$sidepanel .= "<a href=\"$a[1]\" class=\"list-group-item link-extra $active\">".
				"$a[0]</a>";
			 $active = "";}
		$sidepanel .= "</div></div><!--/span-->";
		$ocw = "9";
		}
	else
		{$offcanvasClass = "";
		 $offcanvasToggle = "";
		 $sidepanel = "";
		 $ocw = "12";}
 	
	if ($pageDetails["footer"] or $pageDetails["licence"])
		{
		ob_start();			
		echo <<<END
  <footer>
		<div class="container-fluid">
			<div class="row">
				<div class="col-5" style="text-align:left;">$pageDetails[footer]</div>
				<div class="col-2" style="text-align:center;">$pageDetails[footer2]</div>
				<div class="col-5" style="text-align:right;">$pageDetails[licence]</div>
			</div>
		</div>        
  </footer>
END;
		$pageDetails["footer"] = ob_get_contents();
		ob_end_clean(); // Don't send output to client
		}
  
  if($pageDetails["fluid"]) {$containerClass = "container-fluid";}
  else {$containerClass = "container";}
  
  $fn = "function"; 
	ob_start();			
	echo <<<END
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="$pageDetails[metaDescription]" />
		<meta name="keywords" content="$pageDetails[metaKeywords]" />
    <meta name="author" content="$pageDetails[metaAuthor]" />
    <link rel="icon" href="$pageDetails[metaFavIcon]">
    <title>$pageDetails[metaTitle]</title>
    $cssScripts
    <style>
    $pageDetails[extra_css]
    </style>
  </head>

  <body onload="onLoad();">
		<div class="$containerClass">
			$pageDetails[topNavbar]
			<div class="row $offcanvasClass">				
			 <div class="col-12 col-md-$ocw">          
				$offcanvasToggle
				$pageDetails[body]
			</div><!--/span-->
			
			$sidepanel
			</div><!--/row-->
			
			$pageDetails[footer]
    </div><!--/.container-->
    
    $jsScripts
    <script>
			$pageDetails[extra_js]
			$fn onLoad() {
				$pageDetails[extra_onload]
				}
    </script>
  </body>
</html>
END;
	$page_html = ob_get_contents();
	ob_end_clean(); // Don't send output to client

	return ($page_html);
	}	

?>
