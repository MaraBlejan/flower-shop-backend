<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Bouquet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index()
    {

        $orders = Order::with('bouquets')->orderBy('created_at', 'desc')->get();

        return response()->json($orders);
    }


    public function store(Request $request)
    {

        $validated = $request->validate([

            'user_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.bouquet_id' => 'required|exists:bouquets,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($validated) {


            $order = Order::create([
                'user_id' => $validated['user_id'],
                'status' => 'pending',
                'total_price' => 0,
            ]);

            $calculatedTotal = 0;


            foreach ($validated['items'] as $item) {

                $bouquet = Bouquet::find($item['bouquet_id']);


                $lineTotal = $bouquet->price * $item['quantity'];


                $calculatedTotal += $lineTotal;


                $order->bouquets()->attach($bouquet->id, [
                    'quantity' => $item['quantity'],
                    'price' => $bouquet->price
                ]);
            }


            $order->update(['total_price' => $calculatedTotal]);


            return response()->json($order->load('bouquets'), 201);
        });
    }


    public function show($id)
    {
        $order = Order::with('bouquets')->find($id);

        if (!$order) {
            return response()->json(['message' => 'The order was not found!'], 404);
        }

        return response()->json($order);
    }
}
