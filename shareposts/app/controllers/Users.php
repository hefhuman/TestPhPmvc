<?php
class Users extends Controller {
  public function __construct() {
    $this->userModel = $this->model('User');
  }

  public function register(){
//check for post
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
      //Process form

      //Sanitize POST data
      $_POST = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);

      //init data
      $data = [
        'name' => trim($_POST['name']),
        'email' => trim($_POST['email']),
        'password' => trim($_POST['password']),
        'confirm_password' => trim($_POST['confirm_password']),
        'name_err' => '',
        'email_err' => '',
        'password_err' => '',
        'confirm_password_err' => ''
      ];

      //Validating email
      if(empty($data['email'])){
        $data['email_err'] = 'Please enter email';
      } else {
        //Check email
        if($this->userModel->findUserByEmail($data['email'])){
          $data['email_err'] = 'Email is already taken';
        }
      }

      //Validating name
      if(empty($data['name'])){
        $data['name_err'] = 'Please enter name';
      }

      //Validating password
      if(empty($data['password'])){
        $data['password'] = 'Please enter password';
      }elseif(strlen($data['password']) < 6){
        $data['password_err'] = 'Password must be atleast   6 characters';
      }

      //Validating confirm password
      if(empty($data['confirm_password'])){
        $data['confirm_password_err'] = 'Please confirm password';
      }else{
        if($data['confirm_password'] != $data['confirm_password']){
          $data['confirm_password_err'] = 'Password do not match';
        }
      }

      //Make sure that errors are empty
      if(empty($data['email_err']) && empty($data['name_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])){
        //Validated

      //Hash a password
      $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

      // Register Users
      if($this->userModel->register($data)){
        flash('register_success', 'Your are registered and can log in');
      redirect('users/login');
      } else {
        die('Something went wrong');
      }

      } else {
        //load view with error
        $this->view('users/register',$data);
      }

    } else {
      //Load form
      //init data
      $data = [
        'name' => '',
        'email' => '',
        'password' => '',
        'confirm_password' => '',
        'name_err' => '',
        'email_err' => '',
        'password_err' => '',
        'confirm_password_err' => ''
      ];

      //load view
      $this->view('users/register',$data);
    }
  }
  public function login(){
    //check for post
        if($_SERVER['REQUEST_METHOD'] =='POST'){
          //Process form
          //Sanitize POST data
          $_POST = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);

          //init data
          $data = [
            'email' => trim($_POST['email']),
            'password' => trim($_POST['password']),
            'email_err' => '',
            'password_err' => ''
          ];

          //Validating email
          if(empty($data['email'])){
            $data['email_err'] = 'Please enter email';
          }

          //Validating password
          if(empty($data['password'])){
            $data['password'] = 'Please enter password';
          }elseif(strlen($data['password']) < 6){
            $data['password_err'] = 'Password must be atleast   6 characters';
          }

          // Check for user/email
          if($this->userModel->findUserByEmail($data['email'])){
            //User found
          } else {
            //User not found
            $data['email_err'] = 'No user found';
          }


          //Make sure that errors are empty
          if(empty($data['email_err']) && empty($data['password_err'])){
            //Validated
            //Check and set logged in users
            $loggedInUser = $this->userModel->login($data['email'], $data['password']);

            if($loggedInUser){
              //Create Session
              $this->createUserSession($loggedInUser);
            } else {
              $data['password_err'] = 'Password incorrect';

              $this->view('users/login', $data);
            }
          } else {
            //load view with error
            $this->view('users/login',$data);
          }

        } else {
          //Load form
          //init data
          $data = [
            'name' => '',
            'email' => '',
            'password' => '',
            'confirm_password' => '',
            'name_err' => '',
            'email_err' => '',
            'password_err' => '',
            'confirm_password_err' => ''
          ];

          //load view
    $this->view('users/login',$data);
  }
}
  public function createUserSession($user){
    $_SESSION['user_id'] =  $user->id;
    $_SESSION['user_email'] =  $user->email;
    $_SESSION['user_name'] =  $user->name;
    redirect('posts');
  }

  public function logout(){
    unset($_SESsION['user_id']);
    unset($_SESsION['user_email']);
    unset($_SESsION['user_name']);
    session_destroy();
    redirect('users/login');
  }
  }
