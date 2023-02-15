<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;   
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\traits\UploadTrait;

class ProductController extends Controller
{
    use UploadTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->with('detail', 'section')->paginate(20);
        return $this->successResponse('Product Fetched!', $products);
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
            'product_type_id' => 'required:exists:product_types',
            'title' => 'required',
            'description' => 'required',
            'thumbnail' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        try {
            $data = $request->all();
            if ($request->has('thumbnail')) {
                $path = $this->uploadOne($request->file('thumbnail'));
                $data['thumbnail'] = $path;
            }

            $data['user_id'] = auth()->id();
            $product = Product::create($data);
            return $this->successResponse('Product Created.', $product, 201);
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
        // try {
            $product = Product::findOrFail($id);
            return $this->successResponse('Product Fetched.', $product);
        // } catch (ModelNotFoundException $e) {
        //     return $this->errorResponse('Product Not Found.', 404);
        // } catch (\Throwable $th) {
        //     return $this->errorResponse($th->getMessage(), 500);
        // }
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
            'title' => 'required',
            'description' => 'required',
            'thumbnail' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        try {
            $product = Product::findOrFail($id);
            if (auth()->id() != $product->user_id) {
                return $this->errorResponse('Unauthorized.', 403);
            }
            $product->update($request->all());
            return $this->successResponse('Product Updated.', $product);
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
            $product = Product::findOrFail($id);
            if (auth()->id() != $product->user_id) {
                return $this->errorResponse('Unauthorized.', 403);
            }
            $product->delete();
            return $this->successResponse('Product Deleted.', $product);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    public function section(Request $request, $productId) {
        
    }

    public function pricingIndex(Request $request, $productId) {

        $product = Product::findOrFail($productId);
        return $this->successResponse('Prices fetched.', $product->prices);
    }

    public function pricingStore(Request $request, $productId) {
        $this->validate($request, [
            'amount' => 'required|numeric',
            'type' => 'nullable|in:recurring,one-time,free',
            'recurrence' => 'nullable|in:weekly,monthly,yearly',
            'recurring_amount' => 'nullable|numeric',
            'payment_plan' => 'nullable|string',
            'payment_plan_description' => 'nullable|string'
        ]);

        $product = Product::findOrFail($productId);
        $request->merge(['user_id' => auth()->id()]);
        $product->prices()->create($request->all());
        return $this->successResponse('Pricing Added', $product->prices()->latest()->first(), 201);
    }

    public function certificateShow(Request $request, $productId) {
        $product = Product::findOrFail($productId);
        return $this->successResponse('Certificate fetched.', $product->certificate);
    }

    public function certificateStore(Request $request, $productId) {
        $this->validate($request, [
            'certificate_template_id' => 'required|exists:certificate_templates,id',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'title' => 'required|string',
            'subtitle' => 'required|string',
            'name_subtitle' => 'required|string',
            'course_name' => 'required|string',
            'show_signature' => 'sometimes|boolean',
            'signature' => 'required_if:show_signature,1|image|mimes:png,jpg,jpeg|max:2048',
            'show_seal' => 'sometimes|boolean',
            'seal' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'show_completion_date' => 'sometimes|boolean',
            'show_unique_serial_number' => 'sometimes|boolean',
            'background_color' => 'nullable|string',
            'border_color' => 'nullable|string',
            'primary_text_color' => 'nullable|string',
            'secondary_text_color' => 'nullable|string',
            'template_color' => 'nullable|string',
        ]);

        $data = $request->all();
        $product = Product::findOrFail($productId);
        if ($product->user_id != auth()->id()) {
            return $this->errorResponse('Unauthorized.', 403);
        }

        if ($request->has('signature')) {
            $path = $this->uploadOne($request->file('signature'));
            $data['signature'] = $path;
        }

        if ($request->has('seal')) {
            $path = $this->uploadOne($request->file('seal'));
            $data['seal'] = $path;
        }

        $data['user_id'] = auth()->id();

        $product->certificate()->updateOrCreate(
            ['product_id' => $productId], $data
        );

        return $this->successResponse('Certificate updated.', $product->certificate, 201);
    }

    public function detail(Request $request, $productId) {
        $this->validate($request, [
            'title' => 'required',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'category' => 'nullable|string'
        ]);

        $product = Product::findOrFail($productId);
        if ($request->has('image')) {
            $path = $this->uploadOne($request->file('image'));
            $data['image'] = $path;
        }

        $product->detail()->updateOrCreate(
            ['product_id' => $productId], $request->all()
        );

        return $this->successResponse('Detail Saved.', $product->detail, 201);
    }

    public function certificate(Request $request, $productId) {
        $this->validate($request, [
            'title' => 'required',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'category' => 'nullable|string'
        ]);

        $product = Product::findOrFail($productId);
        if ($request->has('image')) {
            $path = $this->uploadOne($request->file('image'));
            $data['image'] = $path;
        }

        $product->certificate()->updateOrCreate(
            ['product_id' => $productId], $request->all()
        );

        return $this->successResponse('Detail Saved.', $product->detail, 201);
    }
}
