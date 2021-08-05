<?php

namespace App\Security\Voter;

use App\Entity\Review;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ReviewVoter extends Voter
{
    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, ['REVIEW_EDIT', 'REVIEW_DELETE'])
            && $subject instanceof \App\Entity\Review;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var Review $review */
        $review = $subject;
        /** @var User $user */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'REVIEW_EDIT':
            case 'REVIEW_DELETE':
                // Check if the connected user is the author of the review
                return $review->getUser()->getId() === $user->getId();
        }

        return false;
    }
}
