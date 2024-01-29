<?php

namespace App\Http\Controllers;

use App\Models\VCSConsole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class VCSConsoleController extends Controller
{
    /**
     * Opration of AWS EC2 Console
     */ 

     function getCredentials(){
        
        $VCSConsoles = VCSConsole::all()->where('user', Auth::user()->id);
        $accessTokens = \DB::table('personal_access_tokens')->where('tokenable_id', Auth::user()->id)->select('id', 'name')->get();
    
        return view('services.vcs-console', compact('VCSConsoles', 'accessTokens'));
    }    
    
    //Check validation and add product
    function addCredentials(Request $req){

        $req->validate([
            'token_id'=>'required | max:225',
            'instanceId'=>'required | max:225',
            'publicDnsName'=>'required | max:225',          
        ]);

        $credential= new VCSConsole;
        $credential->user=Auth::user()->id;
        $credential->token_id=$req->token_id;
        $credential->instanceId=$req->instanceId;
        $credential->publicDnsName=$req->publicDnsName;
        $credential->save();

        return redirect('/vcs-console');

    }

     //Delete Data
     function deleteCredentials($id){

        $data= VCSConsole::find($id);
        $data->delete();  
        return redirect('/vcs-console');     

    }
    
    
    

    // $VCSConsole = VCSConsole::where('token_id', $accessToken->id)->get(['instanceId', 'publicDnsName', 'token_id']);

    public function index()
    {
        echo "index";
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        echo "create";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Get the user token from the request
        $userToken = request()->bearerToken();

        // Query personal access tokens table
        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        // Check if access token is valid
        if ($accessToken) {
            // Fetch instances from the database
           
            $VCSConsole = VCSConsole::all('instanceId', 'publicDnsName', 'token_id', 'status')->where('token_id', $accessToken->id)->where('status', '0');

            // Initialize variables to track whether any valid instance is found
            $validInstanceFound = false;
            $responseData = [];

            // Iterate through each instance
            foreach ($VCSConsole as $instance) {
                $instanceId = $instance->instanceId;

                // Make a POST request to the external API for the current instance
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $userToken,
                ])->post("https://webservices.immensive.it/api/ec2/{$instanceId}");

                // Check the response content
                $responseContent = $response->json();

                if (isset($responseContent['instanceId']) && $responseContent['instanceId'] === $instanceId) {

                    $VCSConsole = VCSConsole::where('instanceId', $instanceId)->first();
                    $VCSConsole->status=1;
                    $VCSConsole->save();

                    // Valid instance found, update the response data
                    $responseData['instanceId'] = $instanceId;
                    $responseData['publicDnsName'] = $instance->publicDnsName;
                    $validInstanceFound = true;
                    break; // Exit the loop since a valid instance is found
                }
            }

            if (!$validInstanceFound) {
                // No valid instance found
                return response()->json(['error' => 'No server available'], 404);
            }



            // Return the response with instanceId and publicDnsName
            return response()->json($responseData);

        } else {
            // Handle the case when the access token is not valid
            // For example, you can return an error response
            return response()->json(['error' => 'Invalid access token'], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $instanceId)
    {

        // Get the user token from the request
        $userToken = request()->bearerToken();

        // Query personal access tokens table
        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        // Check if access token is valid
        if ($accessToken) {

            $VCSConsole = VCSConsole::where('instanceId', $instanceId)->select('status')->first();

            return [
                "status" => $VCSConsole->status,
            ];

        }else {
            // Handle the case when the access token is not valid
            // For example, you can return an error response
            return response()->json(['error' => 'Invalid access token'], 401);
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VCSConsole $vCSConsole)
    {
        echo "edit";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $instanceId)
    {
        
        // Get the user token from the request
        $userToken = request()->bearerToken();

        // Query personal access tokens table
        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        // Check if access token is valid
        if ($accessToken) {

           // Make a POST request to the external API for the current instance
           $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $userToken,
            ])->post("https://webservices.immensive.it/api/ec2/boot/{$instanceId}");

            // $VCSConsole = VCSConsole::where('instanceId', $instanceId)->first();
            // $VCSConsole->status=0;
            // $VCSConsole->save();
            
            // Response content
            return response()->json(['message' => 'Server is Rebooting'], 200);

        }else {
            return response()->json(['error' => 'Invalid access token'], 401);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VCSConsole $vCSConsole)
    {
        echo "destroy";
    }

    //STOP Instance
    public function stopInstance($instanceId) {

        // Get the user token from the request
        $userToken = request()->bearerToken();

        // Query personal access tokens table
        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        // Check if access token is valid
        if ($accessToken) {

            // Make a POST request to the external API for the current instance
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $userToken,
            ])->post("https://webservices.immensive.it/api/ec2/off/{$instanceId}");

            $VCSConsole = VCSConsole::where('instanceId', $instanceId)->first();
            $VCSConsole->status='0';
            $VCSConsole->save();
            
            // Response content
            return response()->json(['message' => 'Server is Stopping'], 200);

        }else {
            return response()->json(['error' => 'Invalid access token'], 401);
        }

    }

}
