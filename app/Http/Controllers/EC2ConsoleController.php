<?php

namespace App\Http\Controllers;

use App\Models\EC2Console;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class EC2ConsoleController extends Controller
{
    
   /**
     * Opration of AWS EC2 Console
     */ 

    function getCredentials(){
        
        $EC2Consoles = EC2Console::all();
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

        return redirect('/aws-ec2');

    }

     //Delete Data
     function deleteCredentials($id){

        $data= Product::find($id);
        $data->delete();  
        return redirect('admin/products');      

    }
    
    
    
    
    
    
    
    
    
    
    /**
     * API Configration.
     */
    public function index()
    {
        $userToken = request()->bearerToken(); // Assuming the token is sent as a bearer token

        $accessToken = \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $userToken))
            ->first();

        echo  $accessToken->id;
        echo  $accessToken->name;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Create a new EC2 client
        $ec2Client = new Ec2Client([
            'region' => 'eu-south-1', // Set the desired AWS region
            'version' => 'latest', // Set the desired AWS SDK version
            'credentials' => [
                'key' => 'AKIA2HY23QJWYEAULCGA',
                'secret' => '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',
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
                'LaunchTemplateId' => 'lt-0bdb883466bd7d7d9', // Replace with the desired Launch Template ID
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

        // Return the public IP address, DNS, and instance ID in the response
        return response()->json([
            'instanceId' => $instanceId,
            // 'publicIpAddress' => $publicIpAddress,
            'publicDnsName' => $publicDnsName,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        echo "Sorry, This method is not available";

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        echo "Sorry, This method is not available";

    }

    
    // Remove the specified resource from storage.

    public function destroy(string $instanceId) {
        try {
            // Create a new EC2 client
            $ec2Client = new Ec2Client([
                'region' => 'eu-south-1', // Set the desired AWS region
                'version' => 'latest', // Set the desired AWS SDK version
                'credentials' => [
                    'key' => 'AKIA2HY23QJWYEAULCGA',
                    'secret' => '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',
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


    //START Instance
    public function startInstance($instanceId) {
        try {
            // Create a new EC2 client
            $ec2Client = new Ec2Client([
                'region' => 'eu-south-1', // Set the desired AWS region
                'version' => 'latest', // Set the desired AWS SDK version
                'credentials' => [
                    'key' => 'AKIA2HY23QJWYEAULCGA',
                    'secret' => '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',
                ],
            ]);
    
            // Check the current state of the instance
            $instanceInfo = $ec2Client->describeInstances([
                'InstanceIds' => [$instanceId],
            ]);
    
            $currentState = $instanceInfo->get('Reservations')[0]['Instances'][0]['State']['Name'];
    
            if ($currentState !== 'stopped') {
                throw new \Exception('The instance is not in a state from which it can be started.');
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
    
            $publicIpAddress = $instanceInfo->get('Reservations')[0]['Instances'][0]['PublicIpAddress'];
            $publicDnsName = $instanceInfo->get('Reservations')[0]['Instances'][0]['PublicDnsName'];
    
            return response()->json([
                'instanceId' => $instanceId,
                // 'publicIpAddress' => $publicIpAddress,
                'publicDnsName' => $publicDnsName,
                // 'message' => 'Instance started successfully. Current state: ' . $currentState,
            ], 200);
        } catch (\Exception $e) {
            // Handle the exception if starting the instance fails
            return response()->json(['error' => 'Failed to start the instance: ' . $e->getMessage()], 500);
        }
    }     
    

    //STOP Instance
    public function stopInstance($instanceId) {
        try {
            // Create a new EC2 client
            $ec2Client = new Ec2Client([
                'region' => 'eu-south-1', // Set the desired AWS region
                'version' => 'latest', // Set the desired AWS SDK version
                'credentials' => [
                    'key' => 'AKIA2HY23QJWYEAULCGA',
                    'secret' => '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',
                ],
            ]);
    
            // Stop the EC2 instance
            $result = $ec2Client->stopInstances([
                'InstanceIds' => [$instanceId],
            ]);
    
            // Retrieve the current state of the instance
            $currentState = $result->get('StoppingInstances')[0]['CurrentState']['Name'];
    
            return response()->json(['message' => 'Instance stopped successfully. Current state: ' . $currentState], 200);
        } catch (\Exception $e) {
            // Handle the exception if stopping the instance fails
            return response()->json(['error' => 'Failed to stop the instance: ' . $e->getMessage()], 500);
        }
    }
}
