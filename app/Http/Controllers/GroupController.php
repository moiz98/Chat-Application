<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use App\Group;
use App\Group_user;
use App\Group_read;
use App\Contact;
use App\Events\GroupCreated;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function UpdateGroup(Request $request)
    {
        if ($request->file) {
            $filename = $request->file->store('/public/avatars');
            $filename = substr($filename,15);
            if($request->contact_desc == 'null')
            {
                $updated = Group::where('id',$request->contact_id)->update(['name'=> $request->contact_name, 'profile_image' => $filename]);
                return response()->json($updated);
            }
            else {
                $updated = Group::where('id',$request->contact_id)->update(['name'=> $request->contact_name,'description' => $request->contact_desc,'profile_image' => $filename]);
                return response()->json($updated);
            }
        }
        else {
            if($request->contact_desc == 'null')
            {
                $updated = Group::where('id',$request->contact_id)->update(['name'=> $request->contact_name]);
                return response()->json($updated);
            }
            else {
                $updated = Group::where('id',$request->contact_id)->update(['name'=> $request->contact_name,'description' => $request->contact_desc]);
                return response()->json($updated);
            }
        }
    }

    public function getParticipant()
    {
        $contactids = Contact::where('user_id','=', auth()->id() )->get();
        $contacts = [];
        foreach ($contactids as $contid) {
            $contact = User::where('id', '=', $contid->user_contact_id)->first();
            $contacts[] = $contact;
        }
        
        // get all users except the authenticated one
        // $contacts = User::where('id', '!=', auth()->id())->get();                
        return response()->json($contacts);
    }

    public function store(Request $request)
    {
        $group = Group::create([
            'name' => $request->classname,
            'creator' => $request->creator
        ]);
        $participants = [];
        array_push($participants,$request->creator);

        $group_creator = Group_user::create([
            'group_id' => $group->id,
            'user_id' => $request->creator
        ]);
        foreach ($request->participant as $participant) {
            Group_user::create([
                'group_id' => $group->id,
                'user_id' => $participant
            ]);
            array_push($participants,$participant);
        }
        
        $newgroup = ['group'=>$group,'participants'=>$participants];

        broadcast(new GroupCreated($newgroup))->toOthers(); 

        $gg = Group::where('id',$group->id)->first();
        $gg->msg_time = 0;
        $gg->unread = 0;
        return response()->json($gg);
    }

    public function getGroupforUser($id)
    {
        $groups = DB::table('groups')
            ->join('group_users', 'groups.id', '=', 'group_users.group_id')
            ->where('group_users.user_id',$id)
            ->select('groups.*')
            ->get();
        
        // return response()->json($groups);
        
        // attach unread count for this user
        // get a collection of items where group_id is the user who sent us a message
        // and messages_count is the number of unread messages we have for that group
        $unreadIds = Group_read::select(\DB::raw('`group_id` as group_id, count(`msg_id`) as messages_count, max(`created_at`) as created_at'))
            ->where('user_id', auth()->id())
            ->where('read', false)
            ->groupBy('group_id')
            ->get();

        $last_msg_rcvd = Group_read::select(\DB::raw('`group_id` as group_id, max(`created_at`) as created_at'))
                        ->where('user_id', auth()->id())
                        ->where('read', true)
                        ->groupBy('group_id')
                        ->get();
        // add an unread key to each contact with the count of unread messages
        $groups = $groups->map(function($group) use ($unreadIds, $last_msg_rcvd) {
            $groupUnread = $unreadIds->where('group_id', $group->id)->first();
            $last_msg = $last_msg_rcvd->where('group_id', $group->id)->first();

            $group->unread = $groupUnread ? $groupUnread->messages_count : 0;
            if ($groupUnread) {
                $group->msg_time = strtotime($groupUnread->created_at);
            }
            else{
                if ($last_msg) {
                    $group->msg_time = strtotime($last_msg->created_at);
                } else {
                    $group->msg_time = 0;
                }   
            }
            return $group;
        });
        
        return response()->json($groups);               
    }

    public function removePersonfromGroup(Request $request)
    {
        $deleted = Group_user::where([
            ['group_id', '=', $request->group_id],
            ['user_id', '=', $request->user_id],
        ])->delete();
        return response()->json($deleted);
    }
}
