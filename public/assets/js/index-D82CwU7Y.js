import{H as M,v as D,y as I,d as z,a1 as j,B as A,z as J,c as P,g as a,F as h,a5 as x,a0 as V,b as X,G as E,O as Z,P as G,$ as K,R as H}from"./index-BlUydGuh.js";import{a as $,i as F}from"./index-DlzaH1y1.js";/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var k={bordered:Boolean,colon:Boolean,column:{type:Number,default:2},contentStyle:{type:Object},itemLayout:{type:String,default:"horizontal",validator:function(r){return r?["horizontal","vertical"].includes(r):!0}},items:{type:Array},labelStyle:{type:Object},layout:{type:String,default:"horizontal",validator:function(r){return r?["horizontal","vertical"].includes(r):!0}},size:{type:String,default:"medium",validator:function(r){return r?["small","medium","large"].includes(r):!0}},tableLayout:{type:String,default:"fixed",validator:function(r){return r?["fixed","auto"].includes(r):!0}},title:{type:[String,Function]}};/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var L=Symbol("TDescriptions");/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var C=function(e){return e.props="props",e.slots="slots",e}(C||{});function O(e){var r=arguments.length>1&&arguments[1]!==void 0?arguments[1]:{};return M(e)?e:D(e)?e(I,r):D(e==null?void 0:e.render)?e.render(I,r):e}function B(e,r,o){var d,m=(d=e.props)===null||d===void 0?void 0:d[r];if(m)return m;var v=e.children,p=(v==null?void 0:v[r])||(v==null?void 0:v[o]);return p?p==null?void 0:p():null}function _(e,r){return e===C.props}/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var q=z({name:"TDescriptionsRow",props:{row:Array,itemType:String},setup:function(r){var o=j(L),d=A("descriptions"),m=J("descriptions"),v=m.globalConfig,p=P(function(){return o.layout==="horizontal"}),f=P(function(){return o.itemLayout==="horizontal"}),b=function(n){var y=["".concat(d.value,"__label")],i=null,s=null;_(r.itemType)?(i=n.label,s=n.span):(i=B(n,"label"),s=n.props.span);var t=p.value?f.value?1:s:1;return a("td",x({colspan:t,class:y},{style:o.labelStyle}),[i,o.colon&&v.value.colonText])},g=function(n){var y=["".concat(d.value,"__content")],i=null,s=null;_(r.itemType)?(i=n.content,s=n.span):(i=B(n,"content","default"),s=n.props.span);var t=p.value?s>1&&f.value?s*2-1:s:1;return a("td",x({colspan:t,class:y},{style:o.contentStyle}),[i])},T=function(){return a("tr",null,[r.row.map(function(n){return a(h,null,[b(n),g(n)])})])},N=function(){return a(h,null,[a("tr",null,[r.row.map(function(n){return b(n)})]),a("tr",null,[r.row.map(function(n){return g(n)})])])},u=function(){return a(h,null,[r.row.map(function(n){return a("tr",null,[b(n),g(n)])})])},S=function(){return a(h,null,[r.row.map(function(n){return a(h,null,[a("tr",null,[b(n)]),a("tr",null,[g(n)])])})])};return function(){return a(h,null,[p.value?f.value?T():N():f.value?u():S()])}}});/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var Q=z({name:"TDescriptions",props:k,setup:function(r){var o=A("descriptions"),d=V(),m=d.SIZE,v=$(),p=Z(),f=X(C.props),b=function(){var u=r.column,S=r.layout,c=[];if(G(r.items))c=r.items.map(function(t){return{label:O(t.label),content:O(t.content),span:t.span||1}}),f.value=C.props;else{var n=v("TDescriptionsItem");n.length!==0&&(c=n,f.value=C.slots)}if(S==="vertical")return[c];var y=[],i=u,s=[];return c.forEach(function(t,R){var l=1;if(_(f.value))l=F(t.span)?l:t.span,l=l>u?u:l;else{var w;t.props=t.props||{},l=F((w=t.props)===null||w===void 0?void 0:w.span)?l:t.props.span,l=l>u?u:l,t.props.span=l}i>=l?(y.push(t),i-=l):(s.push(y),y=[t],i=u-l),R===c.length-1&&(_(f.value)?t.span+=i:t.props.span+=i,s.push(y))}),s};K(L,r);var g=function(){var u=["".concat(o.value,"__body"),m.value[r.size],E({},"".concat(o.value,"__body--fixed"),r.tableLayout==="fixed"),E({},"".concat(o.value,"__body--border"),r.bordered)];return a("table",{class:u},[a("tbody",null,[b().map(function(S){return a(q,{"item-type":f.value,row:S},null)})])])},T=function(){var u=p("title");return u?a("div",{class:"".concat(o.value,"__header")},[u]):""};return function(){return a("div",{class:o.value},[T(),g()])}}});/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var U={content:{type:[String,Function]},default:{type:[String,Function]},label:{type:[String,Function]},span:{type:Number,default:1}};/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var W=z({name:"TDescriptionsItem",props:U});/**
 * tdesign v1.11.5
 * (c) 2025 tdesign
 * @license MIT
 */var nr=H(Q),er=H(W);export{er as D,nr as a};
