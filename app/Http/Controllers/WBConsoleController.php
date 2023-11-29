<?php

namespace App\Http\Controllers;

use App\Models\WBConsole;
use App\Models\APIActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class WBConsoleController extends Controller
{

    /**
     * Opration of Whereby Console
    **/ 

    function getCredentials(){
        
        $WBConsoles = WBConsole::all()->where('user', Auth::user()->id);
        $accessTokens = \DB::table('personal_access_tokens')->where('tokenable_id', Auth::user()->id)->select('id', 'name')->get();
    
        return view('whereby.whereby-console', compact('WBConsoles','accessTokens'));
    }    
    
    //Check validation and add product
    function addCredentials(Request $req){

        $req->validate([
            'token_id'=>'required | max:225',
            'bearer'=>'required | max:500',
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
        // Not Available
        echo "index";
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Validate the request
        $request->validate([
            'roomName' => 'required|string', 
        ]);

        // Create the Room

        $userToken = request()->bearerToken();

        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        $WBConsole = WBConsole::all('token_id','bearer')->where('token_id', $accessToken->id)->first();

        if (!$WBConsole) {

            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Create Room";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/wb";
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "error";
            $a_p_i_activities->save();

            return response()->json(['error' => 'Invalid token for Whereby'], 401);
        
        }else {
            
            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Create Room";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/wb";
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "success";
            $a_p_i_activities->save();

        }

        // Get room name from the request payload
        $room = $request->input('roomName');
        $roomName = str_replace(' ', '-', $room);

        $url = 'https://api.whereby.dev/v1/meetings';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $WBConsole->bearer,
        ])->post($url, [
            'isLocked' => true,
            'roomNamePrefix' => $roomName,
            'roomNamePattern' => 'uuid',
            'roomMode' => 'group',
            'endDate' => '2030-09-10',
            'fields' => [
                'hostRoomUrl'
            ]
        ]);

        // Extract meetingId and hostRoomUrl from the API response
        $meetingId = $response->json('meetingId');
        $hostRoomUrl = $response->json('hostRoomUrl');

        return response()->json([
            'meetingId' => $meetingId,
            'hostRoomUrl' => $hostRoomUrl,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(WBConsole $wBConsole)
    {
        //
        echo "show";
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WBConsole $wBConsole)
    {
        //
        echo "edit";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WBConsole $wBConsole)
    {
        //
        echo "update";

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $userToken = request()->bearerToken();

        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        $WBConsole = WBConsole::all('token_id','bearer')->where('token_id', $accessToken->id)->first();

        if (!$WBConsole) {

            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Delete Room";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/wb/".$id;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "error";
            $a_p_i_activities->save();

            return response()->json(['error' => 'Invalid token for Whereby'], 401);
        
        }else {
            
            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Delete Room";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/wb/".$id;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "success";
            $a_p_i_activities->save();

        }

        $url = 'https://api.whereby.dev/v1/meetings/'.$id;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$WBConsole->bearer,
            'Accept' => 'application/json',
        ])->delete($url);

        return response()->json(['message' => 'Room destroy successfully. Room ID: ' . $id], 200);
    }
}
