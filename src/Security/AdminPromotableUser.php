<?php

namespace Ubermuda\AdminBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Implement on the app's user entity to opt in to admin-email promotion
 * (see PromoteAdminUserListener). Plain public properties satisfy the
 * property hooks.
 */
interface AdminPromotableUser extends UserInterface
{
    public ?string $email { get; }

    /** @var list<string> */
    public array $roles { get; set; }
}
