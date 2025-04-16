<?php

namespace App\Helpers;

/**
 * Format paginated response in a consistent structure
 *
 * @param \Illuminate\Pagination\LengthAwarePaginator $paginator
 * @return \Illuminate\Http\JsonResponse
 */
function paginatedResponse($paginator)
{
    $response = [
        'page' => $paginator->currentPage(),
        'per_page' => $paginator->perPage(),
        'total' => $paginator->total(),
        'data' => $paginator->items(),
    ];

    if ($paginator->hasMorePages()) {
        $response['next_page_url'] = $paginator->nextPageUrl();
    }

    if ($paginator->previousPageUrl() !== null) {
        $response['previous_page_url'] = $paginator->previousPageUrl();
    }

    return response()->json($response);
}