<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\Events\ResetList;
use App\Models\User;
use DB;
use File;
use Mail;

class UserController extends Controller
{
    public function show()
    {
        if(request()->ajax())
        {
            return datatables()->of( User::all() )
                    ->addColumn('action', function($data){
                        $button =   '<div class="td-actions text-center">
                                        <a rel="tooltip" name="edit" id="'.$data->id.'" 
                                            class="edit btn btn-secondary" href="/edit/'.$data->id.'">
                                            <small><i class="fas fa-pencil-alt"></i></small>
                                        </a>
                                    </div>';
                        $button .= '<div class="td-actions text-center">
                                        <a rel="tooltip" name="delete" id="'.$data->id.'" data-name="'.$data->name.'" class="delete btn btn-warning">
                                            <small><i class="fas fa-trash-alt"></i></small>
                                        </a>
                                    </div>';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }

    public function create()
    {
        $user = "none";
        return view('form', compact('user'));
    }
    
    public function edit($id)
    {
        $user = User::find($id);
        return view('form', compact('user'));  
    }
    
    public function store(Request $request)
    {
        if ($request->process == 'Add') {
            $rules = array(
                // 'picture'        => 'image|max:2048',
                'name'         => 'required',
                'email'        => 'required|email|unique:users',
                'password'     => 'required|string|min:8|confirmed',
            );
    
            $validator = Validator::make($request->all(), $rules);
    
            if($validator->fails())
            {
                return response()->json(['errors' => $validator->errors()->all()]);
            }
    
            if($request->hasFile('picture')){
                // Get filename with the extension
                $filenameWithExt = $request->file('picture')->getClientOriginalName();
                // Get just filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                // Get just ext
                $extension = $request->file('picture')->getClientOriginalExtension();
                // Filename to store
                $fileNameToStore= 'TVC_'.time().'.'.$extension;
                
                $request->file('picture')->move(public_path('storage/pics'), $fileNameToStore);
            
            } else {
                $fileNameToStore = 'blank.PNG';
            }
    
            $data = User::create([
                'name'          => $request->name,
                'picture'       => $fileNameToStore,
                'email'         => $request->email,
                'password'      => Hash::make($request['password']),
            ]);

            $text = "Record Added";

            $details = [
                'name' => $request->name,
                'email' => $request->email
            ];
            Mail::send('email.sendMail', $details, function($message) use ($details) {
               $message->to($details['email'], $details['name'])->subject('Registration successfully');
               $message->from('test@gmail.com','Angelo Cenidoza');
            });
            
            broadcast(new ResetList($details))->toOthers();

        } else {

            $id = $request->id;
            $activity = $request->activity;

            $rules = array(
                'name'        => 'required',
                'email'        => 'required|email|unique:users,email,'.$id,
            );
    
            $validator = Validator::make($request->all(), $rules);
    
            if($validator->fails())
            {
                return response()->json(['errors' => $validator->errors()->all()]);
            }
    
            $pictureOld = $request->pictureOld;
            if($request->hasFile('picture')){

                // Get filename with the extension
                $filenameWithExt = $request->file('picture')->getClientOriginalName();
                // Get just filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                // Get just ext
                $extension = $request->file('picture')->getClientOriginalExtension();

                if ($pictureOld != 'blank.PNG') {
                    $image_path = 'storage/pictures/'.$pictureOld;  // Value is not URL but directory file path
                    if(File::exists($image_path)) {
                        File::delete($image_path);
                    }
                }

                // Filename to store
                $fileNameToStore= 'TVC_'.time().'.'.$extension;
                
                $request->file('picture')->move(public_path('storage/pics'), $fileNameToStore);
            
            } else {
                $fileNameToStore = $pictureOld;
            }
    
            $data = User::find($id)->update([
                'name'          => $request->name,
                'picture'       => $fileNameToStore,
                'email'         => $request->email,
                // 'password'      => Hash::make($request['password']),
            ]);

            $text = "Record Updated!";
            
        }

        $process = $request->process;

        return response()->json([ 'success' => $text, compact('process') ]);
    }
    
    public function location(Request $request)
    {
        // SAVE LOCATION
        $long = $request->long;
        $lat = $request->lat;
        $token = $request->token;

        $curl = curl_init('https://us1.locationiq.com/v1/reverse.php?key=' . $token . '&lat=' . $lat . '&lon=' . $long . '&format=json');

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER    =>  true,
            CURLOPT_FOLLOWLOCATION    =>  true,
            CURLOPT_MAXREDIRS         =>  10,
            CURLOPT_TIMEOUT           =>  30,
            CURLOPT_CUSTOMREQUEST     =>  'GET',
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return 'cURL Error #:' . $err;
        } else {
            $location = json_decode($response);
            return $location;
        }

    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();

        return response()->json('Record deleted', 200);
    }
}
