<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use League\Flysystem\Adapter\Local;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PagesController extends Controller
{

    public function store(Request $request, $project_id)
    {
        $project = Project::findOrFail($project_id);


        $rules = [
            'template' => 'sometimes|required|string',
            'file' => 'required|string',
            'html' => Rule::requiredIf(function () use ($request) {
                return empty($request->input('startTemplateUrl'));
            }),
        ];

        $request->validate($rules);

        $storage = Storage::disk('projects');

        $root = public_path('builder');



        $projectPath = "{$project->user_id}/{$project->slug}";


        $data['html'] = $request->input('html');
        $data['template'] = $request->input('startTemplateUrl');
        $data['file'] = str_replace('.html', '', $request->input('file'));


        $data['file'] = $data['file'].'.html';

        if($request->filled('startTemplateUrl')){

            $data['html'] = Storage::disk('builder')->get($data['template']);

            $file = Storage::disk('builder')->path($data['template']);


            $templatePath = dirname(Storage::disk('builder')->path($data['template']));


//            $filePath = dirname($storage->path("$projectPath/{$data['file']}"));

            $css_file = basename(dirname($file)).'.css';


            $html_file = substr(strrchr($data['file'], '/'), 1);


            $data['css'] = str_replace($html_file, $css_file, $data['file']);


            $cssPath = "$templatePath/{$css_file}";

            $thumbPath = "$templatePath/thumbnail.png";

            if(file_exists($thumbPath)){

                return 'exists';

                $storage->put("$projectPath/thumbnail.png", $thumbPath);

            }

            return 'none';

            if(file_exists($cssPath)){

                $css_content = Storage::disk('builder')->get(str_replace($root.'/','',$cssPath));

                $storage->put("$projectPath/{$data['css']}", $css_content);

            }




        }

        try {

            $storage->put("$projectPath/{$data['file']}", $data['html']);

            $file = public_path("builder/projects/$projectPath/{$data['file']}");

            $filename = pathinfo($file, PATHINFO_FILENAME);

            $folder = basename(dirname($file));

            $new_data = [
                'name' => ucfirst($filename),
                'file' => $data['file'],
                'title' => $filename,
                'url' => asset('builder/projects/'.$project->user_id.'/'.$project->slug.'/'.$data['file']),
                'folder' => $folder,
            ];

            return $this->successResponse('Page saved.', $new_data);
        } catch (\Exception $e) {
            return $this->errorResponse("Error saving file': " . $e->getMessage(), 500);
        }

    }


    public function starterTemplates(){

        $base = 'builder/starter';

        $path = public_path($base);

        $htmlFiles = glob("{{$path}/*.html,{$path}/*/*.html}", GLOB_BRACE);
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
            $name = ucfirst($filename);
            $c_file = str_replace($path.'/','',$file);
            $folder = basename(dirname($file));
            $files[] = [
                'name' => preg_replace('/[^a-zA-Z0-9]+/', ' ', $name),
                'file' => 'starter/'.$c_file,
                'path' => $base.'/'.$c_file,
                'title' => $filename,
                'url' => asset('builder/starter/'.$c_file),
                'folder' => $folder,
                'thumbnail' => asset('builder/starter/'.$filename.'/thumbnail.png'),
            ];
        }


        return $this->successResponse('Pages fetched.', $files);
    }


    public function pages($project_id)
    {
        $project = Project::findOrFail($project_id);

        $path = public_path("builder/projects/{$project->user_id}/{$project->slug}");

        $files = $this->getFiles($project, $path);

        return $this->successResponse('Pages fetched.', $files);

    }

    private function getFiles($project, $path): array
    {
        $htmlFiles = glob("{{$path}/*.html,{$path}/*/*.html}", GLOB_BRACE);
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
                'name' => preg_replace('/[^a-zA-Z0-9]+/', ' ', $name),
                'file' => $c_file,
                'title' => $filename,
                'url' => asset('builder/projects/'.$project->user_id.'/'.$project->slug.'/'.$c_file),
                'folder' => $folder == $project->slug ? 'Root' : $folder,
            ];
        }
        return $files;
    }

    public function delete(Request $request, $project_id)
    {
        $project = Project::findOrFail($project_id);

        $projectPath = "{$project->user_id}/{$project->slug}";

        $rules = [ 'file' => 'required|string'];

        $request->validate($rules);

        $deleted = Storage::disk('projects')->delete("$projectPath/{$request['file']}");

        if($deleted){
            return $this->successResponse('Page deleted.', $request);
        }else{
            return $this->errorResponse("Error deleting file");
        }
    }

    public function edit(Request $request, $project_id)
    {
        $project = Project::findOrFail($project_id);

        $rules = [ 'file' => 'required|string', 'newfile' => 'required'];

        $request->validate($rules);

        $duplicate = $request->get('duplicate');


        $data['file'] = str_replace('.html', '', $request->input('file'));
        $data['newfile'] = str_replace('.html', '', $request->input('newfile'));

        $data['file'] = $data['file'].'.html';
        $data['newfile'] = $data['newfile'].'.html';

        $projectPath = "{$project->user_id}/{$project->slug}";

        if($duplicate){
            $success = Storage::disk('projects')->copy("$projectPath/{$data['file']}", "$projectPath/{$data['newfile']}");

        }else{
            $success = Storage::disk('projects')->move("$projectPath/{$data['file']}", "$projectPath/{$data['newfile']}");

        }


        $file = public_path("builder/projects/$projectPath/{$data['newfile']}");

        $filename = pathinfo($file, PATHINFO_FILENAME);

        $folder = basename(dirname($file));

        $new_data = [
            'name' => ucfirst($filename),
            'file' => $data['newfile'],
            'title' => $filename,
            'url' => asset('builder/projects/'.$project->user_id.'/'.$project->slug.'/'.$data['newfile']),
            'folder' => $folder,
        ];

        if($success){
            return $this->successResponse('Page '.$duplicate ? 'duplicated' : 'renamed', $new_data);
        }else{
            return $this->errorResponse("Error");
        }


    }

}
