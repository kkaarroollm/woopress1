"use strict";(self.webpackChunk_wcAdmin_webpackJsonp=self.webpackChunk_wcAdmin_webpackJsonp||[]).push([[7488],{43707:function(e,t,r){r.d(t,{Z:function(){return C}});var a=r(69307),n=r(65736),s=r(94333),i=r(69771),o=r(9818),l=r(92819),u=r(7862),c=r.n(u),d=r(86020),m=r(67221),p=r(81921),y=r(17844),g=r(5945),f=r(10431);function h(e,t){let r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{};if(!e||0===e.length)return null;const a=e.slice(0),n=a.pop();if(n.showFilters(t,r)){const e=(0,f.flattenFilters)(n.filters),r=t[n.param]||n.defaultValue||"all";return(0,l.find)(e,{value:r})}return h(a,t,r)}function b(e){return t=>(0,i.format)(e,t)}class v extends a.Component{shouldComponentUpdate(e){return e.isRequesting!==this.props.isRequesting||e.primaryData.isRequesting!==this.props.primaryData.isRequesting||e.secondaryData.isRequesting!==this.props.secondaryData.isRequesting||!(0,l.isEqual)(e.query,this.props.query)}getItemChartData(){const{primaryData:e,selectedChart:t}=this.props;return e.data.intervals.map((function(e){const r={};return e.subtotals.segments.forEach((function(e){if(e.segment_label){const a=r[e.segment_label]?e.segment_label+" (#"+e.segment_id+")":e.segment_label;r[e.segment_id]={label:a,value:e.subtotals[t.key]||0}}})),{date:(0,i.format)("Y-m-d\\TH:i:s",e.date_start),...r}}))}getTimeChartData(){const{query:e,primaryData:t,secondaryData:r,selectedChart:a,defaultDateRange:n}=this.props,s=(0,p.getIntervalForQuery)(e,n),{primary:o,secondary:l}=(0,p.getCurrentDates)(e,n);return t.data.intervals.map((function(t,n){const u=(0,p.getPreviousDate)(t.date_start,o.after,l.after,e.compare,s),c=r.data.intervals[n];return{date:(0,i.format)("Y-m-d\\TH:i:s",t.date_start),primary:{label:`${o.label} (${o.range})`,labelDate:t.date_start,value:t.subtotals[a.key]||0},secondary:{label:`${l.label} (${l.range})`,labelDate:u.format("YYYY-MM-DD HH:mm:ss"),value:c&&c.subtotals[a.key]||0}}}))}getTimeChartTotals(){const{primaryData:e,secondaryData:t,selectedChart:r}=this.props;return{primary:(0,l.get)(e,["data","totals",r.key],null),secondary:(0,l.get)(t,["data","totals",r.key],null)}}renderChart(e,t,r,s){const{emptySearchResults:i,filterParam:o,interactiveLegend:l,itemsLabel:u,legendPosition:c,path:y,query:g,selectedChart:f,showHeaderControls:h,primaryData:v,defaultDateRange:C}=this.props,R=(0,p.getIntervalForQuery)(g,C),D=(0,p.getAllowedIntervalsForQuery)(g,C),q=(0,p.getDateFormatsForInterval)(R,v.data.intervals.length,{type:"php"}),_=i?(0,n.__)("No data for the current search","woocommerce"):(0,n.__)("No data for the selected date range","woocommerce"),{formatAmount:E,getCurrencyConfig:T}=this.context;return(0,a.createElement)(d.Chart,{allowedIntervals:D,data:r,dateParser:"%Y-%m-%dT%H:%M:%S",emptyMessage:_,filterParam:o,interactiveLegend:l,interval:R,isRequesting:t,itemsLabel:u,legendPosition:c,legendTotals:s,mode:e,path:y,query:g,screenReaderFormat:b(q.screenReaderFormat),showHeaderControls:h,title:f.label,tooltipLabelFormat:b(q.tooltipLabelFormat),tooltipTitle:"time-comparison"===e&&f.label||null,tooltipValueFormat:(0,m.getTooltipValueFormat)(f.type,E),chartType:(0,p.getChartTypeForQuery)(g),valueType:f.type,xFormat:b(q.xFormat),x2Format:b(q.x2Format),currency:T()})}renderItemComparison(){const{isRequesting:e,primaryData:t}=this.props;if(t.isError)return(0,a.createElement)(g.Z,null);const r=e||t.isRequesting,n=this.getItemChartData();return this.renderChart("item-comparison",r,n)}renderTimeComparison(){const{isRequesting:e,primaryData:t,secondaryData:r}=this.props;if(!t||t.isError||r.isError)return(0,a.createElement)(g.Z,null);const n=e||t.isRequesting||r.isRequesting,s=this.getTimeChartData(),i=this.getTimeChartTotals();return this.renderChart("time-comparison",n,s,i)}render(){const{mode:e}=this.props;return"item-comparison"===e?this.renderItemComparison():this.renderTimeComparison()}}v.contextType=y.CurrencyContext,v.propTypes={filters:c().array,isRequesting:c().bool,itemsLabel:c().string,limitProperties:c().array,mode:c().string,path:c().string.isRequired,primaryData:c().object,query:c().object.isRequired,secondaryData:c().object,selectedChart:c().shape({key:c().string.isRequired,label:c().string.isRequired,order:c().oneOf(["asc","desc"]),orderby:c().string,type:c().oneOf(["average","number","currency"]).isRequired}).isRequired},v.defaultProps={isRequesting:!1,primaryData:{data:{intervals:[]},isError:!1,isRequesting:!1},secondaryData:{data:{intervals:[]},isError:!1,isRequesting:!1}};var C=(0,s.compose)((0,o.withSelect)(((e,t)=>{const{charts:r,endpoint:a,filters:n,isRequesting:s,limitProperties:i,query:o,advancedFilters:u}=t,c=i||[a],d=h(n,o),p=(0,l.get)(d,["settings","param"]),y=t.mode||function(e,t){if(e&&t){const r=(0,l.get)(e,["settings","param"]);if(!r||Object.keys(t).includes(r))return(0,l.get)(e,["chartMode"])}return null}(d,o)||"time-comparison",{woocommerce_default_date_range:g}=e(m.SETTINGS_STORE_NAME).getSetting("wc_admin","wcAdminSettings"),f=e(m.REPORTS_STORE_NAME),b={mode:y,filterParam:p,defaultDateRange:g};if(s)return b;const v=c.some((e=>o[e]&&o[e].length));if(o.search&&!v)return{...b,emptySearchResults:!0};const C=r&&r.map((e=>e.key)),R=(0,m.getReportChartData)({endpoint:a,dataType:"primary",query:o,selector:f,limitBy:c,filters:n,advancedFilters:u,defaultDateRange:g,fields:C});if("item-comparison"===y)return{...b,primaryData:R};const D=(0,m.getReportChartData)({endpoint:a,dataType:"secondary",query:o,selector:f,limitBy:c,filters:n,advancedFilters:u,defaultDateRange:g,fields:C});return{...b,primaryData:R,secondaryData:D}})))(v)},50933:function(e,t,r){var a=r(69307),n=r(65736),s=r(94333),i=r(9818),o=r(7862),l=r.n(o),u=r(10431),c=r(86020),d=r(81595),m=r(67221),p=r(81921),y=r(14599),g=r(17844),f=r(5945);class h extends a.Component{formatVal(e,t){const{formatAmount:r,getCurrencyConfig:a}=this.context;return"currency"===t?r(e):(0,d.formatValue)(a(),t,e)}getValues(e,t){const{emptySearchResults:r,summaryData:a}=this.props,{totals:n}=a,s=n.primary?n.primary[e]:0,i=n.secondary?n.secondary[e]:0,o=r?0:s,l=r?0:i;return{delta:(0,d.calculateDelta)(o,l),prevValue:this.formatVal(l,t),value:this.formatVal(o,t)}}render(){const{charts:e,query:t,selectedChart:r,summaryData:s,endpoint:i,report:o,defaultDateRange:l}=this.props,{isError:d,isRequesting:m}=s;if(d)return(0,a.createElement)(f.Z,null);if(m)return(0,a.createElement)(c.SummaryListPlaceholder,{numberOfItems:e.length});const{compare:g}=(0,p.getDateParamsFromQuery)(t,l);return(0,a.createElement)(c.SummaryList,null,(t=>{let{onToggle:s}=t;return e.map((e=>{const{key:t,order:l,orderby:d,label:m,type:p,isReverseTrend:f,labelTooltipText:h}=e,b={chart:t};d&&(b.orderby=d),l&&(b.order=l);const v=(0,u.getNewPath)(b),C=r.key===t,{delta:R,prevValue:D,value:q}=this.getValues(t,p);return(0,a.createElement)(c.SummaryNumber,{key:t,delta:R,href:v,label:m,reverseTrend:f,prevLabel:"previous_period"===g?(0,n.__)("Previous period:","woocommerce"):(0,n.__)("Previous year:","woocommerce"),prevValue:D,selected:C,value:q,labelTooltipText:h,onLinkClickCallback:()=>{s&&s(),(0,y.recordEvent)("analytics_chart_tab_click",{report:o||i,key:t})}})}))}))}}h.propTypes={charts:l().array.isRequired,endpoint:l().string.isRequired,limitProperties:l().array,query:l().object.isRequired,selectedChart:l().shape({key:l().string.isRequired,label:l().string.isRequired,order:l().oneOf(["asc","desc"]),orderby:l().string,type:l().oneOf(["average","number","currency"]).isRequired}).isRequired,summaryData:l().object,report:l().string},h.defaultProps={summaryData:{totals:{primary:{},secondary:{}},isError:!1}},h.contextType=g.CurrencyContext,t.Z=(0,s.compose)((0,i.withSelect)(((e,t)=>{const{charts:r,endpoint:a,limitProperties:n,query:s,filters:i,advancedFilters:o}=t,l=n||[a],u=l.some((e=>s[e]&&s[e].length));if(s.search&&!u)return{emptySearchResults:!0};const c=r&&r.map((e=>e.key)),{woocommerce_default_date_range:d}=e(m.SETTINGS_STORE_NAME).getSetting("wc_admin","wcAdminSettings");return{summaryData:(0,m.getSummaryNumbers)({endpoint:a,query:s,select:e,limitBy:l,filters:i,advancedFilters:o,defaultDateRange:d,fields:c}),defaultDateRange:d}})))(h)},59714:function(e,t,r){function a(e,t,r){return!!t&&e&&t<=r==="instock"}r.d(t,{d:function(){return a}})},69629:function(e,t,r){r.d(t,{I:function(){return n}});var a=r(65736);function n(e){return[e.country,e.state,e.name||(0,a.__)("TAX","woocommerce"),e.priority].map((e=>e.toString().toUpperCase().trim())).filter(Boolean).join("-")}},68734:function(e,t,r){r.d(t,{FI:function(){return f},V1:function(){return h},YC:function(){return m},hQ:function(){return p},jk:function(){return y},oC:function(){return g},qc:function(){return d},uC:function(){return b}});var a=r(96483),n=r(86989),s=r.n(n),i=r(92819),o=r(10431),l=r(67221),u=r(69629),c=r(79205);function d(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:i.identity;return function(){let r=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"",n=arguments.length>1?arguments[1]:void 0;const i="function"==typeof e?e(n):e,l=(0,o.getIdsFromQuery)(r);if(l.length<1)return Promise.resolve([]);const u={include:l.join(","),per_page:l.length};return s()({path:(0,a.addQueryArgs)(i,u)}).then((e=>e.map(t)))}}d(l.NAMESPACE+"/products/attributes",(e=>({key:e.id,label:e.name})));const m=d(l.NAMESPACE+"/products/categories",(e=>({key:e.id,label:e.name}))),p=d(l.NAMESPACE+"/coupons",(e=>({key:e.id,label:e.code}))),y=d(l.NAMESPACE+"/customers",(e=>({key:e.id,label:e.name}))),g=d(l.NAMESPACE+"/products",(e=>({key:e.id,label:e.name}))),f=d(l.NAMESPACE+"/taxes",(e=>({key:e.id,label:(0,u.I)(e)})));function h(e){let{attributes:t,name:r}=e;const a=(0,c.O3)("variationTitleAttributesSeparator"," - ");if(r&&r.indexOf(a)>-1)return r;const n=(t||[]).map((e=>{let{option:t}=e;return t})).join(", ");return n?r+a+n:r}const b=d((e=>{let{products:t}=e;return t?l.NAMESPACE+`/products/${t}/variations`:l.NAMESPACE+"/variations"}),(e=>({key:e.id,label:h(e)})))},62409:function(e,t,r){r.d(t,{Z:function(){return n}});var a=r(92819);function n(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:[];const r=(0,a.find)(t,{key:e});return r||t[0]}}}]);