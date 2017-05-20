"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
var vue_1 = require("vue");
var root_1 = require("./components/root/root");
new vue_1.default({
    el: "#app",
    render: function (h) { return h(root_1.default); }
});
