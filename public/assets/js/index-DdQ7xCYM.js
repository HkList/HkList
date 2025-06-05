import{d as x,B as k,a0 as p,z as _,c as N,G as i,Z as T,O as I,g as l,U as w,R as P}from"./index-ClWwn4SE.js";import{c as b}from"./useResizeObserver-btYw-Jj3.js";/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var F={content:{type:[String,Function]},default:{type:[String,Function]},disabled:{type:Boolean,default:void 0},download:{type:[String,Boolean]},hover:{type:String,default:"underline",validator:function(e){return e?["color","underline"].includes(e):!0}},href:{type:String,default:""},prefixIcon:{type:Function},size:{type:String,default:"medium",validator:function(e){return e?["small","medium","large"].includes(e):!0}},suffixIcon:{type:Function},target:{type:String,default:""},theme:{type:String,default:"default",validator:function(e){return e?["default","primary","danger","warning","success"].includes(e):!0}},underline:Boolean,onClick:Function};/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var z=x({name:"TLink",props:F,emits:["click"],setup:function(e,d){var f=d.emit,v=T(),u=I(),n=k("link"),o=p(),m=o.STATUS,g=o.SIZE,C=_("classPrefix"),S=C.classPrefix,a=b(),y=N(function(){return["".concat(n.value),"".concat(n.value,"--theme-").concat(e.theme),i(i(i(i({},g.value[e.size],e.size!=="medium"),m.value.disabled,a.value),"".concat(S.value,"-is-underline"),e.underline),"".concat(n.value,"--hover-").concat(e.hover),!a.value)]}),h=function(t){a.value||f("click",t)};return function(){var c=v("default","content"),t=u("prefixIcon"),s=u("suffixIcon");return l("a",{class:w(y.value),href:a.value||!e.href?void 0:e.href,target:e.target?e.target:void 0,download:e.download?e.download:void 0,onClick:h},[t?l("span",{class:"".concat(n.value,"__prefix-icon")},[t]):null,c,s?l("span",{class:"".concat(n.value,"__suffix-icon")},[s]):null])}}});/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var E=P(z);export{E as L};
