<?php 
require 'ClassAutoLoad.php';


$mailcontent = [
    'name_from' => 'Stacy Mwangi',
    'mail_from' => 'stacynjoks@gmail.com',
    'name_to' => 'Stacy Njoki',
    'mail_to' => 'stacynjoks1@gmail.com',
    'subject' => 'Greetings From ICS 2B',
    'body' => 'Welcome to ICS 2B! <br> This is a new semester. Let\'s have fun and make memories together.'
];


$ObjSendMail->send_Mail($conf, $mailcontent);