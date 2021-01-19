<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use Image;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function logout()
    {
        Auth::logout();
    }

    public function profile()
    {
        return view('profile', array('user' => Auth::user()));
    }
    public function update_profile(Request $request)
    {
        if ($request->file) {
            $filename = $request->file->store('/public/avatars');
            $filename = substr($filename,15);
            if($request->user_status == 'null')
            {
                $updated = User::where('id',$request->user_id)->update(['name'=> $request->user_name, 'profile_image' => $filename]);
                return response()->json($updated);
            }
            else {
                $updated = User::where('id',$request->user_id)->update(['name'=> $request->user_name,'status' => $request->user_status,'profile_image' => $filename]);
                return response()->json($updated);
            }
        }
        else {
            if($request->user_status == 'null')
            {
                $updated = User::where('id',$request->user_id)->update(['name'=> $request->user_name]);
                return response()->json($updated);
            }
            else {
                $updated = User::where('id',$request->user_id)->update(['name'=> $request->user_name,'status' => $request->user_status]);
                return response()->json($updated);
            } 
        }
        
        // if($request->hasFile('profile_image'))
        // {
        //     $avatar = $request->file('profile_image');
        //     $filename = time().'.'.$avatar->getClientOriginalExtension();
        //     Image::make($avatar)->resize(300,300)->save( public_path('/uploads/avatars/'. $filename) );

        //     $user = Auth::user();
        //     $user->profile_image = $filename;
        //     $user->save();

        //     return view('profile', array('user' => Auth::user()));
        // }
    }
}
