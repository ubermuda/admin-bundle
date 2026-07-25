<?php

namespace Ubermuda\AdminBundle\Test\Security;

use Ubermuda\AdminBundle\Security\AdminPromotableUser;

final class PromotableUser implements AdminPromotableUser
{
    /** @param list<string> $roles */
    public function __construct(
        public ?string $email = null,
        public array $roles = [],
        private bool $verified = true,
    ) {
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    /** @return list<string> */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email ?? 'anonymous';
    }
}
