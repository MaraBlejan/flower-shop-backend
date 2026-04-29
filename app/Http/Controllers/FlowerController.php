<?php

namespace App\Http\Controllers;

use App\Models\Flower;
use http\Env\Response;
use Illuminate\Http\Request;

class FlowerController extends Controller
{
    public function index()
    {
        $flowers = Flower::all();
        return response()->json($flowers, 200);
    }

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:50',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable',
        ]);

        $imageName = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName);
        }

        $flower = Flower::create([
            'name' => $validatedData['name'],
            'color' => $validatedData['color'],
            'quantity' => $validatedData['quantity'],
            'image_url' => $imageName,
        ]);

        return response()->json($flower, 201);
    }
    public function show($id){
        $flower=Flower::find($id);
        if(!$flower){
            return response()->json(['message'=>'The flower was not found!'],404);
        }
        return response()->json($flower,200);
    }
    public function update(Request $request, $id)
    {
        $flower = Flower::find($id);

        if (!$flower) {
            return response()->json(['message' => 'The flower was not found!'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'color' => 'sometimes|string|max:50',
            'quantity' => 'sometimes|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {

            if ($flower->image_url && file_exists(public_path('images/' . $flower->image_url))) {
                unlink(public_path('images/' . $flower->image_url));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName);

            $validatedData['image_url'] = $imageName;
        }

        unset($validatedData['image']);

        $flower->update($validatedData);

        return response()->json($flower, 200);
    }

    public function destroy($id)
    {
        $flower = Flower::find($id);

        if (!$flower) {
            return response()->json(['message' => 'The flower was not found!'], 404);
        }

        $flower->delete();

        return response()->json(null, 204);
    }
    }
