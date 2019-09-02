import {compact, concat, intersection, join, pullAll, uniq} from "lodash/array";
import {includes, map} from "lodash/collection";
import isArray from "lodash/isArray";
import mapValues from "lodash/mapValues";
import {split, trim} from "lodash/string";
import extRules from "../routing/extRules";

const ext_rules = map(extRules, "value");

export default data => {
    const rule_name = [
        "proxy",
        "direct",
        "block"
    ];
    data = mapValues(data, (value, index) => {
        if (includes(rule_name, index)) {
            if (isArray(value)) {
                value = uniq(value);
                const ext = intersection(value, ext_rules);
                const custom = join(pullAll(value, ext_rules), ", ");
                return {
                    custom: custom,
                    ext: ext
                }
            } else {
                const custom = map(split(value.custom, ","), trim);
                return uniq(concat(compact(custom), value.ext));
            }
        }
        return value;
    });
    return data;
}
