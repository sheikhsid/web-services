<?php

namespace App\Http\Controllers;

use App\Models\VCSConsole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VCSConsoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // $VCSConsole = VCSConsole::where('token_id', $accessToken->id)->get(['instanceId', 'publicDnsName', 'token_id']);

    public function index()
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
        echo "store";
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

            $VCSConsole = VCSConsole::where('instanceId', $instanceId)->first();
            $VCSConsole->status=1;
            $VCSConsole->save();
            
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
     * Remove the specified resource from storage.
     */
    public function destroy(VCSConsole $vCSConsole)
    {
        echo "destroy";
    }
}
