<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductContent;

class ProductContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($productId)
    {
        try{
            $productContents = ProductContent::where('product_id', $productId)->get();
            return $this->successResponse('Contents fetched.', $productContents);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
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
            'product_id' => 'required|exists:products,id',
            'product_section_id' => 'nullable|exists:product_sections,id',
            'title' => 'required|string',
            'type' => 'required|in:text,embed,video,quiz,coaching,file',
            'content' => 'required'
        ]);

        try {
            $request->merge(['user_id' => auth()->id()]);
            $productContent = ProductContent::create($request->all());

            if ($request->type == 'file' || $request->type == 'video') {
                // process file upload
            }

            // if content type is a video or file, perform an extra validation for the video/file type
            
            return $this->successResponse('Content Saved.', $productContent, 201);
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
        try{
            $productContent = ProductContent::findOrFail($id);
            return $this->successResponse('Content fetched.', $productContent);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
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
            'product_section_id' => 'nullable|exists:product_sections,id',
            'title' => 'required|string',
            'type' => 'required|in:text,embed,video,quiz,coaching,file',
            'content' => 'required'
        ]);
        
        try{
            $productContent = ProductContent::findOrFail($id);
            if ($productContent->user_id != auth()->id()) {
                return $this->errorResponse('Unauthorized.', 403);
            }
            $productContent->update($request->except('product_id'));
            return $this->successResponse('Content updated.', $productContent);
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
        try {
            $productContent = ProductContent::findOrFail($id);
            if ($productContent->user_id != auth()->id()) {
                return $this->errorResponse('Unauthorized.', 403);
            }
            $productContent->delete();
            return $this->successResponse('Content deleted.');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }
}
