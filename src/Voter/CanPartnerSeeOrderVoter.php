<?php

namespace App\Voter;

use App\Entity\Order;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CanPartnerSeeOrderVoter extends Voter
{
    const NAME = 'MRS.CanPartnerSeeOrderVoter';

    protected function supports($attribute, $subject)
    {
        return $attribute === self::NAME && $subject instanceof Order;
    }

    /**
     * @param string $attribute
     * @param Order $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) return false;

        return true;
    }
}