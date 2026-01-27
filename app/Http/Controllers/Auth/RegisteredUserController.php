<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserMeta;
use App\Models\UserAvatar;
//use App\Models\UserWorld;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'firstName' => 'required',
            'lastName' => 'required'
        ]);

        $newUid = rand(1111111111, 9999999999);
        $userEx = User::where('uid', '=', $newUid);
        while ($userEx != null){
            $newUid = $newUid = rand(1111111111, 9999999999);;
            $userEx = User::where('uid', '=', $newUid)->first();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'uid' => $newUid
        ]);

        // Create the user meta
        $userMeta = UserMeta::create([
            'uid' => $newUid,
            'firstName' => request('firstName'),
            'lastName' => request('lastName'),
            // the schema specifies additional defaults
        ]);

        $userAvatar = UserAvatar::create([
            'uid' => $newUid,
            'value' => null
        ]);

        // worlds will be created if they don't exist, so this code is redundant
        /* 
        // Unix timestamp in milliseconds
        $plantTime = (float) ((time() * 1000) - 172800000); // pretend 2 days elapsed

        $userWorld = UserWorld::create([
            'uid' => $newUid,
            'type' => 'farm',
            'sizeX' => 48,
            'sizeY' => 48,
            'objects' => serialize(array(
                0 => 
                (object) array(
                    'plantTime' => $plantTime,
                    'position' => 
                    (object) array(
                    'x' => 27,
                    'z' => 0,
                    'y' => 13,
                    ),
                    'isBigPlot' => false,
                    'direction' => 0,
                    'isJumbo' => true,
                    'deleted' => false,
                    'tempId' => -1,
                    'className' => 'Plot',
                    'state' => 'fallow',
                    'instanceDataStoreKey' => NULL,
                    'components' => 
                    (object) array(
                    ),
                    'isProduceItem' => false,
                    'id' => 1,
                    'itemName' => NULL,
                ),
                1 => 
                (object) array(
                    'plantTime' => $plantTime,
                    'position' => 
                    (object) array(
                    'x' => 27,
                    'z' => 0,
                    'y' => 9,
                    ),
                    'isBigPlot' => false,
                    'direction' => 0,
                    'isJumbo' => true,
                    'deleted' => false,
                    'tempId' => -1,
                    'className' => 'Plot',
                    'state' => 'fallow',
                    'instanceDataStoreKey' => NULL,
                    'components' => 
                    (object) array(
                    ),
                    'isProduceItem' => false,
                    'id' => 2,
                    'itemName' => NULL,
                ),
                2 => 
                (object) array(
                    'plantTime' => $plantTime, // finish growing now
                    'position' => 
                    (object) array(
                    'x' => 19,
                    'z' => 0,
                    'y' => 9,
                    ),
                    'isBigPlot' => false,
                    'direction' => 0,
                    'isJumbo' => false,
                    'deleted' => false,
                    'tempId' => -1,
                    'className' => 'Plot',
                    'state' => 'planted',
                    'instanceDataStoreKey' => NULL,
                    'components' => 
                    (object) array(
                    ),
                    'isProduceItem' => false,
                    'id' => 3,
                    'itemName' => 'eggplant',
                ),
                3 => 
                (object) array(
                    'plantTime' => $plantTime,
                    'position' => 
                    (object) array(
                    'x' => 19,
                    'z' => 0,
                    'y' => 13,
                    ),
                    'isBigPlot' => false,
                    'direction' => 0,
                    'isJumbo' => false,
                    'deleted' => false,
                    'tempId' => -1,
                    'className' => 'Plot',
                    'state' => 'planted',
                    'instanceDataStoreKey' => NULL,
                    'components' => 
                    (object) array(
                    ),
                    'isProduceItem' => false,
                    'id' => 4,
                    'itemName' => 'eggplant',
                ),
                4 => 
                (object) array(
                    'plantTime' => NAN,
                    'position' => 
                    (object) array(
                    'x' => 23,
                    'z' => 0,
                    'y' => 9,
                    ),
                    'isBigPlot' => false,
                    'direction' => 0,
                    'isJumbo' => false,
                    'deleted' => false,
                    'tempId' => -1,
                    'className' => 'Plot',
                    'state' => 'plowed',
                    'instanceDataStoreKey' => NULL,
                    'components' => 
                    (object) array(
                    ),
                    'isProduceItem' => false,
                    'id' => 5,
                    'itemName' => NULL,
                ),
                5 => 
                (object) array(
                    'plantTime' => NAN,
                    'position' => 
                    (object) array(
                    'x' => 23,
                    'z' => 0,
                    'y' => 13,
                    ),
                    'isBigPlot' => false,
                    'direction' => 0,
                    'isJumbo' => false,
                    'deleted' => false,
                    'tempId' => -1,
                    'className' => 'Plot',
                    'state' => 'plowed',
                    'instanceDataStoreKey' => NULL,
                    'components' => 
                    (object) array(
                    ),
                    'isProduceItem' => false,
                    'id' => 6,
                    'itemName' => NULL,
                ),
            )),
            'messageManager' => ""
        ]);
        */

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
