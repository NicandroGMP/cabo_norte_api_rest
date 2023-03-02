<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AccountsModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\Hash;
use App\Libraries\Message;
use App\Libraries\StringMake;

class Auth extends BaseController {
    public $accounts;
    public function __construct()
    {
		helper(["url", "form"]);

        $this->accounts = new AccountsModel();
    }
    public function index(){
        if($this->accounts){
            return $this->getResponse([
                "status" => "ok",
            ], ResponseInterface::HTTP_CREATED);
        }
        else{
            return $this->getResponse([
                "status" => "database error conection",
            ], ResponseInterface::HTTP_CREATED);
        }
    }

    public function login(){
            $rules = [
                'username' => 'required|is_not_unique[accounts.username]',
                'password' => 'required|min_length[5]|max_length[12]',
            ];
            $errors = [
                'password' => [
                    'required' => 'Your password is required'
                ],
                'username' => [
                    'required' => 'username is required',
                    "is_not_unique" => "This email is not registered on our service"
                ]
            ];
    
          /*   $validation = $this->validate([
                "email" => [
                "rules" => "required|valid_email|is_not_unique[accounts.email]",
                "errors" =>[
                    "required" => "Email is required", 
                    "valid_email" => "Enter valid email address", 
                    "is_not_unique" => "This email is not registered on our service"
                    ] 
                ],
                "password" => ["rules" => "required|min_length[5]|max_length[12]", 
                "errors" => [
                    "required" => "Password is required",
                    "min_length" => "Password must have atleast 5 characters in length",
                    "max_length" => "Password must not have more than 12 characters in length"
                    ]
                ]
            ]);
     */
    
            $input = $this->getRequestInput($this->request);
            if (!$this->validateRequest($input, $rules, $errors)) {
                return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
            }

            $username = $input["username"];
            $password = $input["password"];
            $user_info = $this->accounts->where("username", $username)->where("status", "Habilitado")->first();
            $check_pass = Hash::check($password, $user_info["password"]);
            if (!$check_pass){

                return $this->getResponse(["error" => "Invalid password"],ResponseInterface::HTTP_BAD_REQUEST);
            }else{

                return $this->getJWTForUser($input['username']); 
            }
/* 
            */
        }
    public function register()
    {
        $rules = [
            'username' => 'required',
            'email' => 'required|valid_email|is_unique[accounts.email]',
            'password' => 'required|min_length[8]|max_length[255]'
        ];

        $input = $this->getRequestInput($this->request);

        if (!$this->validateRequest($input, $rules)) {
            return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }

        $userModel = new AccountsModel();
        $userModel->save($input);

        return $this->getJWTForUser($input['email'], ResponseInterface::HTTP_CREATED);
    }
    public function forgetPassword()
    {
        $rules = [
            "email" => [
                "rules" => "required|valid_email|is_not_unique[accounts.email]",
            ]
        ];
        $errors = [
            "email" => [
            "required" => "Email is required",
            "valid_email" => "Enter Valid Email",
            "is_not_unique" => "This Email Not Exist"
            ]
        ];

        $input = $this->getRequestInput($this->request);
        if (!$this->validateRequest($input, $rules,$errors)) {
            return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }

        $to = $input["email"];
        $create_at = date("Y-m-d H:m:s");
        $string_date = strtotime($create_at);
        $date_expire = date("Y-m-d H:m:s",strtotime($create_at."+1 week"));

        $string_encript = StringMake::makeString();
        //$date_encript = StringMake::makeString((strtotime($date)));
    $key = "$string_encript$string_date";

    $email = \Config\Services::email();
    $email->setTo($to);
    //$email->setFrom('nicandrogama@gmail.com', 'NicandroMP');
    $mesagge = Message::message($key);
    $email->setSubject("Correo de Recuperacion de Contraseña");
    $email->setMessage($mesagge);

    if ($email->send())
    {
        $data = ["encript_string" => $key, "email" => $to, "expire_link" => $date_expire];
        $this->recove_pass->insert($data);
        return $this->getResponse([
            "message" => "
            The Email Has Been Sent: Check Your Mailbox",
            "cookie_key" => $key
        ]);
    }
    }

  /*   public function pageExpire($key, $string_cookie){
        $verifySessionCookie = Hash::checkString($string_cookie, $key);

        if(!$verifySessionCookie){
            return $this->getResponse( "",ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            $inf_user = $this->recove_pass->where("encript_string", $string_cookie)->first();
            $expiredate = date(strtotime($inf_user['expire_link']));
            $dateuser = (getdate()['0']);
            //($intvl = (new Datetime($dateuser))->diff(new DateTime($expiredate)));
            if ($dateuser > $expiredate) {
               return $this->getResponse("", ResponseInterface::HTTP_BAD_REQUEST);
            }else{
                return $this->getResponse(true);
            }

        }
    } */

    public function UpdatePassword(){

        $rules =[
            "password" => [
                "rules" => "required|min_length[5]|max_length[12]|", 
                
            "cpassword" => [
                "rules" => "required|min_length[5]|max_length[12]|matches[password]",
                
            ],
        ]
        ];
        $errors = [
            "password" =>[
                "required" => "Password is Required",
                "min_length" => "Password Must Have Atleast 5 Characters in Length",
                "max_length" => "Password Must Not Have More Than 12 Characters in Length",],
            "cpassword" =>[
                    "required" => "Password is Required",
                    "min_length" => "Password Must Have Atleast 5 characters in length",
                    "max_length" => "Password Must Not Have more than 12 characters in length",
                    "matches" => "Your Password Don't Match, Please Try Again",
                ]
            ];
        $input = $this->getRequestInput($this->request);
        if (!$this->validateRequest($input, $rules,$errors)) {
            return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }
        $cookie = $input["cookie"];
        $inf_user = $this->recove_pass->where("encript_string", $cookie)->first();
        $email = $inf_user["email"];
        $dataSearch = $this->accounts->where("email",$email)->first();
        $id = $dataSearch ['id'];
        $newPassword = $input["password"];
        $newdata = ["password" => Hash::make($newPassword)];
        $update = $this->accounts->update($id,$newdata);

        if (!$update){
            return $this->getResponse("",ResponseInterface::HTTP_BAD_REQUEST);
        }else{
            $id = $dataSearch ['id'];
            $delete = $this->recove_pass->delete($id);
            return $this->getResponse([
                "message" => "Change Password successfully",
            ]);
        }

    }
    private function getJWTForUser(string $username, int $responseCode = ResponseInterface::HTTP_OK)
    {
        try {
            $model = new AccountsModel();
            $user = $model->findUserByUsername($username);
            unset($user['password']);

            helper('jwt');

            return $this->getResponse([
                'message' => 'User authenticated successfully',
                'user' => $user,
                'access_token' => getSignedJWTForUser($username)
            ]);
        } catch (\Exception $e) {
            return $this->getResponse([
                'error' => $e->getMessage()
            ], $responseCode);
        }
    }
}