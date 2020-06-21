<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use Kreait\Firebase\Database;

class UserController extends Controller
{
    // Intialize API Response Manager
    use \App\Traits\APIResponseManager;

    protected $userObj;

    public function __construct(User $user, Database $database)
    {
        $this->userObj=$user;
        $this->database = $database;
    }

    /**
     * Desc: Method is used to authenticate the user
     * Param: email, password
     */
    public function login(Request $request){

        // Check Validation
        try{
            
            $request->validate([
                'email' => 'required|exists:users,email',
                'password' => 'required'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {

            $errorResponse = $this->validationResponse($e);
            return $this->responseManager(400, 'Validation Error', $errorResponse);

        }

        // Authenticate User
        try{

            // Valdate Email
            if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                    return $this->responseManager(400, 'USER _INVALID_CREDENTIALS');
                }
            } 

            // User Check
            $user=Auth::user();
            if(!empty($user)){

                // Create Token
                $token = $this->userObj->createPassportToken($user);
                $user->access_token=$token;
                $userData=$this->userObj->user_resource($user);

                return $this->responseManager(200, 'USER_LOGIN_SUCCESS', $userData);
            }else {
                return $this->responseManager(402, 'ACCOUNT_NOT_VERIFY');
            }

        } catch (\PDOException $e) {
            $errorResponse = $e->getMessage();
            return $this->responseManager(402, 'DB_ERROR', $errorResponse);
        }
        
    }


    /**
     * Desc: Method is used to get all user
     * Params: Authorization Token
     */
    public function user_listing(Request $request){

        $userList = $this->userObj->getallUsers();
        if(!empty($userList)){
            return $this->responseManager(200, 'USER_LISING_SUCCESS', $userList);
        } else {
            return $this->responseManager(400, 'USER_RECORD_NOT_FOUND');
        }
        
    }


    /***
     * Desc: Methd 
     * Params
     */
    public function post_message(Request $request){

        // Check Validation
        try{
            
            $request->validate([
                'sender_id' => 'required|exists:users,id',
                'reciever_id' => 'required|exists:users,id',
                'message' => 'required'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {

            $errorResponse = $this->validationResponse($e);
            return $this->responseManager(400, 'Validation Error', $errorResponse);

        }

        // GET AUTH USER -SENDER_ID
        $currentUser = Auth::user();
        if(!empty($currentUser)){

            // CHECK SENDER_ID == AUTH_ID
            if($request->sender_id == $currentUser->id){

                // RECIEVER_ID NOT EQUAL TO SENDER_ID
                if($request->reciever_id != $request->sender_id ){

                    // PREPARE DATA ON FIREBASE DB
                    $postData = [
                        'sender_id' => $currentUser->id,
                        'reciever_id' => $request->reciever_id,
                        'message' => $request->message,
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $postRef = $this->database->getReference('MEMBER_CHAT')->push($postData);
                    $postKey = $postRef->getKey();

                    return $this->responseManager(200, 'MESSAGE_POST_SUCCESS', ['post_key' => $postKey]);

                } else {

                    return $this->responseManager(402, 'RECIEVER_ID_SHOULD_BE_DIFFERENT_TO SENDER_ID');

                }

            } else {

                return $this->responseManager(402, 'INVALID_SENDER_ID');

            }

        } else {

            return $this->responseManager(402, 'USER_DOES_NOT_EXIST, LOGIN_AGAIN');

        }

    }


    /**
     * Desc: Method is used to get chat - 1 to 1
     * Params: sender_id, reciever_id
     */
    public function messages(Request $request){

        // Check Validation
        try{
            
            $request->validate([
                'sender_id' => 'required|exists:users,id',
                'reciever_id' => 'required|exists:users,id'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {

            $errorResponse = $this->validationResponse($e);
            return $this->responseManager(400, 'Validation Error', $errorResponse);

        }

        // GET AUTH USER -SENDER_ID
        $currentUser = Auth::user();
        if(!empty($currentUser)){

            // CHECK SENDER_ID == AUTH_ID
            if($request->sender_id == $currentUser->id){

                // RECIEVER_ID NOT EQUAL TO SENDER_ID
                if($request->reciever_id != $request->sender_id ){

                    // GET USER CHAT
                    $reference = $this->database->getReference('MEMBER_CHAT')->getValue();

                    $filtered = array_filter($reference, function (array $userData) use($request){
                       return ($userData['sender_id']==$request->sender_id && $userData['reciever_id']==$request->reciever_id ) || ($userData['sender_id']==$request->reciever_id && $userData['reciever_id']==$request->sender_id );
                    });

                    if(!empty($filtered)){
                        return $this->responseManager(200, 'MESSAGE_FOUND', $filtered);
                    } else {
                        return $this->responseManager(400, 'MESSAGE_NOT_FOUND', $filtered);
                    }

                } else {

                    return $this->responseManager(402, 'RECIEVER_ID_SHOULD_BE_DIFFERENT_TO SENDER_ID');

                }

            } else {

                return $this->responseManager(402, 'INVALID_SENDER_ID');

            }

        } else {

            return $this->responseManager(402, 'USER_DOES_NOT_EXIST, LOGIN_AGAIN');

        }
    }

    
}
