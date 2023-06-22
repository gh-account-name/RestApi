<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\EquipmentType;

class SerialNumberMaskRule implements Rule
{
    protected $equipmentTypeId;

    public function __construct($equipmentTypeId)
    {
        $this->equipmentTypeId = $equipmentTypeId;
    }

    public function passes($attribute, $value)
    {
        $equipmentType = EquipmentType::find($this->equipmentTypeId);

        if (!$equipmentType) {
            return false; //  если тип оборудования не найден
        }

        $mask = $equipmentType->mask;

        if (strlen($value) != strlen($mask))
            return false;

        $regx = array(
            "N" => "[0-9]",
            "A" => "[A-Z]",
            "a" => "[a-z]",
            "X" => "[A-Z0-9]",
            "Z" => "[-|_|@]"
        );

        //Делаем список символов маски
        $maskChars = str_split($mask);

        //Формируем регулярное выражение для проверки
        $outputRegex = "/^";
        foreach ($maskChars as $char) {
            $outputRegex .= $regx[$char];
        }
        $outputRegex .= "/";

        return (preg_match($outputRegex, $value) > 0 ? true : false);
    }

    public function message()
    {
        return 'Серийный номер не соответствует маске указанного типа оборудования';
    }
}
