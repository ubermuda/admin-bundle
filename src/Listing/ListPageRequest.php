<?php

namespace Ubermuda\AdminBundle\Listing;

use Symfony\Component\HttpFoundation\Request;

/**
 * Parsed and validated listing page parameters (page / sort / dir).
 *
 * Mirrors the input cleanup the three admin list controllers (users, events,
 * feature flags) used to do inline. Sort is checked against an allowlist with
 * a fallback to the default; direction is lower-cased and constrained to
 * `asc|desc`; page is clamped to a minimum of 1.
 */
final readonly class ListPageRequest
{
    public function __construct(
        public int $page,
        public string $sort,
        public string $dir,
    ) {
    }

    /**
     * @param list<string> $allowedSorts
     */
    public static function fromRequest(
        Request $request,
        array $allowedSorts,
        string $defaultSort,
        string $defaultDir = 'desc',
    ): self {
        $page = max(1, $request->query->getInt('page', 1));

        $sort = $request->query->getString('sort');
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = $defaultSort;
        }

        $dir = strtolower($request->query->getString('dir'));
        if (!in_array($dir, ['asc', 'desc'], true)) {
            $dir = $defaultDir;
        }

        return new self($page, $sort, $dir);
    }
}
