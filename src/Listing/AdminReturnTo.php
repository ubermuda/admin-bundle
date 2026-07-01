<?php

namespace Ubermuda\AdminBundle\Listing;

use Psr\Log\LoggerInterface;

/**
 * Validates an opaque `returnTo` URL coming back from a list-page Edit/Delete
 * /Toggle round trip.
 *
 * Only URLs starting with `/admin/` are accepted, both to defend against
 * open-redirect probes and to keep the redirect inside the admin shell.
 * Logs `admin.list.return_to_rejected` (info) when a non-null candidate is
 * rejected so suspicious payloads / buggy callers are observable.
 */
final readonly class AdminReturnTo
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Returns the candidate when it is a valid `/admin/`-prefixed URL, else null.
     *
     * Accepts `mixed` for the candidate so callers can pass `$request->query->get('returnTo')`
     * or `$request->request->get('returnTo')` directly without coercing first.
     */
    public function validate(string $entity, mixed $candidate): ?string
    {
        if (!is_string($candidate) || '' === $candidate) {
            return null;
        }

        if (!str_starts_with($candidate, '/admin/')) {
            $this->logger->info('admin.list.return_to_rejected', [
                'entity' => $entity,
                'return_to_prefix' => substr($candidate, 0, 32),
            ]);

            return null;
        }

        return $candidate;
    }
}
