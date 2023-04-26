<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\People;
use Illuminate\Http\Request;

class PeopleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $people = People::orderByDesc('created_at')->paginate(25);
        return $this->successResponse('Poeple Fetched', $people);
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
            'email' => 'required|email|unique:people,email',
            'email_marketing_subscribed_at' => 'nullable|date_format:Y-m-d H:i:s|after:now'
        ], [
            'email.unique' => 'This email has already been added'
        ]);

        $people = People::create($request->all());
        return $this->successResponse('Person added', $people, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\People  $people
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $people = People::findOrFail($id);
        return $this->successResponse('Person fetched', $people);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\People  $people
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $poepleId)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email_marketing_subscribed_at' => 'nullable|date_format:Y-m-d H:i:s|after:now'
        ]);

        // $people->update(['name' => $request->name, 'email_marketing_subscribed_at' => $request->email_marketing_subscribed_at]);

        return $this->successResponse('Person Updated', ['asdfl' => $people]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\People  $people
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $people = People::findOrFail($id);
        $people->delete();
        return $this->successResponse('Person deleted successfully');
    }
}
