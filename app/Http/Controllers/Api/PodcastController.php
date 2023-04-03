<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Podcast;
use Illuminate\Http\Request;

class PodcastController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $podcasts = Podcast::where('user_id', auth()->id())->paginate(20);
        return $this->successResponse('Podcast Fetched.', $podcasts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateData($request);
        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['published_at'] = $request->published_at == true ? date("Y-m-d h:i:s") : null;

        $postcast = Podcast::create($data);
        return $this->successResponse('Podcast created', $postcast);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Podcast  $podcast
     * @return \Illuminate\Http\Response
     */
    public function show(Podcast $podcast)
    {
        return $this->successResponse('Podcast fetched', $podcast);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Podcast  $podcast
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Podcast $podcast)
    {
        $this->validateData($request);
        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['published_at'] = $request->published_at == true ? date("Y-m-d h:i:s") : null;

        $podcast->update($data);

        return $this->successResponse('Podcast Updated', $podcast);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Podcast  $podcast
     * @return \Illuminate\Http\Response
     */
    public function destroy(Podcast $podcast)
    {
        $podcast->delete();
        return $this->successResponse('Podcast Delete Successfully');
    }

    private function validateData($request) {
        $this->validate($request, [
            'podcast_category_id' => 'required|exists:podcast_categories,id',
            'type' => 'required|in:private,public',
            'title' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'language' => 'required',
            'in_episodic_order' => 'nullable|boolean',
            'in_serial_order' => 'nullable|boolean',
            'published_at' => 'nullable|boolean'
        ]);
    }
}
