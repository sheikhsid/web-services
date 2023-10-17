<?php

namespace App\Http\Controllers;

use App\Models\Screenshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class ScreenshotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Return a JSON response of all screenshots
        $screenshots = Screenshot::all('id','file_path');
        return response()->json($screenshots);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //Empty
        echo "Function is Empty";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'file' => 'required|string', // Assuming 'file' is the key for the Base64 encoded string
        ]);

        // Get the base64 encoded file content from the request
        $base64File = $request->input('file');

        // Convert the base64 data to a file
        $file = base64_decode($base64File);

        // Generate a unique file name
        $fileName = 'screenshot_' . time() . '.jpg';

        // Save the image
        file_put_contents(storage_path('app/screenshots/' . $fileName), $file);

        // Create a new Screenshot record
        $screenshot = new Screenshot();
        $screenshot->file_path = $fileName;
        $screenshot->save();

        return response()->json(['message' => 'Screenshot uploaded successfully', 'id' => $screenshot->id]);
    }







    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $image = Screenshot::findOrFail($id);
        $path = storage_path("app/screenshots/{$image->file_path}");
        
        // Read the file
        $fileContents = file_get_contents($path);
        
        // Encode the file contents as base64
        $base64 = base64_encode($fileContents);

        return response()->json([
            'file' => $base64
        ]);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Screenshot $screenshot)
    {
        //Empty
        echo "Function is Empty edit";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Screenshot $screenshot)
    {
        //Empty
        echo "Function is Empty";
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $screenshot = Screenshot::find($id);

        if (!$screenshot) {
            return response()->json(['message' => 'Screenshot not found'], 404);
        }

        // Delete the file from storage
        Storage::delete($screenshot->file_path);

        // Delete the record from the database
        $screenshot->delete();

        return response()->json(['message' => 'Screenshot deleted successfully']);
    }

}
