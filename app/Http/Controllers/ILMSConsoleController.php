<?php

namespace App\Http\Controllers;

use App\Models\ILMSConsole;
use App\Models\APIActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class ILMSConsoleController extends Controller
{
    /**
     * Opration of AWS EC2 Console
     */ 

     function getCredentials(){
        
        $ILMSConsoles = ILMSConsole::all()->where('user', Auth::user()->id);
        $accessTokens = \DB::table('personal_access_tokens')->where('tokenable_id', Auth::user()->id)->select('id', 'name')->get();
    
        return view('services.ilms-console', compact('ILMSConsoles', 'accessTokens'));
    }    
    
    //Check validation and add product
    function addCredentials(Request $req){

        $req->validate([
            'token_id'=>'required | max:225',    
        ]);

        $credential= new ILMSConsole;
        $credential->user=Auth::user()->id;
        $credential->token_id=$req->token_id;
        $credential->status=1;
        $credential->save();

        return redirect('/ilms-console');

    }

     //Delete Data
     function deleteCredentials($id){

        $data= ILMSConsole::find($id);
        $data->delete();  
        return redirect('/ilms-console');     

    }
    
    
    

    // $ILMSConsole = ILMSConsole::where('token_id', $accessToken->id)->get(['instanceId', 'publicDnsName', 'token_id']);

    public function index()
    {
        $userToken = request()->bearerToken();

        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        $ILMSConsole = ILMSConsole::all()->where('token_id', $accessToken->id)->first();


        if($ILMSConsole){

            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Get Licenses";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/licenses";
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "success";
            $a_p_i_activities->save();

            // API endpoint
            $url = 'https://app.immensive.it/api/licenses';

            // Bearer token for the API request
            $token = 'your_bearer_token_here'; // Replace with your actual bearer token

            // Initialize cURL session
            $ch = curl_init();

            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Accept: application/json',
            ]);

            // Execute cURL request and get response
            $response = curl_exec($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
                return response()->json(['error' => curl_error($ch)], 500);
            } else {
                // Decode the JSON response
                $responseData = json_decode($response, true);

                // Return the response data
                return response()->json($responseData);
            }

        }else{
            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Invalid Token";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/licenses";
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "error";
            $a_p_i_activities->save();

            return response()->json(['error' => 'Invalid token'], 401);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $userToken = request()->bearerToken();

        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        $ILMSConsole = ILMSConsole::all()->where('token_id', $accessToken->id)->first();


        if($ILMSConsole){

            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Get Single Licenses";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/licenses/".$id;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "success";
            $a_p_i_activities->save();

            // API endpoint
            $url = 'https://app.immensive.it/api/licenses/'.$id;

            // Bearer token for the API request
            $token = 'your_bearer_token_here'; // Replace with your actual bearer token

            // Initialize cURL session
            $ch = curl_init();

            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Accept: application/json',
            ]);

            // Execute cURL request and get response
            $response = curl_exec($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
                return response()->json(['error' => curl_error($ch)], 500);
            } else {
                // Decode the JSON response
                $responseData = json_decode($response, true);

                // Return the response data
                return response()->json($responseData);
            }

        }else{
            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Invalid Token";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/licenses/".$instanceId;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "error";
            $a_p_i_activities->save();

            return response()->json(['error' => 'Invalid token'], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $userToken = request()->bearerToken();
    
        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();
    
        $ILMSConsole = ILMSConsole::where('token_id', $accessToken->id)->first();
    
        if($ILMSConsole){
    
            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "PUT Device Active";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/licenses/".$id;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "success";
            $a_p_i_activities->save();
    
            // Prepare the data to send
            $sendData = [
                'software_v' => $request->input('software_v'),
                'mac' => $request->input('mac'),
            ];
    
            // API endpoint
            $url = 'https://app.immensive.it/api/licenses/'.$id;
    
            // Bearer token for the API request
            $token = 'your_bearer_token_here';
    
            // Initialize cURL session
            $ch = curl_init();
    
            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Accept: application/json',
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sendData));
    
            // Execute cURL request and get response
            $response = curl_exec($ch);
    
            // Check for cURL errors
            if (curl_errno($ch)) {
                return response()->json(['error' => curl_error($ch)], 500);
            } else {
                // Decode the JSON response
                $responseData = json_decode($response, true);
    
                // Return the response data
                return response()->json($responseData);
            }
    
        } else {
            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Invalid Token";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/licenses/".$id;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "error";
            $a_p_i_activities->save();
    
            return response()->json(['error' => 'Invalid token'], 403);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     echo "create";
    // }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     echo "store";
    // }


    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(ILMSConsole $ILMSConsole)
    // {
    //     echo "edit";
    // }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(ILMSConsole $ILMSConsole)
    // {
    //     echo "destroy";
    // }

    public function versionsGET($id) {
        $userToken = request()->bearerToken();

        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        $ILMSConsole = ILMSConsole::all()->where('token_id', $accessToken->id)->first();


        if($ILMSConsole){

            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Get Single Licenses";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/versions/".$id;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "success";
            $a_p_i_activities->save();

            // API endpoint
            $url = 'https://app.immensive.it/api/versions/'.$id;

            // Bearer token for the API request
            $token = 'your_bearer_token_here'; // Replace with your actual bearer token

            // Initialize cURL session
            $ch = curl_init();

            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Accept: application/json',
            ]);

            // Execute cURL request and get response
            $response = curl_exec($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
                return response()->json(['error' => curl_error($ch)], 500);
            } else {
                // Decode the JSON response
                $responseData = json_decode($response, true);

                // Return the response data
                return response()->json($responseData);
            }

        }else{
            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Invalid Token";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/versions/".$instanceId;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "error";
            $a_p_i_activities->save();

            return response()->json(['error' => 'Invalid token'], 401);
        }
    }

    public function instituteGET($id) {
        $userToken = request()->bearerToken();

        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        $ILMSConsole = ILMSConsole::all()->where('token_id', $accessToken->id)->first();


        if($ILMSConsole){

            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Get Client Labs";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/institute/".$id;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "success";
            $a_p_i_activities->save();

            // API endpoint
            $url = 'https://app.immensive.it/api/institute/'.$id;

            // Bearer token for the API request
            $token = 'your_bearer_token_here'; // Replace with your actual bearer token

            // Initialize cURL session
            $ch = curl_init();

            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Accept: application/json',
            ]);

            // Execute cURL request and get response
            $response = curl_exec($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
                return response()->json(['error' => curl_error($ch)], 500);
            } else {
                // Decode the JSON response
                $responseData = json_decode($response, true);

                // Return the response data
                return response()->json($responseData);
            }

        }else{
            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Invalid Token";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/institute/".$instanceId;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "error";
            $a_p_i_activities->save();

            return response()->json(['error' => 'Invalid token'], 401);
        }
    }

    public function studentPOST(Request $request) {

        $userToken = request()->bearerToken();
    
        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();
    
        $ILMSConsole = ILMSConsole::where('token_id', $accessToken->id)->first();
    
        if($ILMSConsole){
    
            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Post New System";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/student/on";
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "success";
            $a_p_i_activities->save();
    
            // Prepare the data to send
            $sendData = [
                'room_id' => $request->input('room_id'),
                'account_name' => $request->input('account_name'),
                'pc_name' => $request->input('pc_name'),
                'ip_address' => $request->input('ip_address'),
            ];
    
            // API endpoint
            $url = 'https://app.immensive.it/api/student/on';
    
            // Bearer token for the API request
            $token = 'your_bearer_token_here';
    
            // Initialize cURL session
            $ch = curl_init();
    
            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Accept: application/json',
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sendData));
    
            // Execute cURL request and get response
            $response = curl_exec($ch);
    
            // Check for cURL errors
            if (curl_errno($ch)) {
                return response()->json(['error' => curl_error($ch)], 500);
            } else {
                // Decode the JSON response
                $responseData = json_decode($response, true);
    
                // Return the response data
                return response()->json($responseData);
            }
    
        } else {
            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Invalid Token";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/student/on";
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "error";
            $a_p_i_activities->save();
    
            return response()->json(['error' => 'Invalid token'], 403);
        }
    }

    public function studentPUT(Request $request, $id) {

        $userToken = request()->bearerToken();
    
        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();
    
        $ILMSConsole = ILMSConsole::where('token_id', $accessToken->id)->first();
    
        if($ILMSConsole){
    
            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "PUT System Name";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/student/".$id;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "success";
            $a_p_i_activities->save();
    
            // Prepare the data to send
            $sendData = [
                'student_name' => $request->input('student_name'),
            ];
    
            // API endpoint
            $url = 'https://app.immensive.it/api/student/'.$id;
    
            // Bearer token for the API request
            $token = 'your_bearer_token_here';
    
            // Initialize cURL session
            $ch = curl_init();
    
            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Accept: application/json',
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sendData));
    
            // Execute cURL request and get response
            $response = curl_exec($ch);
    
            // Check for cURL errors
            if (curl_errno($ch)) {
                return response()->json(['error' => curl_error($ch)], 500);
            } else {
                // Decode the JSON response
                $responseData = json_decode($response, true);
    
                // Return the response data
                return response()->json($responseData);
            }
    
        } else {
            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Invalid Token";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/student/".$id;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "error";
            $a_p_i_activities->save();
    
            return response()->json(['error' => 'Invalid token'], 403);
        }
    }

    public function studentGET($id) {

        $userToken = request()->bearerToken();

        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        $ILMSConsole = ILMSConsole::all()->where('token_id', $accessToken->id)->first();


        if($ILMSConsole){

            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Get Lab System";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/student/".$id;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "success";
            $a_p_i_activities->save();

            // API endpoint
            $url = 'https://app.immensive.it/api/student/'.$id;

            // Bearer token for the API request
            $token = 'your_bearer_token_here';

            // Initialize cURL session
            $ch = curl_init();

            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Accept: application/json',
            ]);

            // Execute cURL request and get response
            $response = curl_exec($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
                return response()->json(['error' => curl_error($ch)], 500);
            } else {
                // Decode the JSON response
                $responseData = json_decode($response, true);

                // Return the response data
                return response()->json($responseData);
            }

        }else{
            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Invalid Token";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/student/".$instanceId;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "error";
            $a_p_i_activities->save();

            return response()->json(['error' => 'Invalid token'], 401);
        }

    }

    public function studentDELETE($id) {

        $userToken = request()->bearerToken();

        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        $ILMSConsole = ILMSConsole::all()->where('token_id', $accessToken->id)->first();


        if ($ILMSConsole) {
            // Store API Activities
            $apiActivities = new APIActivity();
            $apiActivities->token_id = $accessToken->id;
            $apiActivities->resource = "Delete Lab System";
            $apiActivities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/student/" . $id;
            $apiActivities->ip_address = request()->ip();
            $apiActivities->response = "success";
            $apiActivities->save();
    
            // API endpoint
            $url = 'https://app.immensive.it/api/student/' . $id;
    
            // Bearer token for the API request
            $token = 'your_bearer_token_here'; // Replace with your actual bearer token
    
            // Initialize cURL session
            $ch = curl_init();
    
            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Accept: application/json',
            ]);
    
            // Execute cURL request and get response
            $response = curl_exec($ch);
    
            // Check for cURL errors
            if (curl_errno($ch)) {
                return response()->json(['error' => curl_error($ch)], 500);
            } else {
                // Decode the JSON response
                $responseData = json_decode($response, true);
                curl_close($ch);
    
                // Return the response data
                return response()->json($responseData);
            }
        } else {
            // Store API Activities for invalid token
            $apiActivities = new APIActivity();
            $apiActivities->token_id = $accessToken->id;
            $apiActivities->resource = "Invalid Token";
            $apiActivities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/student/" . $id;
            $apiActivities->ip_address = request()->ip();
            $apiActivities->response = "error";
            $apiActivities->save();
    
            return response()->json(['error' => 'Invalid token'], 401);
        }
    }
}
