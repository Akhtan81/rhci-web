<?php

namespace App\Entity;

class OrderStatus
{
    const FAILED = 'failed';
    const CREATED = 'created';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';
    const IN_PROGRESS = 'in_progress';
    const DONE = 'done';
    const CANCELED = 'canceled';

}