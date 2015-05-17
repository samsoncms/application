<?php
namespace samsoncms;

use samsonphp\i18n\IDictionary;

class Dictionary implements IDictionary
{
    public function getDictionary()
    {
        return array(
            "en" => array(

                "В данный момент данных нет" => "Currently there is no data",
                "По Вашему запросу ничего не найдено" => "Your search did not match",
            ),
            "ua" => array(
                "В данный момент данных нет" => "На даний момент даних немає",
                "По Вашему запросу ничего не найдено" => "За Вашим запитом даних не знайдено",
            ),
        );
    }
}
