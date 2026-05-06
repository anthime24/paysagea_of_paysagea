import {dictionnaryFrObject} from './fr';
import {dictionnaryEnObject} from './en';

let TransObject = function (options) {
    var lang = '';
    var dictionnary = null;

    this.getLang = function () {
        return lang;
    };

    this.setLang = function (dataLang) {
        lang = dataLang;
    };

    this.getDictionnary = function () {
        return dictionnary;
    };

    this.setDictionnary = function (dataDictionnary) {
        dictionnary = dataDictionnary;
    };

    this.refreshLang = function () {
        if (this.getDictionnary() == null) {
            this.setLang(jQuery('html').attr('lang').substr(0, 2));
            if (this.getLang() == 'en')
                this.setDictionnary(dictionnaryEnObject.getDictionnary());
            else
                this.setDictionnary(dictionnaryFrObject.getDictionnary());
        }
    };

    this.trans = function (string, options) {
        this.refreshLang();
        let dictionnary = this.getDictionnary();

        return (typeof dictionnary[string] == 'undefined' ? string : dictionnary[string]);
    }
};

let transObject = new TransObject();

export {transObject};