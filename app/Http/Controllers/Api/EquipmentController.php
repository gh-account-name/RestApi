<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use App\Rules\SerialNumberMaskRule;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class EquipmentController extends Controller
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

        $query = Equipment::query();

        // Применяем фильтрацию по ключам объекта, если указаны
        $filters = $request->except(['per_page', 'page', 'q']);
        foreach ($filters as $key => $value) {
            $query->where($key, $value);
        }

        // Применяем поиск, если указан параметр 'q'
        if ($searchQuery) {
            $columns = $query->getConnection()->getSchemaBuilder()->getColumnListing('equipment');
            $query->where(function ($q) use ($columns, $searchQuery) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $searchQuery . '%');
                }
            });
        }

        $equipment = $query->paginate($perPage);

        return EquipmentResource::collection($equipment);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Reqest
     * @return Response
     */
    public function store(Request $request)
    {
        $errors = [];
        $success = [];
        $data = $request->json()->all();

        // Проходимся по каждому объекту из запроса
        foreach ($data as $index => $item) {
            $validator = Validator::make($item, [
                'equipment_type_id' => 'required|integer|exists:equipment_types,id',
                'serial_number' => ['required', 'string', 'unique:equipment', new SerialNumberMaskRule($item['equipment_type_id'])],
                'note' => 'nullable|string',
            ], [
                'equipment_type_id.required' => 'Поле ID типа оборудования обязательно',
                'equipment_type_id.integer' => 'Поле ID типа оборудования должно быть целым числом',
                'equipment_type_id.exists' => 'Указаный тип оборудования не существует',
                'serial_number.required' => 'Поле серийного номера обязательно',
                'serial_number.string' => 'Поле серийного номера должно быть строкой',
                'serial_number.unique' => 'Данный серийный номер уже существует',
                'desc.string' => 'Поле описания должно быть строкой',
            ]);

            if ($validator->fails()) {
                $errors[$index] = $validator->errors()->all();
            } else {
                // Данные проходят валидацию, выполняем сохранение
                $equipment = Equipment::create($item);
                $success[$index] = new EquipmentResource($equipment);
            }
        }

        // Формируем ответ согласно заданному формату
        $response = [
            'errors' => (object) $errors,
            'success' => (object) $success,
        ];


        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param Equipment
     * @return Response
     */
    public function show(Equipment $equipment)
    {
        return new EquipmentResource($equipment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request
     * @param Equipment
     * @return Response
     */
    public function update(Request $request, Equipment $equipment)
    {
        $validator = Validator::make($request->all(), [
            'equipment_type_id' => 'sometimes|required|integer|exists:equipment_types,id',                                       //Сверяем с маской переданного типа оборудования, если тип оборудования передан не был сверяем с маской текущего типа
            'serial_number' => ['required_with:equipment_type_id', 'string', 'unique:equipment,serial_number,' . $equipment->id, new SerialNumberMaskRule($request->equipment_type_id ? $request->equipment_type_id : $equipment->equipment_type_id)],
            'note' => 'nullable|string',
        ], [
            'equipment_type_id.required' => 'Поле ID типа оборудования обязательно',
            'equipment_type_id.integer' => 'Поле ID типа оборудования должно быть целым числом',
            'equipment_type_id.exists' => 'Указаный тип оборудования не существует',
            'serial_number.required_with' => 'Поле серийного номера обязательно',
            'serial_number.string' => 'Поле серийного номера должно быть строкой',
            'serial_number.unique' => 'Данный серийный номер уже существует',
            'desc.string' => 'Поле описания должно быть строкой',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all());
        }

        $equipment->update($validator->validated());

        return new EquipmentResource($equipment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Equipment
     * @return Response
     */
    public function destroy(Equipment $equipment)
    {
        $equipment->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
