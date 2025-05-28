import{d as r,b as c,B as n,c as f,G as p,g as u,O as i,$ as h,a1 as N,n as y,a3 as _,Z as T,R as s}from"./index-ClQzGfh-.js";/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var g=r({name:"TLayout",setup:function(){var e=c(!1),a=i(),t=n("layout"),d=f(function(){return[t.value,p({},"".concat(t.value,"--with-sider"),e.value)]});return h("layout",{hasSide:e}),function(){return u("section",{class:d.value},[a("default")])}}});/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var S={height:{type:String,default:""}};/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var C=r({name:"THeader",props:S,setup:function(e){var a=n("layout__header"),t=i();return function(){return u("header",{class:a.value,style:e.height?{height:e.height}:{}},[t("default")])}}});/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var O={height:{type:String,default:""}};/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var m=r({name:"TFooter",props:O,setup:function(e){var a=n("layout__footer"),t=i();return function(){return u("footer",{class:a.value,style:e.height?{height:e.height}:{}},[t("default")])}}});/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var M={width:{type:String,default:""}};/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var E=r({name:"TAside",props:M,setup:function(e){var a=N("layout",Object.create(null)),t=a.hasSide,d=n("layout__sider"),l=i();if(t)return y(function(){t.value=!0}),_(function(){t.value=!1}),function(){var v=e.width?{width:e.width}:{};return u("aside",{class:d.value,style:v},[l("default")])}}});/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var A={content:{type:[String,Function]},default:{type:[String,Function]}};/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var P=r({name:"TContent",props:A,setup:function(){var e=n("layout__content"),a=T();return function(){return u("main",{class:e.value},[a("default","content")])}}});/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var J=s(E),X=s(g),F=s(C);s(m);var H=s(P);export{J as A,H as C,F as H,X as L};
