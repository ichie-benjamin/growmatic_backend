<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function editor($project_id){
        $project = Project::findOrFail($project_id);


        $path = public_path("builder/projects/{$project->user_id}/{$project->slug}");



        $htmlFiles = glob("{{$path}/*.html,{$path}/*/*.html}", GLOB_BRACE);

//        $htmlFiles = glob("{$path}/*.html");

        $files = [];
        foreach ($htmlFiles as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);

            $subfolder = preg_replace('@^.+?/@', '', pathinfo($file, PATHINFO_DIRNAME));
            if ($filename == 'index' && $subfolder) {
                $filename = basename(dirname($file));
            }

            if (in_array($filename, ['new-page-blank-template', 'editor'])) {
                continue;
            }
            $url = str_replace($path . '/', '', $file);
            $name = ucfirst($filename);
            $c_file = str_replace($path.'/','',$file);
            $folder = basename(dirname($file));
            $files[] = [
                'name' => $name,
                'file' => $c_file,
                'title' => $filename,
                'url' => asset('builder/projects/'.$project->user_id.'/'.$project->slug.'/'.$c_file),
                'folder' => $folder,
            ];
        }

//        return response()->json($files);
        return view('editor', compact('project', 'files'));
    }
}
