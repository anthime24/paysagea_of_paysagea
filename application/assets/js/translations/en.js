let DictionnaryEnObject = function (options) {
    var dictionnary = {
        "crédits restants": "remaining credits",
        "crédit restant": "remaining credit",
        "Illimité": "Illimited",
        "Limité à 5 objets": "Limited to 5 items",
        "limité": "limited",
        "Oui": "Yes",
        "Non": "No"
    };

    this.getDictionnary = function () {
        return dictionnary;
    };
};

let dictionnaryEnObject = new DictionnaryEnObject();

export {dictionnaryEnObject};
