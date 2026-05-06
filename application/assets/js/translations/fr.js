let DictionnaryFrObject = function (options) {
    var dictionnary = {
        "crédits restants": "crédits restants",
        "crédit restant": "crédit restant",
        "Illimité": "Illimité",
        "Limité à 5 objets": "Limité à 5 objets",
        "limité": "limité",
        "Oui": "Oui",
        "Non": "Non"
    };

    this.getDictionnary = function () {
        return dictionnary;
    };
};

let dictionnaryFrObject = new DictionnaryFrObject();

export {dictionnaryFrObject};