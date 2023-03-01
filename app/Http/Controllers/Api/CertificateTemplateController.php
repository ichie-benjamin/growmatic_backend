<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CertificateTemplate;
use App\traits\UploadTrait;

class CertificateTemplateController extends Controller
{
    use UploadTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = CertificateTemplate::all();
        return $this->successResponse('Templates Fetched', $templates);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'design' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        try {
            $data = $request->all();
            $path = $this->uploadOne($request->file('design'));
            $data['design'] = $path;
    
            $template = CertificateTemplate::create($data);
            return $this->successResponse('Template Stored.', $template, 201);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'design' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        try {
            $data = $request->all();
    
            $template = CertificateTemplate::findOrFail($id);
            if ($request->has('design')) {
                $path = $this->uploadOne($request->file('design'));
                $data['design'] = $path;
            }
            $oldTemplate = $template->replicate();
            $template->update($data);

            if ($oldTemplate->design && $request->has('design')) $this->deleteOne($oldTemplate->design);

            return $this->successResponse('Template Updated.', $template);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
