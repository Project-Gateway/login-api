<?php

namespace App\Services\Auth\Contracts;

interface UserContract
{
    public function getId();
    public function getAllEmails();
}
