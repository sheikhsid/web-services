<?php

namespace App\Http\Controllers;

use App\Models\Screenshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScreenshotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Return a JSON response of all screenshots
        $screenshots = Screenshot::all();
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
            'file' => 'required|image|mimes:bmp|max:30720', 
        ]);

        // Handle file upload
        $file = $request->file('file');
        $path = $file->store('screenshots');

        // Create a new Screenshot record
        $screenshot = new Screenshot();
        $screenshot->file_path = $path;
        $screenshot->save();

        return response()->json(['message' => 'Screenshot uploaded successfully']);
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
