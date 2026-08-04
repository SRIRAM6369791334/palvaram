<?php

namespace App\Http\Controllers;

use App\Models\DeliveryPerson;
use App\Models\MilkOrder;
use App\Models\MilkOrderUserAddress;
use App\Models\MilkSlot;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(Request $request)
    {
        $orderId = "MV-ORD-000006";
        $user = User::query()->where("user_id", "MV-CUS-00002")->first();

        $defaultUserAddress =  $user->defaultAddress->toArray();


         MilkOrderUserAddress::create([
            ...$defaultUserAddress,
            "order_id" => $orderId,
        ]);

    

        $userDefaultAddressId  = $user->user_default_address_id;
        $userDefaultAddress =  UserAddress::query()->findOrFail($userDefaultAddressId);

        $areaId = $userDefaultAddress["area_id"];
        $deliveryPerson = DeliveryPerson::with(['areaAssigns', 'milkOrders'])
            ->whereHas('areaAssigns', function ($query) use ($areaId) {
                $query->where('area_id', $areaId);
            })
            ->withCount('milkOrders')
            ->get();
        // ->orderBy('milk_orders_count', 'asc')
        // ->first();

        dd($deliveryPerson);
        if ($deliveryPerson) {
            $milkOrder =   MilkOrder::query()->where("order_id", $orderId)->first();
            $milkSlot =  MilkSlot::query()->where("order_id", $orderId);
            $milkOrder->update([
                "delivery_person_id" => $deliveryPerson->delivery_person_id,
                "is_delivery_assigned" => 1
            ]);
            $milkSlot->update([
                "deliver_person_id" => $deliveryPerson->delivery_person_id
            ]);
        }


        return  "hi";
    }
}
