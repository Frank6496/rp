var getLocalization_m = function (culture) {
    var localization = null;
    switch (culture) {
        case "en":
			localization = {
				view:"View",
				details:"Details",
				actions:"Actions",
				filters:"Filters",
				choosevalue:"Choose Value",
				ok:"Ok",
				cancel:"Cancel",
				add:"Add",
				delete:"Delete",
				reload:"Reload",
				saveformstate:"Save Form State",
				hide:"hide/show"
				};
        break;
        case "ru":
         localization = {
				view:"Список",
				details:"Обзор",
				actions:"Операции",
				filters:"Фильтры",
				choosevalue:"Выберите значение",
				ok:"Ок",
				cancel:"Отмена",
				add:"Добавить",
				delete:"Удалить",
				reload:"Обновить",
				saveformstate:"Сохранить состояние формы",
				hide:"скрыть/показать"
				};
        break;
        }
   return localization;  
    }
