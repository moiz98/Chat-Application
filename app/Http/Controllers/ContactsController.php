<?php

namespace App\Http\Controllers;

use App\User;
use App\Message;
use App\Group;
use App\Group_user;
use App\Group_read;
use App\Contact;
use App\Events\NewMessage;
use App\Events\GroupMessage;
use Illuminate\Http\Request;

class ContactsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function removeContact(Request $request)
    {
        $deleted = Contact::where([
            ['user_id', '=', $request->user_id],
            ['user_contact_id', '=', $request->contact_id],
        ])->delete();
        return response()->json($deleted);
    }

    public function addcontact(Request $request)
    {
        $adduserid = User::where('email',$request->email)->first();
        if($adduserid){
            $checkentry = Contact::where(['user_id'=>$request->user,'user_contact_id'=> $adduserid->id])->first();
            if ($checkentry) {
                $response = ['status' => 0, 'contact'=>null];
                return response()->json($response); 
            }
            else {
                $entry = Contact::create([
                    'user_id' => $request->user,
                    'user_contact_id'=> $adduserid->id
                ]);
                $response = ['status' => 1, 'contact'=>$adduserid];
                return response()->json($response);
            }
        } else {
            $response = ['status' => 2, 'contact'=>null];
            return response()->json($response);
        }
        
    }

    public function get()
    {
        // get all contacts for the authenticated user
        $contactids = Contact::where('user_id','=', auth()->id() )->get();
        $contacts = [];
        foreach ($contactids as $contid) {
            $contact = User::where('id', '=', $contid->user_contact_id)->first();
            $contacts[] = $contact;
        }
        

        // get a collection of items where sender_id is the user who sent us a message
        // and messages_count is the number of unread messages we have from him
        $unreadIds = Message::select(\DB::raw('`from` as sender_id, count(`from`) as messages_count, max(`created_at`) as created_at'))
            ->where('to', auth()->id())
            ->where('read', false)
            ->groupBy('from')
            ->get();
            
        $last_msg_rcvd = Message::select(\DB::raw('`from` as sender_id, max(`created_at`) as created_at'))
                        ->where('to', auth()->id())
                        ->where('read', true)
                        ->groupBy('from')
                        ->get();
        // add an unread key to each contact with the count of unread messages
        $contacts = collect($contacts);
        $contacts = $contacts->map(function($contact) use ($unreadIds, $last_msg_rcvd) {
            $contactUnread = $unreadIds->where('sender_id', $contact->id)->first();
            $last_msg = $last_msg_rcvd->where('sender_id', $contact->id)->first();

            $contact->unread = $contactUnread ? $contactUnread->messages_count : 0;
            if ($contactUnread) {
                $contact->msg_time = strtotime($contactUnread->created_at);
            }
            else{
                if ($last_msg) {
                    $contact->msg_time = strtotime($last_msg->created_at);
                } else {
                    $contact->msg_time = 0;
                }   
            }
            return $contact;
        });

        return response()->json($contacts);
    }

    public function getMessagesFor($id)
    {
        // mark all messages with the selected contact as read
        Message::where('from', $id)->where('to', auth()->id())->update(['read' => true]);
        
        // get all messages between the authenticated user and the selected user
        $messages = Message::where(function($q) use ($id) {
            $q->where('from', auth()->id());
            $q->where('to', $id);
        })->orWhere(function($q) use ($id) {
            $q->where('from', $id);
            $q->where('to', auth()->id());
        })
        ->get();
        foreach ($messages as $msg) {
            if($msg->file){
                $extension = pathinfo(storage_path($msg->file), PATHINFO_EXTENSION);
                $msg['extension'] = $extension;
            }
        }
        return response()->json($messages);
    }

    public function getMessagesForGroup($id)
    {
        // mark all messages with the selected contact as read
        Group_read::where('group_id', $id)->where('user_id', auth()->id())->update(['read' => true]);
        
        // get all messages between the authenticated user and the selected user
        $messages = Message::where('to_group',$id)->get();
        
        foreach ($messages as $msg) {
            if($msg->file){
                $extension = pathinfo(storage_path($msg->file), PATHINFO_EXTENSION);
                $msg['extension'] = $extension;
            }
        }
        return response()->json($messages);
    }

    public function send(Request $request)
    {
        
        if ($request->contact_type == 'contact') {
            $message = null;
            if ($request->file && $request->text) {
                $filename = $request->file->store('/public/img');
                $filename = substr($filename,7);
                $message = Message::create([
                    'from' => auth()->id(),
                    'to' => $request->contact_id,
                    'text' => $request->text,
                    'file' => $filename
                ]);
            } else {
                if ($request->file) {
                    $filename = $request->file->store('/public/img');
                    $filename = substr($filename,7);
                    $message = Message::create([
                        'from' => auth()->id(),
                        'to' => $request->contact_id,
                        'file' => $filename
                    ]);    
                } else {
                    $message = Message::create([
                        'from' => auth()->id(),
                        'to' => $request->contact_id,
                        'text' => $request->text
                    ]);
                }
            }
            
            
            
            broadcast(new NewMessage($message));
    
            return response()->json($message);
        }
        if ($request->contact_type == 'group')
        {
            $message = null;
            if ($request->file && $request->text) {
                $filename = $request->file->store('/public/img');
                $filename = substr($filename,7);
                $message = Message::create([
                    'from' => auth()->id(),
                    'to_group' => $request->contact_id,
                    'text' => $request->text,
                    'file' => $filename
                ]);
            } else {
                if ($request->file) {
                    $filename = $request->file->store('/public/img');
                    $filename = substr($filename,7);
                    $message = Message::create([
                        'from' => auth()->id(),
                        'to_group' => $request->contact_id,
                        'file' => $filename
                    ]);
                } else {
                    $message = Message::create([
                        'from' => auth()->id(),
                        'to_group' => $request->contact_id,
                        'text' => $request->text
                    ]);
                }
            }
            
            
            $group = Group::where('id',$request->contact_id)->get();
            $participants = Group_user::where('group_id',$request->contact_id)->pluck('user_id');
            $request = ['group'=>$group, 'participants'=>$participants, 'message'=>$message]; 
            
            //create group_read table entries
            foreach ($participants as $participant) {
                if ($participant == auth()->id()) {
                    Group_read::create(
                        ['group_id' => $message->to_group, 'user_id' => $participant, 'msg_id' => $message->id, 'read' => true]
                    );
                } else {
                    Group_read::create(
                        ['group_id' => $message->to_group, 'user_id' => $participant, 'msg_id' => $message->id]
                    );
                }
                
                
            }
            broadcast(new GroupMessage($request))->toOthers();
            return response()->json($message);
        }        
    }
}
