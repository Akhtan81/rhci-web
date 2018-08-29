<?php

namespace App\Entity;

class OrderStatus
{
    const CREATED = 'created';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';
    const IN_PROGRESS = 'in_progress';
    const DONE = 'done';

}