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
        $fileName = 'compressed_' . time() . '.jpg'; // Change the extension to jpeg for compression

        // Compress and save the image
        $compressedImage = Image::make($file);
        $compressedImage->resize(800, 600); // Resize before saving
        $compressedImage->interlace(true); // Enable progressive scan
        $compressedImage->encode('jpg', 50); // Adjust quality as needed (0-100)
        $compressedImage->save(storage_path('app/screenshots/' . $fileName));

        // Create a new Screenshot record
        $screenshot = new Screenshot();
        $screenshot->file_path = 'screenshots/' . $fileName;
        $screenshot->save();

        return response()->json(['message' => 'Screenshot uploaded and compressed successfully']);
    }





    /**
     * Display the specified resource.
     */
    public function show($filename)
    {
        $path = storage_path("app/screenshots/{$filename}");
        return response()->download($path);
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
    public function destroy(Screenshot $screenshot)
    {
        // Delete the file from storage
        Storage::delete($screenshot->file_path);

        // Delete the record from the database
        $screenshot->delete();

        return response()->json(['message' => 'Screenshot deleted successfully']);
    }
}
