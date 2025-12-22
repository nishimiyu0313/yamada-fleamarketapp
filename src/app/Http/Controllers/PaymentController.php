<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Payment;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function  index($id)
    {

        $item = Item::find($id);
        $user = Auth::user();
        if (!$user) {

            return redirect()->route('login');
        }

        $profile = $user->profile;

        return view('payment.purchase', compact('item', 'user', 'profile'));
    }
    public function payment(PurchaseRequest $request, $item_id)
    {

        $item = Item::find($item_id);

        if (!$item) {

            abort(404);
        }

        if ($item->is_sold) {

            return redirect()->back()->withErrors(['msg' => 'この商品は既に購入済みです。']);
        }

        $user = Auth::user();

        $payment = Payment::where('user_id', $user->id)
            ->where('item_id', $item_id)
            ->first();

        if ($payment) {
            $payment->update([
                'content' => $request->content,
                'status' => Payment::STATUS_COMPLETED,
            ]);
        } else {

            $profile = $user->profile;

            Payment::create([
                'user_id'     => $user->id,
                'item_id'     => $item_id,
                'content'     => $request->content,
                'postal_code' => $profile->postal_code,
                'address'     => $profile->address,
                'building'    => $profile->building,
                'status'      => Payment::STATUS_COMPLETED,
            ]);
        }

        $item->is_sold = true;

        $item->save();

        return redirect('/');
    }
}
