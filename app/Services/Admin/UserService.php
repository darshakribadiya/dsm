<?php

namespace App\Services\Admin;

use App\Models\User;

class UserService
{
    public function getAllUsers(array $filters)
    {
        $query = User::query();

        // Filter by user_type if provided
        if (!empty($filters['user_type'])) {
            $query->where('user_type', $filters['user_type']);
        }

        // Filter by status if provided
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Advanced search (name OR email)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Pagination (default 10 per page)
        $perPage = $filters['per_page'] ?? 10;

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }
}