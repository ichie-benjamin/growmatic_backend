<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProjectRepository;

use App\Services\TemplateLoader;
use App\Services\TemplateRepository;

use Common\Database\Datasource\DatasourceFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class ProjectsController extends Controller
{

    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var Project
     */
    private Project $project;

    /**
     * @var ProjectRepository
     */
    private ProjectRepository $repository;

    public function __construct(Request $request, Project $project, ProjectRepository $repository) {
        $this->request = $request;
        $this->project = $project;
        $this->repository = $repository;
    }
    public function index()
    {
//        $builder = $this->project->with(['domain', 'users']);
        $builder = $this->project->with(['users']);

        $userId = $this->request->get('user_id') ?? auth()->id();


        if ($userId) {
            $builder->whereHas('users', function (Builder $q) use ($userId) {
                return $q->where('users.id', $userId);
            });
        }

        if($this->request->has('published') && $this->request->get('published') !== 'all') {
            $builder->where('published', $this->request->get('published'));
        }

        $builder = $builder->paginate(100);
        return $this->successResponse('projects',$builder);
    }

    public function show($id)
    {
        $project = $this->project->with('pages', 'users')->findOrFail($id);

        $this->authorize('show', $project);

        $project = $this->repository->load($project);

        return $this->success(['project' => $project]);
    }

    public function update(int $id)
    {
        $project = $this->project->with('users')->find($id);

//        $this->authorize('update', $project);

        $this->validate($this->request, [
            'name' => 'string|min:3|max:255',
            'css' => 'nullable|string|min:1',
            'js' => 'nullable|string|min:1',
            'template' => 'nullable|string|min:1|max:255',
            'custom_element_css' => 'nullable|string|min:1',
            'published' => 'boolean',
            'pages' => 'array',
            'pages.*' => 'array',
        ]);

        $this->repository->update($project, $this->request->all());

        return $this->success(['project' => $this->repository->load($project)]);
    }

    public function savePage($project_id)
    {

        return $this->request;

//        $this->authorize('update', $project);

        $this->validate($this->request, [
            'name' => 'string|min:3|max:255',
            'css' => 'nullable|string|min:1',
            'js' => 'nullable|string|min:1',
            'template' => 'nullable|string|min:1|max:255',
            'custom_element_css' => 'nullable|string|min:1',
            'published' => 'boolean',
            'pages' => 'array',
            'pages.*' => 'array',
        ]);

        $this->repository->update($project, $this->request->all());

        return $this->success(['project' => $this->repository->load($project)]);
    }

    public function toggleState(Project $project)
    {
        $this->authorize('update', $project);

        $project
            ->fill(['published' => $this->request->get('published')])
            ->save();

        return $this->success(['project' => $project]);
    }

    public function store()
    {

        $user_id = auth()->id();

        $this->validate($this->request, [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('projects')->where(function ($query) use ($user_id) {
                    return $query->where('user_id', $user_id);
                }),
            ],
//            'slug' => 'string|min:3|max:30|unique:projects',
            'css' => 'nullable|string|min:1|max:255',
            'js' => 'nullable|string|min:1|max:255',
            'template_name' => 'nullable|string',
            'published' => 'boolean',
        ]);


        $project = $this->repository->create($this->request->all());

        return $this->successResponse('project',['project' => $this->repository->load($project)]);
    }

    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);

        $this->repository->delete($project);

        return $this->successResponse($project->name.' successfully deleted', $project);

//        $projectIds = explode(',', $ids);
//        foreach ($projectIds as $id) {
//            $project = $this->project->findOrFail($id);
//
//            $this->authorize('destroy', $project);
//
//            $this->repository->delete($project);
//        }

    }

}
