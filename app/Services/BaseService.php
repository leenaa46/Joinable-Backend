<?php

namespace App\Services;

use App\Traits\ImageTrait;
use App\Http\Resources\PaginateCollection;

class BaseService
{
    use ImageTrait;

    public function formatQuery($query, $filterBy = [], $columnsSearch = [])
    {
        if (\count(request()->all()) <= 0) return $query->get();

        // Filter
        $query = $this->filter($query, $filterBy);

        // Column search.
        $query = $this->searchCollumn($query, $columnsSearch);

        // Order
        $query = $this->order($query);

        // Pagination
        $resData = $this->pagination($query);

        return $resData;
    }

    public function filter($query, $filterBy)
    {
        // Filter
        if (request()->has('filter') && count($filterBy) > 0) {
            $query->where(function ($q) use ($filterBy) {
                $q->where($filterBy[0], 'like', '%' . request()->filter . '%');
                unset($filterBy[0]);
                foreach ($filterBy as $filterKey) {
                    $q->orWhere($filterKey, 'like', '%' . request()->filter . '%');
                }
            });
        }

        return $query;
    }

    public function searchCollumn($query, array $columnsSearch)
    {
        foreach ($columnsSearch as $column) {
            if (request()->has($column) && isset(request()->$column)) {
                $query->where($column, request()->$column);
            }
        }

        return $query;
    }

    public function order($query)
    {
        switch (request()->order_by) {
            case 'newest':
                $query->orderByDesc('id');
                break;
            case 'current':
                $query->orderByDesc('updated_at');
                break;
            default:
                if (\request()->order_by) $query->orderByDesc(\request()->order_by);
                break;
        }

        return $query;
    }

    public function pagination($query)
    {
        if (request()->per_page) {
            $resData = new PaginateCollection($query->paginate(request()->per_page));
        } elseif (request()->cursor_paginate) {
            $resData = $query->cursorPaginate(request()->cursor_paginate);
        } else {
            $resData = $query->get();
        }

        return $resData;
    }
}
