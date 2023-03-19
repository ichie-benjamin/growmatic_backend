<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProjectRepository;

use Common\Database\Datasource\DatasourceFilters;
use Common\Database\Datasource\MysqlDataSource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ProjectsController extends Controller
{


    protected $httpClient;

    protected string $baseUrl;


    public function __construct()
    {
        $this->httpClient = Http::withToken(env("ACCESS_TOKEN"));
        $this->baseUrl = "https://builder.bennyondev.com/api/v1";
    }


    public function index(): JsonResponse
    {
        $response = $this->httpClient->get($this->baseUrl.'/projects');

        return response()->json($response->json());

    }
    public function templates(): JsonResponse
    {
        $response = $this->httpClient->get($this->baseUrl.'/templates');

        return response()->json($response->json());

    }

    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'slug' => 'string|min:3|max:30',
            'css' => 'nullable|string|min:1|max:255',
            'js' => 'nullable|string|min:1|max:255',
            'template_name' => 'nullable|string',
            'published' => 'boolean',
        ]);

        $response = $this->httpClient->post($this->baseUrl.'/projects', [
            'name' => $request['name'],
            'slug' => $request['slug'],
            'template_name' => $request['template_name'],
            'js' => $request['js'],
            'css' => $request['css'],
            'published' => true,
        ]);

        return response()->json($response->json());
    }


    public function show($id)
    {
        $project = $this->project->with('pages', 'users')->findOrFail($id);

        $this->authorize('show', $project);

        $project = $this->repository->load($project);

        return $this->success(['project' => $project]);
    }



}
