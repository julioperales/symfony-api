<?php
    namespace App\Dto;

    class RegisterDto{
        public $id;
        public $firstname;
        public $lastname;
        public $address;
        public $phone;
        //public $others; Just commented to see DTO mapping works
        public $created_at;
        public $updated_at;
        public $active;    
    }