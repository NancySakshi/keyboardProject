<?php

namespace App\Http\Controllers;

use App\Models\Control;
use App\Models\KeyboardKey;
use Illuminate\Http\Request;

class KeyboardController extends Controller
{

    public function index()
    {
        $control = Control::orderByDesc('created_at')->first();

        $keyboardKeys = KeyboardKey::all();

        return view('keyboard', compact('control', 'keyboardKeys'));
    }

    public function acquireControl(Request $request)
    {
        $currentControl = Control::orderByDesc('created_at')->first();

        if ($currentControl && time() - strtotime($currentControl->created_at) <= 120) {
            return response()->json(['success' => false, 'message' => 'Control already acquired']);
        }

        $user = Control::where('user_id', '=', $request->user_id)->first();

        if ($user != null) {

            Control::create(['user_id' => $request->user_id + 1, 'accoure_control' => 'true']);

        } else {
            Control::create(['user_id' => $request->user_id, 'accoure_control' => 'true']);

        }


        return response()->json(['success' => true]);
    }

    public function updateKeyState(Request $request)
    {
        $keyId = $request->input('key_id');

        $key = KeyboardKey::find($keyId);

        if ($key) {
            $key->state = 1;
            $key->save();

            KeyboardKey::where('id', '<>', $keyId)->update(['state' => 0]);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => true]);
    }

    public function poll(Request $request)
    {
        $keyboardKeys = KeyboardKey::all();
        $control = Control::orderByDesc('created_at')->first();

        return response()->json(['success' => true, 'keyboardKeys' => $keyboardKeys, 'control' => $control]);
    }

}
