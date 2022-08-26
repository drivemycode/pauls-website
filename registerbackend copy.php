<?php

$user = new User;
// $_POST['name'];
// $_POST['email'];
// $_POST['password'];
// $_POST['password-repeat'];

class User {
    public $name = '';
    public $email = '';
    private $password = '';
    public $errors = ['name'=>'', 'password'=>''];

    public function setName(){
        if (empty($_POST['name'])){
            $this->errors['name'] = "error: no name filled in.";
            exit();
        }
        if (!preg_match('/^[a-z]*$/i', $_POST['name'])){
            $this->errors['name'] = "error: name contains invalid symbols.";
            exit();
        }
        $this->name = $_POST['name'];
    }
}

if(isset($_POST['submit'])){
   echo $user->setName();
   echo $user->name;
   print_r($user->errors);
}