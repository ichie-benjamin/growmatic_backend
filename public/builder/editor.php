<?php
//include 'editor.html';
$html = file_get_contents('editor.html');

if (isset($_GET['u']) && isset($_GET['p'])) {
    $u = $_GET['u'];
    $p = $_GET['p'];

    $path = 'projects/'.$u.'/'.$p;

    if (!file_exists($path)) {
        header('Location: https://growmatic.bennyondev.com/builder');
        exit;
    }

} else {
    header('Location: https://growmatic.bennyondev.com/builder');
    exit;
}

//search for html files in demo and my-pages folders
//$htmlFiles = glob('{my-pages/*.html,demo/*\/*.html, demo/*.html}',  GLOB_BRACE);
$htmlFiles = glob('{' . $path . '/*.html,' . $path . '/*/*.html, ',  GLOB_BRACE);

$files = '';
foreach ($htmlFiles as $file) {
   if (in_array($file, array('new-page-blank-template.html', 'editor.html'))) continue;//skip template files
   $pathInfo = pathinfo($file);
   $filename = $pathInfo['filename'];
   $folder = preg_replace('@/.+?$@', '', $pathInfo['dirname']);
   $subfolder = preg_replace('@^.+?/@', '', $pathInfo['dirname']);
   if ($filename == 'index' && $subfolder) {
	   $filename = $subfolder;
   }
   $url = $pathInfo['dirname'] . '/' . $pathInfo['basename'];
   $name = ucfirst($filename);

  $files .= "{name:'$name', file:'$file', title:'$name',  url: '$url', folder:'$folder'},";
}


//replace files list from html with the dynamic list from demo folder
$html = str_replace('(pages);', "([$files]);", $html);

$pagesJson = json_encode($files);

dd($pagesJson);

//$html = str_replace('let pagesData = null;', "let pagesData = $pagesJson;", $html);
$html = str_replace('REPLACE_PAGES_DATA', $pagesJson, $html);

echo $html;
