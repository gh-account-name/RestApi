<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EquipmentType;
use Illuminate\Http\Request;

class EquipmentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request
     * @return Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $searchQuery = $request->input('q');

        $query = EquipmentType::query();

        // Применяем фильтрацию по ключам объекта, если указаны
        $filters = $request->except(['per_page', 'page', 'q']);
        foreach ($filters as $key => $value) {
            $query->where($key, $value);
        }

        // Применяем поиск, если указан параметр 'q'
        if ($searchQuery) {
            $columns = $query->getConnection()->getSchemaBuilder()->getColumnListing('equipment_types');
            $query->where(function ($q) use ($columns, $searchQuery) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $searchQuery . '%');
                }
            });
        }

        $equipment_types = $query->paginate($perPage);

        return $equipment_types;
    }
}
