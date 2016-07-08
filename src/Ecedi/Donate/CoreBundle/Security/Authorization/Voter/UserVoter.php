<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 */
namespace Ecedi\Donate\CoreBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Ecedi\Donate\CoreBundle\Entity\User;

/**
 * Voter for view / edit / delete of an existing user.
 *
 * @since  2.3
 */
class UserVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports($attribute, $subject)
    {
        if (($subject instanceof User) && in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $currentUser = $token->getUser();
        //current user can always view her account
        if ($attribute == self::VIEW && $currentUser->isUser($subject)) {
            return true;
        }

        //current user can always edit her account
        if ($attribute == self::EDIT && $currentUser->isUser($subject)) {
            return true;
        }

        //current user can always edit her account
        if ($attribute == self::DELETE && $currentUser->isUser($subject)) {
            return false;
        }

        return true;
    }
}
