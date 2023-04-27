<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function editor($project_id){
        $project = Project::findOrFail($project_id);

        $path = public_path('builder/projects/'.$project->user_id.'/'.$project->slug);

        $htmlFiles = glob("{{$path}/*.html,{$path}/*/*.html}", GLOB_BRACE);

        $files = '';
        foreach ($htmlFiles as $file) {
            if (in_array($file, array('new-page-blank-template.html', 'editor.html'))) continue;
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


// Remove the trailing comma from the $files string
        $files = rtrim($files, ',');

//        return response()->json($files);
        return view('editor', compact('project', 'files'));
    }
}
