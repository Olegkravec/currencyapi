export default class Model {
    constructor(data) {
        if(typeof data === "string")
            data = JSON.parse(data);

        let _defs = this.defaults();
        this.initObj(this,_defs)

        for (const [key, value] of Object.entries(data)) {
            // eslint-disable-next-line no-prototype-builtins
            if(_defs.hasOwnProperty(key))
                this[key] = value;

        }

        this.toString = function () {
            return JSON.stringify(this);
        }
    }
    defaults() {
        return {}
    }

    initObj(where, what){
        for (const [key, value] of Object.entries(what)) {
            where[key] = value;
        }
    }
}
