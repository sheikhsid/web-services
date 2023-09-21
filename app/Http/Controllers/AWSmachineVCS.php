<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        //
        echo "02";

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
