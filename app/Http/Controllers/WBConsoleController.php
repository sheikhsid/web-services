<?php

namespace App\Http\Controllers;

use App\Models\WBConsole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WBConsoleController extends Controller
{

    /**
     * Opration of Whereby Console
    **/ 

     function getCredentials(){
        
        $WBConsoles = WBConsole::all();
        $accessTokens = \DB::table('personal_access_tokens')->where('tokenable_id', Auth::user()->id)->select('id', 'name')->get();
    
        return view('whereby.whereby-console', compact('WBConsoles','accessTokens'));
    }    
    
    //Check validation and add product
    function addCredentials(Request $req){

        $req->validate([
            'token_id'=>'required | max:225',
            'bearer'=>'required | max:225',
        ]);

        $credential= new WBConsole;
        $credential->user=Auth::user()->id;
        $credential->token_id=$req->token_id;
        $credential->bearer=$req->bearer;
        $credential->save();

        return redirect('/whereby-console');

    }

     //Delete Data
     function deleteCredentials($id){

        $data= WBConsole::find($id);
        $data->delete();  
        return redirect('/whereby-console');     

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
    public function show(WBConsole $wBConsole)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WBConsole $wBConsole)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WBConsole $wBConsole)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WBConsole $wBConsole)
    {
        //
    }
}
