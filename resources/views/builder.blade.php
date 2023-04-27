@php
    // $html = file_get_contents('editor.html');
    $html = public_path('builder/editor.html'); //assuming editor.html is stored in the 'resources/views' directory

    if (request()->has('u') && request()->has('p')) {
        $u = request()->input('u');
        $p = request()->input('p');

        $path = public_path('builder/projects/'.$u.'/'.$p);

        if (!file_exists($path)) {
            return redirect('https://growmatic.bennyondev.com/builder');
        }

    } else {
        return redirect('https://growmatic.bennyondev.com/builder');
    }

    $htmlFiles = glob('{' . $path . '/*.html,' . $path . '/*/*.html}',  GLOB_BRACE);

    $files = '';
    foreach ($htmlFiles as $file) {
        if (in_array($file, ['new-page-blank-template.html', 'editor.html'])) continue; //skip template files
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

    $html = str_replace('(pages);', "([$files]);", $html);

    $save_url = "https://growmatic.bennyondev.com/api/v1/project/page/$u/$p";

    $html = str_replace('SAVE_URL', $save_url, $html);

    echo $html;
@endphp
