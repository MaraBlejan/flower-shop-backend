<?php

namespace App\Http\Controllers;

use App\Models\Bouquet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BouquetController extends Controller
{
    public function index()
    {
        $bouquets = Bouquet::with('category', 'flowers')->get();
        return response()->json($bouquets);
    }

    public function show($id)
    {
        $bouquet = Bouquet::with(['flowers', 'category'])->find($id);
        if (!$bouquet) {
            return response()->json(['message' => 'The bouquet does not exist! '], 404);
        }
        return response()->json($bouquet);
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
            'flowers' => 'required|array|min:1',
            'flowers.*.id' => 'required|exists:flowers,id',
            'flowers.*.quantity' => 'required|integer|min:1'
        ]);

        return DB::transaction(function () use ($request, $validated) {


            $path = null;
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('bouquets', 'public');
            }


            $bouquet = Bouquet::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'category_id' => $validated['category_id'],
                'image_url' => $path,
                'is_available' => $request->is_available ?? true,
            ]);

            foreach ($validated['flowers'] as $flowerItem) {
                $bouquet->flowers()->attach($flowerItem['id'], [
                    'quantity' => $flowerItem['quantity']
                ]);
            }

            return response()->json($bouquet->load('flowers'), 201);
        });
    }

    public function update(Request $request, $id)
    {
        $bouquet = Bouquet::find($id);
        if (!$bouquet) {
            return response()->json(['message' => 'The bouquet does not exist!'], 404);
        }


        $validated = $request->validate([
            'name' => 'sometimes|string',
            'description' => 'sometimes|string|nullable',
            'price' => 'sometimes|numeric',
            'category_id' => 'sometimes|exists:categories,id',
            'image' => 'sometimes|image|max:2048',
            'flowers' => 'sometimes|array',
            'flowers.*.id' => 'required_with:flowers|exists:flowers,id',
            'flowers.*.quantity' => 'required_with:flowers|integer|min:1'
        ]);


        if ($request->hasFile('image')) {

            if ($bouquet->image_url) {
                Storage::disk('public')->delete($bouquet->image_url);
            }

            $path = $request->file('image')->store('bouquets', 'public');
            $bouquet->image_url = $path;
        }


        $bouquet->update($request->only(['name', 'description', 'price', 'category_id', 'is_available']));

        if ($request->has('flowers')) {
            $syncData = [];
            foreach ($request->flowers as $item) {
                $syncData[$item['id']] = ['quantity' => $item['quantity']];
            }
            $bouquet->flowers()->sync($syncData);
        }

        return response()->json($bouquet->load('flowers'));
    }

    public function destroy($id)
    {
        $bouquet = Bouquet::find($id);

        if (!$bouquet) {
            return response()->json(['message' => 'The bouquet was not found!'], 404);
        }

        $bouquet->flowers()->detach();

        if ($bouquet->image_url) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($bouquet->image_url);
        }

        $bouquet->delete();

        return response()->json(null, 204);
    }
}
