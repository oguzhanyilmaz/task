<?php
namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'orderCode' => 'required',
            'productId' => 'required',
            'quantity' => 'required',
            'address' => 'required',
            'shippingDate' => 'required|date|date_format:Y-m-d'
        ]);

        $order = new Orders();

        $order->orderCode = $request->orderCode;
        $order->productId = $request->productId;
        $order->quantity = $request->quantity;
        $order->address = $request->address;
        $order->shippingDate = $request->shippingDate;
        $order->userId = Auth::user()->getAuthIdentifier();

        if($order->save()){
            return response()->json('Order is added.');
        }else{
            return response()->json('Error.');
        }
    }

    public function update(Request $request, $orderId)
    {
       $today = date('Y-m-d');

        $request->validate([
            'orderCode' => 'required',
            'productId' => 'required',
            'quantity' => 'required',
            'address' => 'required',
            'shippingDate' => 'required|date|date_format:Y-m-d'
        ]);

        $order = Orders::find($orderId);

        if($today < $order->shippingDate){
            $order->orderCode = $request->orderCode;
            $order->productId = $request->productId;
            $order->quantity = $request->quantity;
            $order->address = $request->address;
            $order->shippingDate = $request->shippingDate;

            if($order->save()){
                return response()->json('Order is updated.');
            }else{
                return response()->json('Error.');
            }
        }else{
            return response()->json('Shipping date is expired.');
        }
    }

    public function detail($orderId)
    {
        $userId = Auth::user()->getAuthIdentifier();
        $order = Orders::where('id', '=', $orderId)->where('userId', '=', $userId)->first();
        if($order){
            return response()->json($order->getAttributes());
        }else{
            return response()->json('Error.');
        }
    }

    public function list()
    {
        $userId = Auth::user()->getAuthIdentifier();
        $orders = Orders::where('userId', '=', $userId)->get();

        if($orders){
            return response()->json($orders);
        }else{
            return response()->json('Error.');
        }
    }
}
