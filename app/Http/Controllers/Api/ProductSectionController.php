<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductSection;

class ProductSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($productId)
    {
        try{
            $productSections = ProductSection::where('product_id', $productId)->with('contents')->get();
            return $this->successResponse('Sections fetched.', $productSections);
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
            'title' => 'required',
            'description' => 'required',
            'is_available_immediately' => 'sometimes|boolean',
            'is_delayed_by' => 'nullable|integer'
        ]);

        try {
            $request->merge(['user_id' => auth()->id()]);
            $productSection = ProductSection::create($request->all());
            return $this->successResponse('Section Saved.', $productSection, 201);
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
            $productSection = ProductSection::findOrFail($id);
            return $this->successResponse('Section fetched.', $productSection);
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
        try{
            $productSection = ProductSection::findOrFail($id);
            if ($productSection->user_id != auth()->id()) {
                return $this->errorResponse('Unauthorized.', 403);
            }
            $productSection->update($request->all());
            return $this->successResponse('Section updated.', $productSection);
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
            $productSection = ProductSection::findOrFail($id);
            if ($productSection->user_id != auth()->id()) {
                return $this->errorResponse('Unauthorized.', 403);
            }
            $productSection->delete();
            return $this->successResponse('Section deleted.');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }
}
