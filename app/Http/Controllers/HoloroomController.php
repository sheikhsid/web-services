<?php

namespace App\Http\Controllers;

use App\Models\Holoroom;
use App\Models\APIActivity;
use Illuminate\Http\Request;

class HoloroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userToken = request()->bearerToken();

        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        $holorooms = Holoroom::all('id', 'name', 'room_ip', 'capacity')->where('capacity', '<', 2)->values();

         //Store API Activities
         $a_p_i_activities = new APIActivity();
         $a_p_i_activities->token_id = $accessToken->id;
         $a_p_i_activities->resource = "Get Holoroom List";
         $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/holorooms";
         $a_p_i_activities->ip_address = request()->ip();
         $a_p_i_activities->response = "success";
         $a_p_i_activities->save();

        return response()->json($holorooms);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //Empty
        echo "create is Empty";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $userToken = request()->bearerToken();

        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        // Validate the request
        $request->validate([
            'name' => 'required|string|max:225', 
            'room_ip' => 'required|string|max:225', 
        ]);

        // Create a new Holoroom record
        $holoroom = new Holoroom();
        $holoroom-> name = $request->name;
        $holoroom-> room_ip = $request->room_ip;
        $holoroom-> ip_address = $request->ip();
        $holoroom->save();

        //Store API Activities
        $a_p_i_activities = new APIActivity();
        $a_p_i_activities->token_id = $accessToken->id;
        $a_p_i_activities->resource = "Create Holoroom";
        $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/holorooms/".$holoroom->id;
        $a_p_i_activities->ip_address = request()->ip();
        $a_p_i_activities->response = "success";
        $a_p_i_activities->save();

        return response()->json(['message' => 'Holoroom successfully created', 'id' => $holoroom->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // $userToken = request()->bearerToken();

        // $accessToken = \DB::table('personal_access_tokens')
        //     ->where('token', hash('sha256', $userToken))
        //     ->first();

        //     //Store API Activities
        //     $a_p_i_activities = new APIActivity();
        //     $a_p_i_activities->token_id = $accessToken->id;
        //     $a_p_i_activities->resource = "Get One Holoroom";
        //     $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/holorooms/".$id;
        //     $a_p_i_activities->ip_address = request()->ip();
        //     $a_p_i_activities->response = "success";
        //     $a_p_i_activities->save();

        // $holoroom = Holoroom::select('id', 'name', 'room_ip', 'capacity')->findOrFail($id);
        // return response()->json($holoroom);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Holoroom $holoroom)
    {
        //Empty
        echo "edit is Empty";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {

        $userToken = request()->bearerToken();

        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        $holoroom = Holoroom::where('id', $id)->firstOrFail();

        if ($holoroom->capacity < 2) {
            if (!$holoroom) {
                return response()->json(['message' => 'Holoroom not found'], 404);
            }

            $holoroom->capacity = '2';
            $holoroom->save();

            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Update capacity";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/holorooms/".$id;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "success";
            $a_p_i_activities->save();

            // Return the specified columns for the updated Holoroom
            return response()->json([
                'id'        => $holoroom->id,
                'name'      => $holoroom->name,
                'room_ip'   => $holoroom->room_ip,
                'message'   => 'capacity is full now',
            ]);
        } else {
            // Return a message indicating that the room is already full
            return response()->json(['message' => 'Room is already full'], 400);
        }
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


        $holoroom = Holoroom::find($id);

        if (!$holoroom) {
            return response()->json(['message' => 'Holoroom not found'], 404);
        }

        $holoroom->delete();

        //Store API Activities
        $a_p_i_activities = new APIActivity();
        $a_p_i_activities->token_id = $accessToken->id;
        $a_p_i_activities->resource = "Delete Holoroom";
        $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/holorooms/".$id;
        $a_p_i_activities->ip_address = request()->ip();
        $a_p_i_activities->response = "success";
        $a_p_i_activities->save();

        return response()->json(['message' => 'Holoroom successfully deleted']);
    }
}
