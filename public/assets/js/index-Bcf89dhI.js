import{d as F,B as k,a0 as I,W as O,X as R,c as t,G as i,w as x,v as S,y as C,H as y,P as U,g as c,au as Z,R as D}from"./index-ClWwn4SE.js";import{c as G,u as H}from"./useResizeObserver-btYw-Jj3.js";/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var J={beforeChange:{type:Function},customValue:{type:Array},disabled:{type:Boolean,default:void 0},label:{type:[Array,Function],default:function(){return[]}},loading:Boolean,size:{type:String,default:"medium",validator:function(e){return e?["small","medium","large"].includes(e):!0}},value:{type:[String,Number,Boolean],default:void 0},modelValue:{type:[String,Number,Boolean],default:void 0},defaultValue:{type:[String,Number,Boolean]},onChange:Function};/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var L=F({name:"TSwitch",props:J,setup:function(e,w){var d=w.slots,o=G(),s=k("switch"),f=I(),u=f.STATUS,m=f.SIZE,g=O(e),_=g.value,N=g.modelValue,T=H(_,N,e.defaultValue,e.onChange),h=R(T,2),n=h[0],A=h[1],r=t(function(){return e.customValue&&e.customValue.length>0?e.customValue[0]:!0}),B=t(function(){return e.customValue&&e.customValue.length>1?e.customValue[1]:!1});function V(a){var l=n.value===r.value?B.value:r.value;A(l,{e:a})}function E(a){if(!(o.value||e.loading)){if(!e.beforeChange){V(a);return}Promise.resolve(e.beforeChange()).then(function(l){l&&V(a)}).catch(function(l){throw new Error("Switch: some error occurred: ".concat(l))})}}var M=t(function(){return["".concat(s.value),m.value[e.size],i(i(i({},u.value.disabled,o.value),u.value.loading,e.loading),u.value.checked,n.value===r.value||e.modelValue===r.value)]}),P=t(function(){return["".concat(s.value,"__handle"),i(i({},u.value.disabled,o.value),u.value.loading,e.loading)]}),z=t(function(){return["".concat(s.value,"__content"),m.value[e.size],i({},u.value.disabled,o.value)]});x(n,function(a){if(e.customValue&&e.customValue.length&&!e.customValue.includes(a))throw new Error("value is not in ".concat(JSON.stringify(e.customValue)))},{immediate:!0});var b=t(function(){if(S(e.label))return e.label(C,{value:n.value});if(y(e.label))return e.label;if(U(e.label)&&e.label.length){var a=n.value===r.value?e.label[0]:e.label[1];if(!a)return;if(y(a))return a;if(S(a))return a(C)}return d.label?d.label({value:n.value}):null});return function(){var a,l;return e.loading?l=c(Z,{size:"small"},null):b.value&&(a=b.value),c("div",{class:M.value,onClick:E},[c("span",{class:P.value},[l]),c("div",{class:z.value},[a])])}}});/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var j=D(L);export{j as S};
