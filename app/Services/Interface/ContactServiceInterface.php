<?php

namespace App\Services\Interface;

use App\Models\Contact;
use App\Models\User;

interface ContactServiceInterface
{
    public function getContactById(int $contactId, User $user): Contact;
}