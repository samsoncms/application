<?php
namespace samsoncms\i18n;

use samsonphp\i18n\IDictionary;

class Dictionary implements IDictionary
{
    public function getDictionary()
    {
        return array(
            "en" => array(
                "В данный момент данных нет" => "Currently there is no data",
                "По Вашему запросу ничего не найдено" => "Your search did not match",
                "Выход" => "Exit",
                "На сайт" => "Live",
                "Форма редактирования сущности" => "Edit entity",
                "Теги структуры" => "Structure tags",
                "Добавить" => "Add field",
                "Загрузка формы" => "Loading the form",
                "Список полей для" => "Fields of",
                "Общие" => "Common",
                "ФИО" => "Full name",
                'Назад к списку' => 'Back to the list',
                "Вы ввели некорректное значение" => "You entered an incorrect value",
                "Поле с таким именем уже существует" => "A field with the same name already exists",
                "Отобразить в виде таблицы" => "Make a table view",
                "Отобразить в виде блоков" => "Make a tile view"
            ),
            "ua" => array(
                "В данный момент данных нет" => "На даний момент даних немає",
                "По Вашему запросу ничего не найдено" => "За Вашим запитом даних не знайдено",
                "Выход" => "Вихід",
                "На сайт" => "На сайт",
                "Форма редактирования сущности" => "Форма редагування",
                "Теги структуры" => "Теги структури",
                "Добавить" => "Додати",
                "Загрузка формы" => "Завантаження форми",
                "Список полей для" => "Список полів для",
                "Общие" => "Загальні",
                "ФИО" => "ПІБ",
                'Назад к списку' => 'Повернутись до списку',
                "Вы ввели некорректное значение" => "Ви ввели некоректне значення",
                "Поле с таким именем уже существует" => "Поле з таким іменем існує",
                "Отобразить в виде таблицы" => "Відобразити як таблицю",
                "Отобразить в виде блоков" => "Відобразити як блоки"
            ),
            "de" => array(
                "В данный момент данных нет" => "Derzeit liegen keine Daten",
                "По Вашему запросу ничего не найдено" => "Ihre Suche brachte nicht überein",
                "Выход" => "Ausfahrt",
                "На сайт" => "Zuhause",
                "Форма редактирования сущности" => "Wir bilden das Wesen der Bearbeitung",
                "Теги структуры" => "Stichworte Strukturen",
                "Добавить" => "Hinzufügen",
                "Загрузка формы" => "Download-Formular",
                "Список полей для" => "Liste der golf",
                "Общие" => "Gemeinsam",
                "ФИО" => "Vollständiger Name",
                'Назад к списку' => 'Zurück zur Liste',
                "Вы ввели некорректное значение" => "Sie haben einen falschen Wert",
                "Поле с таким именем уже существует" => "Ein Feld mit diesem Namen existiert bereits",
                "Отобразить в виде таблицы" => "Zum Anzeigen einer Tabelle",
                "Отобразить в виде блоков" => "Eine Anzeigeeinheit"
            ),
        );
    }
}
