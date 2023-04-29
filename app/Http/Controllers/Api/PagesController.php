<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
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


        $projectPath = "{$project->user_id}/{$project->slug}";


        $data['html'] = $request->input('html');
        $data['template'] = $request->input('startTemplateUrl');
        $data['file'] = str_replace('.html', '', $request->input('file'));

        $data['file'] = $data['file'].'.html';

        if($request->filled('startTemplateUrl')){

            $data['html'] = Storage::disk('builder')->get($data['template']);

//            $path = $storage->path($data['file']);
//
//            $dir = dirname($path);
//
//            if (!is_dir($dir)) {
//                Storage::makeDirectory($dir);
//            }

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

            return $this->successResponse('File saved.', $new_data);
        } catch (\Exception $e) {
            return $this->errorResponse("Error saving file': " . $e->getMessage(), 500);
        }

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
            $success = Storage::disk('projects')->move("$projectPath/{$data['file']}", "$projectPath/{$data['newfile']}");
        }else{
            $success = Storage::disk('projects')->copy("$projectPath/{$data['file']}", "$projectPath/{$data['newfile']}");
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
