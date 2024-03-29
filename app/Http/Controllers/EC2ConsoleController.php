<?php

namespace App\Http\Controllers;

use App\Models\EC2Console;
use App\Models\EC2Instance;
use App\Models\APIActivity;
use Aws\Ec2\Ec2Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EC2ConsoleController extends Controller
{
    
   /**
     * Opration of AWS EC2 Console
     */ 

    function getCredentials(){
        
        $EC2Consoles = EC2Console::all()->where('user', Auth::user()->id);
        $accessTokens = \DB::table('personal_access_tokens')->where('tokenable_id', Auth::user()->id)->select('id', 'name')->get();
    
        return view('aws.ec2.ec2-console', compact('EC2Consoles', 'accessTokens'));
    }    
    
    //Check validation and add product
    function addCredentials(Request $req){

        $req->validate([
            'token_id'=>'required | max:225',
            'key'=>'required | max:225',
            'secret'=>'required | max:225',          
            'template'=>'required | max:225'
        ]);

        $credential= new EC2Console;
        $credential->user=Auth::user()->id;
        $credential->token_id=$req->token_id;
        $credential->key=$req->key;
        $credential->secret=$req->secret;
        $credential->template=$req->template;
        $credential->save();

        return redirect('/ec2-console');

    }

     //Delete Data
     function deleteCredentials($id){

        $data= EC2Console::find($id);
        $data->delete();  
        return redirect('/ec2-console');     

    }
    
    
    
    
    
    
    
    
    
    
    /**
     * API Configration.
     */
    public function index()
    {
        // $userToken = request()->bearerToken(); // Assuming the token is sent as a bearer token

        // $accessToken = \DB::table('personal_access_tokens')
        //     ->where('token', hash('sha256', $userToken))
        //     ->first();

        // echo $EC2Consoles = EC2Console::all('token_id','key','secret','template')->where('token_id', $accessToken->id)->first();
        
        
        
        // echo  $accessToken->id;
        // echo  $accessToken->name;
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

        $EC2Console = EC2Console::all('token_id','key','secret','template')->where('token_id', $accessToken->id)->first();

        if (!$EC2Console) {

            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Create Instance";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/ec2";
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "error";
            $a_p_i_activities->save();

            return response()->json(['error' => 'Invalid token for AWS'], 401);
        
        }else {
            
            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Create Instance";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/ec2";
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "success";
            $a_p_i_activities->save();

        }

        // Create a new EC2 client
        $ec2Client = new Ec2Client([
            'region' => 'eu-south-1', // Set the desired AWS region
            'version' => 'latest', // Set the desired AWS SDK version
            'credentials' => [
                'key' => $EC2Console->key,
                'secret' => $EC2Console->secret,
            ],
        ]);

        // Set the desired values for MaxCount and MinCount
        $maxCount = 1;
        $minCount = 1;

        // Launch the EC2 instances
        $result = $ec2Client->runInstances([
            'MaxCount' => $maxCount,
            'MinCount' => $minCount,
            'LaunchTemplate' => [
                'LaunchTemplateId' => $EC2Console->template, // Replace with the desired Launch Template ID
                'Version' => '1', // Replace with the desired Launch Template version
            ],
        ]);
        
        // Retrieve the instance information
        $instanceId = $result->get('Instances')[0]['InstanceId']; // Assuming only one instance is launched

        // Wait for the instance to reach the running state
        $ec2Client->waitUntil('InstanceRunning', [
            'InstanceIds' => [$instanceId],
        ]);

        // Describe the instance to get its Public IPv4 address and DNS
        $result = $ec2Client->describeInstances([
            'InstanceIds' => [$instanceId],
        ]);

        // Get the public IP address, DNS, and instance ID
        $publicIpAddress = $result->get('Reservations')[0]['Instances'][0]['PublicIpAddress'];
        $publicDnsName = $result->get('Reservations')[0]['Instances'][0]['PublicDnsName'];

        $e_c2_instances= new EC2Instance;
        $e_c2_instances->instanceId=$instanceId;
        $e_c2_instances->publicDnsName="Waiting for URL";
        $e_c2_instances->save();

        // Return the public IP address, DNS, and instance ID in the response
        return response()->json([
            'instanceId' => $instanceId,
            'publicIpAddress' => $publicIpAddress,
            'publicDnsName' => $publicDnsName,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $instanceId)
    {
        $EC2Instance = EC2Instance::where('instanceId', $instanceId)->select('publicDnsName')->first();

        return [
            "publicDnsName" => $EC2Instance->publicDnsName,
        ];
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $instanceId)
    {
        $requestData = $request->all();

        $EC2Instance = EC2Instance::where('instanceId', $instanceId)->first();

        if (!$EC2Instance) {
            return response()->json(['message' => 'Instance not found'], 404);
        }

        $EC2Instance->publicDnsName = $requestData['publicDnsName'];
        $EC2Instance->save();

        return response()->json(['message' => 'Instance updated successfully'], 200);
    }

    //START Instance
    public function startInstance($instanceId) {

        $userToken = request()->bearerToken(); // Assuming the token is sent as a bearer token

        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        $EC2Console = EC2Console::all('token_id','key','secret')->where('token_id', $accessToken->id)->first();

        if (!$EC2Console) {

            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Start Instance";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/ec2/".$instanceId;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "error";
            $a_p_i_activities->save();

            return response()->json(['error' => 'Invalid token for AWS'], 401);
        
        }else {
            
            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Start Instance";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/ec2/".$instanceId;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "success";
            $a_p_i_activities->save();

        }        

        try {
            // Create a new EC2 client
            $ec2Client = new Ec2Client([
                'region' => 'eu-south-1', // Set the desired AWS region
                'version' => 'latest', // Set the desired AWS SDK version
                'credentials' => [
                    'key' => $EC2Console->key,
                    'secret' => $EC2Console->secret,
                ],
            ]);

            // Check the current state of the instance
            $instanceInfo = $ec2Client->describeInstances([
                'InstanceIds' => [$instanceId],
            ]);

            $currentState = $instanceInfo->get('Reservations')[0]['Instances'][0]['State']['Name'];

            if ($currentState !== 'stopped') {
                return response()->json(['error' => 'Current state: ' . $currentState], 400);
            }

            // Start the EC2 instance
            $result = $ec2Client->startInstances([
                'InstanceIds' => [$instanceId],
            ]);

            // Wait for the instance to reach the running state (optional)
            $ec2Client->waitUntil('InstanceRunning', ['InstanceIds' => [$instanceId]]);

            // Retrieve public IP address and public DNS name of the instance
            $instanceInfo = $ec2Client->describeInstances([
                'InstanceIds' => [$instanceId],
            ]);

            

            if (!isset($instanceInfo['Reservations'][0]['Instances'][0])) {
                return response()->json(['error' => 'Instance information not available.'], 500);
            }

            $publicIpAddress = $instanceInfo->get('Reservations')[0]['Instances'][0]['PublicIpAddress'];
            $publicDnsName = $instanceInfo->get('Reservations')[0]['Instances'][0]['PublicDnsName'];

            return response()->json([
                'instanceId' => $instanceId,
                'publicIpAddress' => $publicIpAddress,
                'publicDnsName' => $publicDnsName,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to start the instance: ' . $e->getMessage()], 500);
        }

        $e_c2_instance = EC2Instance::where('instanceId', $instanceId)->first();

            if ($e_c2_instance) {
                $e_c2_instance->publicDnsName = "Waiting for URL";
                $e_c2_instance->save();
            } else {
                // Handle case where instance with given instanceId was not found
            }
    }
    
    

    //STOP Instance
    public function stopInstance($instanceId) {

        $userToken = request()->bearerToken(); // Assuming the token is sent as a bearer token

        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        $EC2Console = EC2Console::all('token_id','key','secret')->where('token_id', $accessToken->id)->first();
        
        if (!$EC2Console) {

            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Stop Instance";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/ec2/off/".$instanceId;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "error";
            $a_p_i_activities->save();

            return response()->json(['error' => 'Invalid token for AWS'], 401);
        
        }else {
            
            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Stop Instance";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/ec2/off/".$instanceId;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "success";
            $a_p_i_activities->save();

        }

        try {
            // Create a new EC2 client
            $ec2Client = new Ec2Client([
                'region' => 'eu-south-1', // Set the desired AWS region
                'version' => 'latest', // Set the desired AWS SDK version
                'credentials' => [
                    'key' => $EC2Console->key,
                    'secret' => $EC2Console->secret,
                ],
            ]);
    
            // Stop the EC2 instance
            $result = $ec2Client->stopInstances([
                'InstanceIds' => [$instanceId],
            ]);
    
            // Retrieve the current state of the instance
            $currentState = $result->get('StoppingInstances')[0]['CurrentState']['Name'];
    
            return response()->json(['message' => 'Current state: ' . $currentState], 200);
        } catch (\Exception $e) {
            // Handle the exception if stopping the instance fails
            return response()->json(['error' => 'Failed to stop the instance: ' . $e->getMessage()], 500);
        }
    }

    public function rebootInstance($instanceId) {

        $userToken = request()->bearerToken(); // Assuming the token is sent as a bearer token
    
        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();
    
        $EC2Console = EC2Console::all('token_id','key','secret')->where('token_id', $accessToken->id)->first();

        if (!$EC2Console) {

            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Reboot Instance";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/ec2/boot/".$instanceId;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "error";
            $a_p_i_activities->save();

            return response()->json(['error' => 'Invalid token for AWS'], 401);
        
        }else {
            
            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Reboot Instance";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/ec2/boot/".$instanceId;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "success";
            $a_p_i_activities->save();

        }
    
        try {
            // Create a new EC2 client
            $ec2Client = new Ec2Client([
                'region' => 'eu-south-1', // Set the desired AWS region
                'version' => 'latest', // Set the desired AWS SDK version
                'credentials' => [
                    'key' => $EC2Console->key,
                    'secret' => $EC2Console->secret,
                ],
            ]);
    
            // Reboot the EC2 instance
            $result = $ec2Client->rebootInstances([
                'InstanceIds' => [$instanceId],
            ]);

            if ($result && isset($result['RebootingInstances'][0]['CurrentState']['Name'])) {
                $currentState = $result['RebootingInstances'][0]['CurrentState']['Name'];
                return response()->json(['message' => 'Current state after reboot: ' . $currentState], 200);
            } else {
                return response()->json(['error' => 'Failed to get current state after reboot.'], 500);
            }
        } catch (\Exception $e) {
            // Handle the exception if rebooting the instance fails
            return response()->json(['error' => 'Failed to reboot the instance: ' . $e->getMessage()], 500);
        }
    }    

    
    // Remove the specified resource from storage.

    public function destroy(string $instanceId) {

        $userToken = request()->bearerToken(); // Assuming the token is sent as a bearer token

        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        $EC2Console = EC2Console::all('token_id','key','secret')->where('token_id', $accessToken->id)->first();

        if (!$EC2Console) {

            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Terminate Instance";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/ec2/".$instanceId;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "error";
            $a_p_i_activities->save();

            return response()->json(['error' => 'Invalid token for AWS'], 401);
        
        }else {
            
            //Store API Activities
            $a_p_i_activities = new APIActivity();
            $a_p_i_activities->token_id = $accessToken->id;
            $a_p_i_activities->resource = "Terminate Instance";
            $a_p_i_activities->endpoint = parse_url(url(''), PHP_URL_HOST) . "/ec2/".$instanceId;
            $a_p_i_activities->ip_address = request()->ip();
            $a_p_i_activities->response = "success";
            $a_p_i_activities->save();

        }

        try {
            // Create a new EC2 client
            $ec2Client = new Ec2Client([
                'region' => 'eu-south-1', // Set the desired AWS region
                'version' => 'latest', // Set the desired AWS SDK version
                'credentials' => [
                    'key' => $EC2Console->key,
                    'secret' => $EC2Console->secret,
                ],
            ]);
    
            // Terminate the EC2 instance
            $result = $ec2Client->terminateInstances([
                'InstanceIds' => [$instanceId],
            ]);
    
            // Retrieve the termination state of the instance
            $terminationState = $result->get('TerminatingInstances')[0]['CurrentState']['Name'];
    
            return response()->json(['message' => 'Instance terminated successfully. Termination state: ' . $terminationState], 200);
        } catch (\Exception $e) {
            // Handle the exception if the termination fails
            return response()->json(['error' => 'Failed to terminate the instance: ' . $e->getMessage()], 500);
        }
    }
}
