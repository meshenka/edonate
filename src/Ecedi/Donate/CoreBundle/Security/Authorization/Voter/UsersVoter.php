<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @package eDonate
 */

namespace Ecedi\Donate\CoreBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * View list of users and
 * @since  2.3
 *
 */
class UsersVoter implements VoterInterface
{
    const LIST_USERS = 'list users';
    const CREATE_USERS = 'create users';

    /**
     * List of supported attributes
     * @param  string  $attribute
     * @return boolean
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::LIST_USERS,
            self::CREATE_USERS,
        ));
    }

    public function supportsClass($class)
    {
        return true;
    }

    /**
     * @var Ecedi\Donate\CoreBundle\Entity\User
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        // check if the voter is used correct, only allow one attribute
        // this isn't a requirement, it's just one easy way for you to
        // design your voter
        if (1 !== count($attributes)) {
            throw new \InvalidArgumentException(
                'Only one attribute is allowed for VIEW, EDIT or DELETE'
            );
        }

        // set the attribute to check against
        $attribute = $attributes[0];

        // check if the given attribute is covered by this voter
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // get current logged in user
        $currentUser = $token->getUser();

        // make sure there is a user object (i.e. that the user is logged in)
        if (!$currentUser instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch ($attribute) {
            case self::LIST_USERS:

                if ($currentUser->hasRole('ROLE_ADMIN')) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                //others cannot view others
                break;

            case self::CREATE_USERS:
                if ($currentUser->hasRole('ROLE_ADMIN')) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
