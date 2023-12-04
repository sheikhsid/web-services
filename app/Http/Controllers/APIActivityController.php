<?php

namespace App\Http\Controllers;

use App\Models\APIActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class APIActivityController extends Controller
{
    
    function getActivities(){
        
        $APIActivities = APIActivity::orderBy('created_at', 'desc')->paginate(20);
        $accessToken = \DB::table('personal_access_tokens')->where('tokenable_id', Auth::user()->id)->select('id', 'name', 'last_used_at')->get();

        return view('api', compact('APIActivities', 'accessToken'));    

    }

    function getActivity($id){
        

        $APIActivities = APIActivity::orderBy('created_at', 'desc')->where('token_id', $id)->paginate(20);
        $accessToken = \DB::table('personal_access_tokens')->where('tokenable_id', Auth::user()->id)->select('id', 'name', 'last_used_at')->get();

        return view('api', compact('APIActivities', 'accessToken'));    

    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(APIActivity $aPIActivity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(APIActivity $aPIActivity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, APIActivity $aPIActivity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(APIActivity $aPIActivity)
    {
        //
    }
}
