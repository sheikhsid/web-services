<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\Ec2\Ec2Client;

class AWSmachineVCS extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        echo "01";
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
                'key' => 'AKIA2HY23QJW5K2ZLLY6',
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
            'publicIpAddress' => $publicIpAddress,
            'publicDnsName' => $publicDnsName,
            'instanceId' => $instanceId,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        echo "03";

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        echo "04";

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        echo "05";

    }
}
