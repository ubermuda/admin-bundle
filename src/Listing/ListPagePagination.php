<?php

namespace Ubermuda\AdminBundle\Listing;

use Psr\Log\LoggerInterface;

/**
 * Pagination helpers shared by every admin list page.
 *
 * Two responsibilities:
 *   - Build the elided list of page numbers shown in the pagination control
 *     (always include first/last and ±2 around the current page; null entries
 *     mark ellipsis slots).
 *   - Detect when an out-of-range `page=` query param needs to redirect to the
 *     last page; logs `admin.list.page_clamped` so we can see whether stale
 *     bookmarks/URLs are common enough to warrant a server-side rewrite.
 */
final readonly class ListPagePagination
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @return list<int|null> integer page numbers, null for ellipsis slots
     */
    public function buildPageList(int $page, int $totalPages): array
    {
        $pages = [];
        $previous = 0;
        for ($candidate = 1; $candidate <= $totalPages; ++$candidate) {
            if (1 === $candidate
                || $candidate === $totalPages
                || ($candidate >= $page - 2 && $candidate <= $page + 2)
            ) {
                if ($previous > 0 && $candidate > $previous + 1) {
                    $pages[] = null;
                }
                $pages[] = $candidate;
                $previous = $candidate;
            }
        }

        return $pages;
    }

    /**
     * Returns the clamped page number when a redirect is needed, or null when
     * the requested page is in range.
     *
     * @param array<string, mixed> $filters
     */
    public function clampPage(string $entity, int $page, int $total, int $perPage, array $filters): ?int
    {
        if ($total <= 0 || $perPage <= 0) {
            return null;
        }

        $totalPages = max(1, (int) ceil($total / $perPage));
        if ($page <= $totalPages) {
            return null;
        }

        $this->logger->info('admin.list.page_clamped', [
            'entity' => $entity,
            'requested_page' => $page,
            'total_pages' => $totalPages,
            'per_page' => $perPage,
            'filters' => $filters,
        ]);

        return $totalPages;
    }
}
